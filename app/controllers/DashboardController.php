<?php

namespace app\controllers;

use app\models\VilleModel;
use app\models\BesoinModel;
use app\models\DonModel;
use app\models\DonateurModel;
use app\utils\DataConverter;

class DashboardController extends BaseController
{
    public function index(): void
    {
        // Statistiques sur les besoins
        $totalBesoinsData = $this->db()->fetchRow("
            SELECT 
                COUNT(*) AS nb_besoins,
                SUM(bv.quantite_demandee * tb.prix_unitaire) AS valeur_besoins,
                COALESCE(SUM(bv.quantite_recue * tb.prix_unitaire), 0) AS valeur_couverte
            FROM besoin_ville bv
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
        ");

        // Convertir en array si c'est une Collection
        $totalBesoins = DataConverter::toArray($totalBesoinsData);

        // Statistiques sur les dons et distributions
        $statsGlobauxData = $this->db()->fetchRow("
            SELECT 
                COUNT(d.id_don) AS nb_dons,
                COALESCE(SUM(d.quantite * tb.prix_unitaire), 0) AS valeur_totale,
                COALESCE(SUM(dist.quantite_attribuee * tb.prix_unitaire), 0) AS valeur_distribuee
            FROM don d
            JOIN type_besoin tb ON d.id_type_besoin = tb.id_type_besoin
            LEFT JOIN distribution dist ON d.id_don = dist.id_don
        ");

        // Convertir en array si c'est une Collection
        $statsGlobaux = DataConverter::toArray($statsGlobauxData);

        // Statistiques par ville
        $dashboardVillesData = $this->db()->fetchAll("
            SELECT 
                v.id_ville,
                v.nom_ville,
                v.region,
                COUNT(bv.id_besoin) AS nb_besoins,
                COALESCE(SUM(bv.quantite_demandee * tb.prix_unitaire), 0) AS valeur_besoins,
                COALESCE(SUM(bv.quantite_recue * tb.prix_unitaire), 0) AS valeur_recue
            FROM ville v
            LEFT JOIN besoin_ville bv ON v.id_ville = bv.id_ville
            LEFT JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            GROUP BY v.id_ville, v.nom_ville, v.region
            ORDER BY v.nom_ville
        ");

        $dashboardVilles = DataConverter::toArray($dashboardVillesData);

        $this->render('dashboard/index', [
            'totalBesoins' => $totalBesoins,
            'statsGlobaux' => $statsGlobaux,
            'dashboardVilles' => $dashboardVilles,
        ], 'Dashboard - BNGRC');
    }

    /**
     * Page de récapitulation
     */
    public function recapitulation(): void
    {
        $this->render('dashboard/recapitulation', [], 'Récapitulation - BNGRC');
    }

    /**
     * API pour récupérer les données de récapitulation (AJAX)
     */
    public function getRecapData(): void
    {
        // Statistiques globales
        $statsData = $this->db()->fetchRow("
            SELECT 
                COUNT(DISTINCT v.id_ville) AS nb_villes,
                COUNT(DISTINCT bv.id_besoin) AS nb_besoins_total,
                COUNT(DISTINCT CASE WHEN bv.quantite_recue >= bv.quantite_demandee THEN bv.id_besoin END) AS nb_besoins_satisfaits,
                SUM(bv.quantite_demandee * tb.prix_unitaire) AS valeur_totale_besoins,
                SUM(bv.quantite_recue * tb.prix_unitaire) AS valeur_totale_recue,
                SUM((bv.quantite_demandee - bv.quantite_recue) * tb.prix_unitaire) AS valeur_totale_manquante
            FROM besoin_ville bv
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            JOIN ville v ON bv.id_ville = v.id_ville
        ");
        $stats = DataConverter::toArray($statsData);

        // Besoins par ville
        $besoinsParVilleData = $this->db()->fetchAll("
            SELECT 
                v.id_ville,
                v.nom_ville,
                COUNT(bv.id_besoin) AS nb_besoins,
                SUM(bv.quantite_demandee * tb.prix_unitaire) AS montant_total,
                SUM(bv.quantite_recue * tb.prix_unitaire) AS montant_satisfait,
                SUM((bv.quantite_demandee - bv.quantite_recue) * tb.prix_unitaire) AS montant_restant,
                ROUND(100 * SUM(bv.quantite_recue * tb.prix_unitaire) / NULLIF(SUM(bv.quantite_demandee * tb.prix_unitaire), 0), 2) AS pourcentage_satisfait
            FROM ville v
            LEFT JOIN besoin_ville bv ON v.id_ville = bv.id_ville
            LEFT JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            GROUP BY v.id_ville, v.nom_ville
            ORDER BY v.nom_ville
        ");
        $besoinsParVille = DataConverter::toArray($besoinsParVilleData);

        // Détails des besoins restants
        $besoinsRestantsData = $this->db()->fetchAll("
            SELECT 
                v.nom_ville,
                cb.nom_categorie,
                tb.nom AS type_nom,
                tb.unite,
                bv.quantite_demandee,
                bv.quantite_recue,
                (bv.quantite_demandee - bv.quantite_recue) AS quantite_manquante,
                tb.prix_unitaire,
                ((bv.quantite_demandee - bv.quantite_recue) * tb.prix_unitaire) AS valeur_manquante
            FROM besoin_ville bv
            JOIN ville v ON bv.id_ville = v.id_ville
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            WHERE bv.quantite_recue < bv.quantite_demandee
            ORDER BY v.nom_ville, cb.nom_categorie, tb.nom
        ");
        $besoinsRestants = DataConverter::toArray($besoinsRestantsData);

        // Statistiques des dons
        $statsDonsData = $this->db()->fetchRow("
            SELECT 
                COUNT(*) AS nb_dons_total,
                SUM(quantite) AS montant_total_dons,
                SUM(CASE WHEN tb.id_categorie = 1 THEN quantite * prix_unitaire ELSE 0 END) AS valeur_nature,
                SUM(CASE WHEN tb.id_categorie = 2 THEN quantite * prix_unitaire ELSE 0 END) AS valeur_materiaux,
                SUM(CASE WHEN tb.id_categorie = 3 THEN quantite ELSE 0 END) AS montant_argent
            FROM don d
            JOIN type_besoin tb ON d.id_type_besoin = tb.id_type_besoin
        ");
        $statsDons = DataConverter::toArray($statsDonsData);

        $this->app->json([
            'stats' => $stats,
            'besoins_par_ville' => $besoinsParVille,
            'besoins_restants' => $besoinsRestants,
            'stats_dons' => $statsDons,
            'timestamp' => time(),
        ]);
    }
}
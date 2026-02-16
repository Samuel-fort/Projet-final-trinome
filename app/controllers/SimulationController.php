<?php

namespace app\controllers;

use app\models\SimulationModel;
use app\models\ConfigModel;
use app\models\TypeBesoinModel;
use app\models\VilleModel;
use app\models\BesoinModel;

class SimulationController extends BaseController
{
    private SimulationModel $model;

    private function getModel(): SimulationModel
    {
        if (!isset($this->model)) {
            $this->model = new SimulationModel($this->db());
        }
        return $this->model;
    }

    /**
     * Page principale de simulation
     */
    public function index(): void
    {
        $donsArgent = $this->getDonsArgentDisponibles();
        $villes = (new VilleModel($this->db()))->findAll();
        $fraisPourcentage = (new ConfigModel($this->db()))->getFraisAchat();

        $this->render('simulation/index', [
            'donsArgent' => $donsArgent,
            'villes' => $villes,
            'fraisPourcentage' => $fraisPourcentage,
        ], 'Simulation d\'achats - BNGRC');
    }

    /**
     * Récupère les dons en argent disponibles
     */
    private function getDonsArgentDisponibles(): array
    {
        return $this->db()->fetchAll("
            SELECT dn.id_don, dn.quantite AS montant_total,
                   CONCAT(COALESCE(d.prenom,''), ' ', COALESCE(d.nom,'')) AS donateur_nom,
                   COALESCE((SELECT SUM(montant_total) FROM achat WHERE id_don_argent = dn.id_don), 0) AS depense,
                   (dn.quantite - COALESCE((SELECT SUM(montant_total) FROM achat WHERE id_don_argent = dn.id_don), 0)) AS disponible
            FROM don dn
            LEFT JOIN donateur d ON dn.id_donateur = d.id_donateur
            WHERE dn.id_type_besoin = 11
            HAVING disponible > 0
            ORDER BY dn.date_saisie ASC
        ");
    }

    /**
     * Récupère les besoins non satisfaits d'une ville (AJAX)
     */
    public function getBesoinsVille(): void
    {
        $request = $this->app->request();
        $idVille = (int)($request->query->id_ville ?? 0);

        if (!$idVille) {
            $this->app->json(['error' => 'ID ville manquant']);
            return;
        }

        $besoins = $this->db()->fetchAll("
            SELECT bv.*, tb.nom AS type_nom, tb.unite, tb.prix_unitaire, tb.id_type_besoin,
                   (bv.quantite_demandee - bv.quantite_recue) AS quantite_manquante,
                   ((bv.quantite_demandee - bv.quantite_recue) * tb.prix_unitaire) AS valeur_manquante
            FROM besoin_ville bv
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            WHERE bv.id_ville = ? 
              AND bv.quantite_recue < bv.quantite_demandee
              AND tb.id_categorie IN (1, 2)
            ORDER BY tb.nom
        ", [$idVille]);

        $this->app->json($besoins);
    }

    /**
     * Simule des achats (AJAX)
     */
    public function simuler(): void
    {
        $request = $this->app->request();
        $idDonArgent = (int)($request->data->id_don_argent ?? 0);
        $achats = $request->data->achats ?? [];

        if (!$idDonArgent || empty($achats)) {
            $this->app->json(['error' => 'Données manquantes']);
            return;
        }

        try {
            $configModel = new ConfigModel($this->db());
            $fraisPourcentage = $configModel->getFraisAchat();

            // Vérifier le budget disponible
            $budgetDisponible = $this->db()->fetchRow("
                SELECT dn.quantite AS montant_total,
                       COALESCE((SELECT SUM(montant_total) FROM achat WHERE id_don_argent = ?), 0) AS depense,
                       (dn.quantite - COALESCE((SELECT SUM(montant_total) FROM achat WHERE id_don_argent = ?), 0)) AS disponible
                FROM don dn
                WHERE dn.id_don = ?
            ", [$idDonArgent, $idDonArgent, $idDonArgent]);

            if (!$budgetDisponible) {
                $this->app->json(['error' => 'Don introuvable']);
                return;
            }

            $budget = (float)$budgetDisponible['disponible'];
            $achatsDetails = [];
            $montantTotal = 0;

            foreach ($achats as $achat) {
                $idTypeBesoin = (int)($achat['id_type_besoin'] ?? 0);
                $quantite = (float)($achat['quantite'] ?? 0);

                if (!$idTypeBesoin || $quantite <= 0) continue;

                // Récupérer le prix
                $type = $this->db()->fetchRow("
                    SELECT prix_unitaire, nom, unite FROM type_besoin WHERE id_type_besoin = ?
                ", [$idTypeBesoin]);

                if (!$type) continue;

                $prixUnitaire = (float)$type['prix_unitaire'];
                $montantBase = $quantite * $prixUnitaire;
                $montantFrais = $montantBase * ($fraisPourcentage / 100);
                $montantLigne = $montantBase + $montantFrais;

                $achatsDetails[] = [
                    'id_type_besoin' => $idTypeBesoin,
                    'nom' => $type['nom'],
                    'unite' => $type['unite'],
                    'quantite' => $quantite,
                    'prix_unitaire' => $prixUnitaire,
                    'montant_base' => $montantBase,
                    'montant_frais' => $montantFrais,
                    'montant_total' => $montantLigne,
                ];

                $montantTotal += $montantLigne;
            }

            // Créer la simulation
            $details = [
                'achats' => $achatsDetails,
                'frais_pourcentage' => $fraisPourcentage,
            ];

            $idSimulation = $this->getModel()->creerSimulation($idDonArgent, $details, $montantTotal);

            $this->app->json([
                'success' => true,
                'id_simulation' => $idSimulation,
                'achats' => $achatsDetails,
                'montant_total' => $montantTotal,
                'budget_disponible' => $budget,
                'budget_restant' => $budget - $montantTotal,
                'budget_suffisant' => $montantTotal <= $budget,
            ]);

        } catch (\Exception $e) {
            $this->app->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Valide une simulation
     */
    public function valider(): void
    {
        $request = $this->app->request();
        $idSimulation = (int)($request->data->id_simulation ?? 0);

        if (!$idSimulation) {
            $this->redirect('/simulation?error=simulation_manquante');
            return;
        }

        try {
            $success = $this->getModel()->validerSimulation($idSimulation);
            
            if ($success) {
                $this->redirect('/achats?success=simulation_validee');
            } else {
                $this->redirect('/simulation?error=validation_impossible');
            }
        } catch (\Exception $e) {
            $this->redirect('/simulation?error=' . urlencode($e->getMessage()));
        }
    }
}
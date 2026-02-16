<?php

namespace app\controllers;

use app\models\VilleModel;
use app\models\DonModel;
use app\models\BesoinModel;

class DashboardController extends BaseController
{
    public function index(): void
    {
        $db = $this->db();
        $villeModel = new VilleModel($db);
        $donModel = new DonModel($db);

        $dashboardVilles = $villeModel->getDashboardData();
        $statsGlobaux = $donModel->getStats();

        // Totaux globaux besoins
        $totalBesoins = $db->fetchRow("
            SELECT
                COALESCE(SUM(bv.quantite_demandee * tb.prix_unitaire), 0) AS valeur_besoins,
                COALESCE(SUM(bv.quantite_recue * tb.prix_unitaire), 0) AS valeur_couverte,
                COUNT(*) AS nb_besoins
            FROM besoin_ville bv
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
        ");
        $totalBesoins = $totalBesoins ? json_decode(json_encode($totalBesoins), true) : ['valeur_besoins' => 0, 'valeur_couverte' => 0, 'nb_besoins' => 0];

        $this->render('dashboard/index', [
            'dashboardVilles' => $dashboardVilles,
            'statsGlobaux'    => $statsGlobaux,
            'totalBesoins'    => $totalBesoins,
        ], 'Tableau de bord - BNGRC');
    }
}
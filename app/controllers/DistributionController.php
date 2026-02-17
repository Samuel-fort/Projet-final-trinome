<?php

namespace app\controllers;

use app\models\DistributionModel;
use app\models\VilleModel;
use app\models\BesoinModel;

class DistributionController extends BaseController
{
    private DistributionModel $model;

    private function getModel(): DistributionModel
    {
        if (!isset($this->model)) {
            $this->model = new DistributionModel($this->db());
        }
        return $this->model;
    }

    public function index(): void
    {
        $distributions = $this->getModel()->findAll();
        $donsDisponibles = $this->getModel()->getDonsDisponibles();
        $villes = (new VilleModel($this->db()))->findAll();

        $this->render('distribution/liste', [
            'distributions'  => $distributions,
            'donsDisponibles'=> $donsDisponibles,
            'villes'         => $villes,
        ], 'Distributions - BNGRC');
    }

    /** AJAX ou POST : retourne les besoins d'une ville pour un type donné */
    public function getBesoins(): void
    {
        $request    = $this->app->request();
        $idVille    = (int)($request->query->id_ville ?? 0);
        $idType     = (int)($request->query->id_type_besoin ?? 0);

        $besoins = $this->getModel()->getBesoinsOuverts($idVille, $idType);
        $this->app->json($besoins);
    }

    public function store(): void
    {
        $request    = $this->app->request();
        $idDon      = (int)($request->data->id_don ?? 0);
        $idVille    = (int)($request->data->id_ville ?? 0);
        $idBesoin   = (int)($request->data->id_besoin ?? 0);
        $quantite   = (float)($request->data->quantite_attribuee ?? 0);

        if (!$idDon || !$idVille || !$idBesoin || $quantite <= 0) {
            $this->redirect('/distributions?error=champs');
            return;
        }

        try {
            $this->getModel()->distribuer($idDon, $idVille, $idBesoin, $quantite);
            $this->redirect('/distributions?success=1');
        } catch (\Exception $e) {
            $this->redirect('/distributions?error=' . urlencode($e->getMessage()));
        }
    }

    public function delete(int $id): void
    {
        $this->getModel()->delete($id);
        $this->redirect('/distributions?deleted=1');
    }

    /**
     * ============================================================================
     * NOUVELLES MÉTHODES POUR LA DISTRIBUTION AUTOMATIQUE (V3)
     * ============================================================================
     */

    /**
     * Simule une distribution automatique (AJAX)
     */
    public function simulerAuto(): void
    {
        $request = $this->app->request();
        $idDon = (int)($request->data->id_don ?? 0);
        $mode = $request->data->mode ?? '';

        if (!$idDon || !in_array($mode, ['anciennete', 'demande_min', 'proportionnalite'])) {
            $this->app->json([
                'success' => false,
                'error' => 'Paramètres invalides',
            ]);
            return;
        }

        try {
            $resultat = $this->getModel()->simulerDistributionAuto($idDon, $mode);
            $this->app->json($resultat);
        } catch (\Exception $e) {
            $this->app->json([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Valide une simulation et enregistre les distributions
     */
    public function validerSimulation(): void
    {
        $request = $this->app->request();
        $idDon = (int)($request->data->id_don ?? 0);
        $distributions = $request->data->distributions ?? [];

        if (!$idDon || empty($distributions)) {
            $this->redirect('/distributions?error=simulation_invalide');
            return;
        }

        try {
            $this->getModel()->validerSimulation($idDon, $distributions);
            $this->redirect('/distributions?success=simulation_validee');
        } catch (\Exception $e) {
            $this->redirect('/distributions?error=' . urlencode($e->getMessage()));
        }
    }
}
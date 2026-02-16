<?php

namespace app\controllers;

use app\models\BesoinModel;
use app\models\VilleModel;
use app\models\TypeBesoinModel;

class BesoinController extends BaseController
{
    private BesoinModel $model;

    private function getModel(): BesoinModel
    {
        if (!isset($this->model)) {
            $this->model = new BesoinModel($this->db());
        }
        return $this->model;
    }

    public function index(): void
    {
        $besoins = $this->getModel()->findAll();
        $this->render('besoin/liste', ['besoins' => $besoins], 'Besoins - BNGRC');
    }

    public function create(): void
    {
        $db = $this->db();
        $villes = (new VilleModel($db))->findAll();
        $types  = (new TypeBesoinModel($db))->findAllWithCategorie();
        $this->render('besoin/formulaire', [
            'besoin' => null, 'mode' => 'create',
            'villes' => $villes, 'types' => $types
        ], 'Nouveau besoin - BNGRC');
    }

    public function store(): void
    {
        $request = $this->app->request();
        $idVille      = (int)($request->data->id_ville ?? 0);
        $idType       = (int)($request->data->id_type_besoin ?? 0);
        $quantite     = (float)($request->data->quantite_demandee ?? 0);

        if (!$idVille || !$idType || $quantite <= 0) {
            $db = $this->db();
            $this->render('besoin/formulaire', [
                'besoin' => null, 'mode' => 'create',
                'villes' => (new VilleModel($db))->findAll(),
                'types'  => (new TypeBesoinModel($db))->findAllWithCategorie(),
                'flash_error' => 'Tous les champs sont obligatoires et la quantité doit être > 0.'
            ], 'Nouveau besoin');
            return;
        }

        $this->getModel()->create($idVille, $idType, $quantite);
        $this->redirect('/besoins?success=1');
    }

    public function edit(int $id): void
    {
        $besoin = $this->getModel()->findById($id);
        if (!$besoin) { $this->redirect('/besoins'); return; }
        $db = $this->db();
        $this->render('besoin/formulaire', [
            'besoin' => $besoin, 'mode' => 'edit',
            'villes' => (new VilleModel($db))->findAll(),
            'types'  => (new TypeBesoinModel($db))->findAllWithCategorie(),
        ], 'Modifier besoin - BNGRC');
    }

    public function update(int $id): void
    {
        $request = $this->app->request();
        $idVille  = (int)($request->data->id_ville ?? 0);
        $idType   = (int)($request->data->id_type_besoin ?? 0);
        $quantite = (float)($request->data->quantite_demandee ?? 0);

        if (!$idVille || !$idType || $quantite <= 0) {
            $besoin = $this->getModel()->findById($id);
            $db = $this->db();
            $this->render('besoin/formulaire', [
                'besoin' => $besoin, 'mode' => 'edit',
                'villes' => (new VilleModel($db))->findAll(),
                'types'  => (new TypeBesoinModel($db))->findAllWithCategorie(),
                'flash_error' => 'Tous les champs sont obligatoires.'
            ], 'Modifier besoin');
            return;
        }

        $this->getModel()->update($id, $idVille, $idType, $quantite);
        $this->redirect('/besoins?success=1');
    }

    public function delete(int $id): void
    {
        $this->getModel()->delete($id);
        $this->redirect('/besoins?deleted=1');
    }
}
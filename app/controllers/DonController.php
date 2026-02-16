<?php

namespace app\controllers;

use app\models\DonModel;
use app\models\DonateurModel;
use app\models\TypeBesoinModel;

class DonController extends BaseController
{
    private DonModel $model;

    private function getModel(): DonModel
    {
        if (!isset($this->model)) {
            $this->model = new DonModel($this->db());
        }
        return $this->model;
    }

    public function index(): void
    {
        $dons = $this->getModel()->findAll();
        $this->render('don/liste', ['dons' => $dons], 'Dons - BNGRC');
    }

    public function create(): void
    {
        $db = $this->db();
        $donateurs = (new DonateurModel($db))->findAll();
        $types     = (new TypeBesoinModel($db))->findAllWithCategorie();
        $this->render('don/formulaire', [
            'don' => null, 'mode' => 'create',
            'donateurs' => $donateurs, 'types' => $types
        ], 'Enregistrer un don - BNGRC');
    }

    public function store(): void
    {
        $request     = $this->app->request();
        $idDonateur  = ($request->data->id_donateur) ? (int)$request->data->id_donateur : null;
        $idType      = (int)($request->data->id_type_besoin ?? 0);
        $quantite    = (float)($request->data->quantite ?? 0);

        if (!$idType || $quantite <= 0) {
            $db = $this->db();
            $this->render('don/formulaire', [
                'don' => null, 'mode' => 'create',
                'donateurs' => (new DonateurModel($db))->findAll(),
                'types'     => (new TypeBesoinModel($db))->findAllWithCategorie(),
                'flash_error' => 'Le type de don et la quantité sont obligatoires.'
            ], 'Enregistrer un don');
            return;
        }

        $this->getModel()->create($idDonateur, $idType, $quantite);
        $this->redirect('/dons?success=1');
    }

    public function delete(int $id): void
    {
        try {
            $this->getModel()->delete($id);
            $this->redirect('/dons?deleted=1');
        } catch (\Exception $e) {
            $this->redirect('/dons?error=1');
        }
    }
}
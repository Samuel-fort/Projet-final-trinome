<?php

namespace app\controllers;

use app\models\VilleModel;

class VilleController extends BaseController
{
    private VilleModel $model;

    private function getModel(): VilleModel
    {
        if (!isset($this->model)) {
            $this->model = new VilleModel($this->db());
        }
        return $this->model;
    }

    public function index(): void
    {
        $villes = $this->getModel()->findAll();
        $this->render('ville/liste', ['villes' => $villes], 'Villes - BNGRC');
    }

    public function create(): void
    {
        $this->render('ville/formulaire', ['ville' => null, 'mode' => 'create'], 'Nouvelle ville - BNGRC');
    }

    public function store(): void
    {
        $request = $this->app->request();
        $nom = trim($request->data->nom_ville ?? '');
        $region = trim($request->data->region ?? '');

        if (empty($nom)) {
            $this->render('ville/formulaire', [
                'ville' => null, 'mode' => 'create',
                'flash_error' => 'Le nom de la ville est obligatoire.'
            ], 'Nouvelle ville');
            return;
        }

        $this->getModel()->create($nom, $region);
        $this->redirect('/villes?success=1');
    }

    public function edit(int $id): void
    {
        $ville = $this->getModel()->findById($id);
        if (!$ville) { $this->redirect('/villes'); return; }
        $this->render('ville/formulaire', ['ville' => $ville, 'mode' => 'edit'], 'Modifier ville - BNGRC');
    }

    public function update(int $id): void
    {
        $request = $this->app->request();
        $nom = trim($request->data->nom_ville ?? '');
        $region = trim($request->data->region ?? '');

        if (empty($nom)) {
            $ville = $this->getModel()->findById($id);
            $this->render('ville/formulaire', [
                'ville' => $ville, 'mode' => 'edit',
                'flash_error' => 'Le nom de la ville est obligatoire.'
            ], 'Modifier ville');
            return;
        }

        $this->getModel()->update($id, $nom, $region);
        $this->redirect('/villes?success=1');
    }

    public function delete(int $id): void
    {
        try {
            $this->getModel()->delete($id);
            $this->redirect('/villes?deleted=1');
        } catch (\Exception $e) {
            $this->redirect('/villes?error=1');
        }
    }
}
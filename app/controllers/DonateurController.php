<?php

namespace app\controllers;

use app\models\DonateurModel;

class DonateurController extends BaseController
{
    private DonateurModel $model;

    private function getModel(): DonateurModel
    {
        if (!isset($this->model)) {
            $this->model = new DonateurModel($this->db());
        }
        return $this->model;
    }

    public function index(): void
    {
        $donateurs = $this->getModel()->findAll();
        $this->render('donateur/liste', ['donateurs' => $donateurs], 'Donateurs - BNGRC');
    }

    public function create(): void
    {
        $this->render('donateur/formulaire', ['donateur' => null, 'mode' => 'create'], 'Nouveau donateur - BNGRC');
    }

    public function store(): void
    {
        $request = $this->app->request();
        $this->getModel()->create(
            trim($request->data->nom ?? ''),
            trim($request->data->prenom ?? ''),
            trim($request->data->organisation ?? ''),
            trim($request->data->telephone ?? ''),
            trim($request->data->email ?? ''),
            $request->data->type_donateur ?? 'particulier'
        );
        $this->redirect('/donateurs?success=1');
    }

    public function edit(int $id): void
    {
        $donateur = $this->getModel()->findById($id);
        if (!$donateur) { $this->redirect('/donateurs'); return; }
        $this->render('donateur/formulaire', ['donateur' => $donateur, 'mode' => 'edit'], 'Modifier donateur - BNGRC');
    }

    public function update(int $id): void
    {
        $request = $this->app->request();
        $this->getModel()->update(
            $id,
            trim($request->data->nom ?? ''),
            trim($request->data->prenom ?? ''),
            trim($request->data->organisation ?? ''),
            trim($request->data->telephone ?? ''),
            trim($request->data->email ?? ''),
            $request->data->type_donateur ?? 'particulier'
        );
        $this->redirect('/donateurs?success=1');
    }

    public function delete(int $id): void
    {
        try {
            $this->getModel()->delete($id);
            $this->redirect('/donateurs?deleted=1');
        } catch (\Exception $e) {
            $this->redirect('/donateurs?error=1');
        }
    }
}
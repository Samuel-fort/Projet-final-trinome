<?php

namespace app\controllers;

use app\models\TypeBesoinModel;

class TypeBesoinController extends BaseController
{
    private TypeBesoinModel $model;

    private function getModel(): TypeBesoinModel
    {
        if (!isset($this->model)) {
            $this->model = new TypeBesoinModel($this->db());
        }
        return $this->model;
    }

    public function index(): void
    {
        $types = $this->getModel()->findAll();
        $this->render('type_besoin/liste', ['types' => $types], 'Types de besoins - BNGRC');
    }

    public function create(): void
    {
        $categories = $this->getModel()->getCategories();
        $this->render('type_besoin/formulaire', [
            'type' => null, 'mode' => 'create', 'categories' => $categories
        ], 'Nouveau type de besoin - BNGRC');
    }

    public function store(): void
    {
        $request = $this->app->request();
        $idCat   = (int)($request->data->id_categorie ?? 0);
        $nom     = trim($request->data->nom ?? '');
        $unite   = trim($request->data->unite ?? '');
        $prix    = (float)($request->data->prix_unitaire ?? 0);

        if (!$idCat || empty($nom) || empty($unite) || $prix <= 0) {
            $this->render('type_besoin/formulaire', [
                'type' => null, 'mode' => 'create',
                'categories' => $this->getModel()->getCategories(),
                'flash_error' => 'Tous les champs sont obligatoires et le prix doit être > 0.'
            ], 'Nouveau type de besoin');
            return;
        }

        $this->getModel()->create($idCat, $nom, $unite, $prix);
        $this->redirect('/types-besoins?success=1');
    }

    public function edit(int $id): void
    {
        $type = $this->getModel()->findById($id);
        if (!$type) { $this->redirect('/types-besoins'); return; }
        $categories = $this->getModel()->getCategories();
        $this->render('type_besoin/formulaire', [
            'type' => $type, 'mode' => 'edit', 'categories' => $categories
        ], 'Modifier type de besoin - BNGRC');
    }

    public function update(int $id): void
    {
        $request = $this->app->request();
        $idCat   = (int)($request->data->id_categorie ?? 0);
        $nom     = trim($request->data->nom ?? '');
        $unite   = trim($request->data->unite ?? '');
        $prix    = (float)($request->data->prix_unitaire ?? 0);

        if (!$idCat || empty($nom) || empty($unite) || $prix <= 0) {
            $type = $this->getModel()->findById($id);
            $this->render('type_besoin/formulaire', [
                'type' => $type, 'mode' => 'edit',
                'categories' => $this->getModel()->getCategories(),
                'flash_error' => 'Tous les champs sont obligatoires.'
            ], 'Modifier type de besoin');
            return;
        }

        $this->getModel()->update($id, $idCat, $nom, $unite, $prix);
        $this->redirect('/types-besoins?success=1');
    }

    public function delete(int $id): void
    {
        try {
            $this->getModel()->delete($id);
            $this->redirect('/types-besoins?deleted=1');
        } catch (\Exception $e) {
            $this->redirect('/types-besoins?error=1');
        }
    }
}
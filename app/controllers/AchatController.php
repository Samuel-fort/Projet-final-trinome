<?php

namespace app\controllers;

use app\models\AchatModel;
use app\models\ConfigModel;
use app\models\DonModel;
use app\models\TypeBesoinModel;
use app\models\VilleModel;

class AchatController extends BaseController
{
    private AchatModel $model;

    private function getModel(): AchatModel
    {
        if (!isset($this->model)) {
            $this->model = new AchatModel($this->db());
        }
        return $this->model;
    }

    /**
     * Page principale des achats
     */
    public function index(): void
    {
        $achats = $this->getModel()->findAll();
        $donsArgent = $this->getDonsArgentDisponibles();
        $typesBesoins = (new TypeBesoinModel($this->db()))->findAll();
        $villes = (new VilleModel($this->db()))->findAll();
        $fraisPourcentage = (new ConfigModel($this->db()))->getFraisAchat();

        $this->render('achat/liste', [
            'achats' => $achats,
            'donsArgent' => $donsArgent,
            'typesBesoins' => $typesBesoins,
            'villes' => $villes,
            'fraisPourcentage' => $fraisPourcentage,
        ], 'Achats - BNGRC');
    }

    /**
     * Récupère les dons en argent disponibles
     */
    private function getDonsArgentDisponibles(): array
    {
        // Type besoin 11 = "Aide financière"
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
     * Calcule le montant pour un achat (AJAX)
     */
    public function calculer(): void
    {
        $request = $this->app->request();
        $idTypeBesoin = (int)($request->query->id_type_besoin ?? 0);
        $quantite = (float)($request->query->quantite ?? 0);
        $fraisPourcentage = (float)($request->query->frais ?? 10);

        if (!$idTypeBesoin || $quantite <= 0) {
            $this->app->json(['error' => 'Paramètres invalides']);
            return;
        }

        // Récupérer le prix unitaire
        $type = $this->db()->fetchRow("
            SELECT prix_unitaire, unite FROM type_besoin WHERE id_type_besoin = ?
        ", [$idTypeBesoin]);

        if (!$type) {
            $this->app->json(['error' => 'Type de besoin introuvable']);
            return;
        }

        $prixUnitaire = (float)$type['prix_unitaire'];
        $montantBase = $quantite * $prixUnitaire;
        $montantFrais = $montantBase * ($fraisPourcentage / 100);
        $montantTotal = $montantBase + $montantFrais;

        $this->app->json([
            'prix_unitaire' => $prixUnitaire,
            'unite' => $type['unite'],
            'montant_base' => $montantBase,
            'montant_frais' => $montantFrais,
            'montant_total' => $montantTotal,
            'frais_pourcentage' => $fraisPourcentage,
        ]);
    }

    /**
     * Enregistre un achat
     */
    public function store(): void
    {
        $request = $this->app->request();
        $idDonArgent = (int)($request->data->id_don_argent ?? 0);
        $idTypeBesoin = (int)($request->data->id_type_besoin ?? 0);
        $quantite = (float)($request->data->quantite ?? 0);

        if (!$idDonArgent || !$idTypeBesoin || $quantite <= 0) {
            $this->redirect('/achats?error=champs');
            return;
        }

        try {
            // Récupérer les informations
            $configModel = new ConfigModel($this->db());
            $fraisPourcentage = $configModel->getFraisAchat();

            $type = $this->db()->fetchRow("
                SELECT prix_unitaire FROM type_besoin WHERE id_type_besoin = ?
            ", [$idTypeBesoin]);

            if (!$type) {
                $this->redirect('/achats?error=type_introuvable');
                return;
            }

            $prixUnitaire = (float)$type['prix_unitaire'];
            $montantTotal = ($quantite * $prixUnitaire) * (1 + $fraisPourcentage / 100);

            // Vérifier le budget disponible
            $disponible = $this->getModel()->getMontantDisponible($idDonArgent);
            if ($montantTotal > $disponible) {
                $this->redirect('/achats?error=budget_insuffisant');
                return;
            }

            // Créer l'achat
            $this->getModel()->creerAchat(
                $idDonArgent,
                $idTypeBesoin,
                $quantite,
                $prixUnitaire,
                $fraisPourcentage
            );

            $this->redirect('/achats?success=1');
        } catch (\Exception $e) {
            $this->redirect('/achats?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Supprime un achat
     */
    public function delete(int $id): void
    {
        $this->getModel()->delete($id);
        $this->redirect('/achats?deleted=1');
    }
}
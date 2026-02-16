<?php

namespace app\models;

class AchatModel extends BaseModel
{
    /**
     * Enregistre un achat effectué avec de l'argent
     */
    public function creerAchat(
        int $idDonArgent,
        int $idTypeBesoin,
        float $quantite,
        float $prixUnitaire,
        float $fraisPourcentage
    ): int {
        $montantTotal = ($quantite * $prixUnitaire) * (1 + $fraisPourcentage / 100);

        $this->db->runQuery("
            INSERT INTO achat (
                id_don_argent, id_type_besoin, quantite_achetee, 
                prix_unitaire, frais_pourcentage, montant_total
            ) VALUES (?, ?, ?, ?, ?, ?)
        ", [
            $idDonArgent,
            $idTypeBesoin,
            $quantite,
            $prixUnitaire,
            $fraisPourcentage,
            $montantTotal
        ]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Récupère tous les achats
     */
    public function findAll(): array
    {
        $result = $this->db->fetchAll("
            SELECT a.*, 
                   tb.nom AS type_nom, 
                   tb.unite,
                   tb.prix_unitaire AS prix_actuel,
                   CONCAT(COALESCE(d.prenom,''), ' ', COALESCE(d.nom,''),
                          CASE WHEN d.organisation IS NOT NULL 
                               THEN CONCAT(' (', d.organisation, ')') 
                               ELSE '' END) AS donateur_nom
            FROM achat a
            JOIN type_besoin tb ON a.id_type_besoin = tb.id_type_besoin
            JOIN don dn ON a.id_don_argent = dn.id_don
            LEFT JOIN donateur d ON dn.id_donateur = d.id_donateur
            ORDER BY a.date_achat DESC
        ");
        
        // Convertir Collection en array
        return json_decode(json_encode($result), true);
    }

    /**
     * Récupère le montant total dépensé pour un don en argent
     */
    public function getMontantDepense(int $idDonArgent): float
    {
        $result = $this->db->fetchRow("
            SELECT COALESCE(SUM(montant_total), 0) AS total_depense
            FROM achat
            WHERE id_don_argent = ?
        ", [$idDonArgent]);

        return (float)($result['total_depense'] ?? 0);
    }

    /**
     * Récupère le montant disponible pour un don en argent
     */
    public function getMontantDisponible(int $idDonArgent): float
    {
        $result = $this->db->fetchRow("
            SELECT dn.quantite AS montant_total,
                   COALESCE((SELECT SUM(montant_total) FROM achat WHERE id_don_argent = ?), 0) AS depense,
                   (dn.quantite - COALESCE((SELECT SUM(montant_total) FROM achat WHERE id_don_argent = ?), 0)) AS disponible
            FROM don dn
            WHERE dn.id_don = ?
        ", [$idDonArgent, $idDonArgent, $idDonArgent]);

        return (float)($result['disponible'] ?? 0);
    }

    /**
     * Supprime un achat (annulation)
     */
    public function delete(int $id): bool
    {
        $this->db->runQuery("DELETE FROM achat WHERE id_achat = ?", [$id]);
        return true;
    }
}
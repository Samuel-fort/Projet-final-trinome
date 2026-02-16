<?php

namespace app\models;

class DonModel extends BaseModel
{
    public function findAll(): array
    {
        return $this->db->fetchAll("
            SELECT dn.*, tb.nom AS type_nom, tb.unite, tb.prix_unitaire, cb.nom_categorie,
                   CONCAT(COALESCE(d.prenom,''), ' ', COALESCE(d.nom, ''), 
                          CASE WHEN d.organisation IS NOT NULL THEN CONCAT(' (', d.organisation, ')') ELSE '' END) AS donateur_nom,
                   (dn.quantite * tb.prix_unitaire) AS valeur,
                   COALESCE(
                       (SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_don = dn.id_don),
                       0
                   ) AS quantite_distribuee
            FROM don dn
            JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            LEFT JOIN donateur d ON dn.id_donateur = d.id_donateur
            ORDER BY dn.date_saisie DESC
        ");
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchRow("
            SELECT dn.*, tb.nom AS type_nom, tb.unite, tb.prix_unitaire,
                   COALESCE(
                       (SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_don = dn.id_don),
                       0
                   ) AS quantite_distribuee
            FROM don dn
            JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
            WHERE dn.id_don = ?
        ", [$id]) ?: null;
    }

    public function getStats(): array
    {
        $result = $this->db->fetchRow("
            SELECT
                COUNT(*) AS nb_dons,
                COALESCE(SUM(dn.quantite * tb.prix_unitaire), 0) AS valeur_totale,
                COALESCE(SUM(
                    (SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_don = dn.id_don)
                    * tb.prix_unitaire
                ), 0) AS valeur_distribuee
            FROM don dn
            JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
        ");
        
        return $result ? json_decode(json_encode($result), true) : ['nb_dons' => 0, 'valeur_totale' => 0, 'valeur_distribuee' => 0];
    }

    public function create(int $idDonateur = null, int $idTypeBesoin, float $quantite): bool
    {
        $this->db->runQuery(
            "INSERT INTO don (id_donateur, id_type_besoin, quantite) VALUES (?, ?, ?)",
            [$idDonateur, $idTypeBesoin, $quantite]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->runQuery("DELETE FROM don WHERE id_don = ?", [$id]);
        return true;
    }

    /** Dons disponibles (non encore totalement distribués) pour un type donné */
    public function findDisponiblesByType(int $idTypeBesoin): array
    {
        return $this->db->fetchAll("
            SELECT dn.*,
                   COALESCE(
                       (SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_don = dn.id_don),
                       0
                   ) AS quantite_distribuee,
                   (dn.quantite - COALESCE(
                       (SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_don = dn.id_don),
                       0
                   )) AS quantite_disponible
            FROM don dn
            WHERE dn.id_type_besoin = ?
            HAVING quantite_disponible > 0
            ORDER BY dn.date_saisie ASC
        ", [$idTypeBesoin]);
    }
}
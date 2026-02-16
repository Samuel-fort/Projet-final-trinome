<?php

namespace app\models;

class DistributionModel extends BaseModel
{
    public function findAll(): array
    {
        return $this->db->fetchAll("
            SELECT dist.*, v.nom_ville, tb.nom AS type_nom, tb.unite, tb.prix_unitaire,
                   cb.nom_categorie,
                   (dist.quantite_attribuee * tb.prix_unitaire) AS valeur,
                   CONCAT(COALESCE(d.prenom,''), ' ', COALESCE(d.nom,''),
                          CASE WHEN d.organisation IS NOT NULL THEN CONCAT(' (', d.organisation, ')') ELSE '' END) AS donateur_nom
            FROM distribution dist
            JOIN ville v ON dist.id_ville = v.id_ville
            JOIN don dn ON dist.id_don = dn.id_don
            JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            LEFT JOIN donateur d ON dn.id_donateur = d.id_donateur
            ORDER BY dist.date_distribution DESC
        ");
    }

    /**
     * Distribue manuellement une quantité d'un don vers une ville/besoin
     * et met à jour quantite_recue dans besoin_ville
     */
    public function distribuer(int $idDon, int $idVille, int $idBesoin, float $quantite): bool
    {
        // Vérifier la quantité disponible du don
        $don = $this->db->fetchRow("
            SELECT dn.quantite,
                   COALESCE((SELECT SUM(d2.quantite_attribuee) FROM distribution d2 WHERE d2.id_don = ?), 0) AS deja_distribue
            FROM don dn WHERE dn.id_don = ?
        ", [$idDon, $idDon]);

        if (!$don) return false;

        $disponible = $don['quantite'] - $don['deja_distribue'];
        if ($quantite > $disponible) {
            throw new \Exception("Quantité demandée ($quantite) supérieure à la quantité disponible ($disponible).");
        }

        // Insérer la distribution
        $this->db->runQuery(
            "INSERT INTO distribution (id_don, id_ville, id_besoin, quantite_attribuee) VALUES (?, ?, ?, ?)",
            [$idDon, $idVille, $idBesoin, $quantite]
        );

        // Mettre à jour quantite_recue dans besoin_ville
        $this->db->runQuery(
            "UPDATE besoin_ville SET quantite_recue = quantite_recue + ? WHERE id_besoin = ?",
            [$quantite, $idBesoin]
        );

        return true;
    }

    public function delete(int $id): bool
    {
        // Récupérer infos avant suppression pour rollback de quantite_recue
        $dist = $this->db->fetchRow("SELECT * FROM distribution WHERE id_distribution = ?", [$id]);
        if ($dist) {
            $this->db->runQuery(
                "UPDATE besoin_ville SET quantite_recue = quantite_recue - ? WHERE id_besoin = ?",
                [$dist['quantite_attribuee'], $dist['id_besoin']]
            );
            $this->db->runQuery("DELETE FROM distribution WHERE id_distribution = ?", [$id]);
        }
        return true;
    }

    /** Données pour le formulaire de distribution */
    public function getDonsDisponibles(): array
    {
        return $this->db->fetchAll("
            SELECT dn.id_don, dn.quantite, dn.date_saisie,
                   tb.nom AS type_nom, tb.unite, tb.id_type_besoin,
                   CONCAT(COALESCE(d.prenom,''), ' ', COALESCE(d.nom,'')) AS donateur_nom,
                   COALESCE(
                       (SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_don = dn.id_don), 0
                   ) AS quantite_distribuee,
                   (dn.quantite - COALESCE(
                       (SELECT SUM(dist.quantite_attribuee) FROM distribution dist WHERE dist.id_don = dn.id_don), 0
                   )) AS quantite_disponible
            FROM don dn
            JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
            LEFT JOIN donateur d ON dn.id_donateur = d.id_donateur
            HAVING quantite_disponible > 0
            ORDER BY dn.date_saisie ASC
        ");
    }

    /** Besoins non satisfaits pour une ville et un type donné */
    public function getBesoinsOuverts(int $idVille, int $idTypeBesoin): array
    {
        return $this->db->fetchAll("
            SELECT bv.*, tb.nom AS type_nom, tb.unite,
                   (bv.quantite_demandee - bv.quantite_recue) AS quantite_manquante
            FROM besoin_ville bv
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            WHERE bv.id_ville = ? AND bv.id_type_besoin = ?
              AND bv.quantite_recue < bv.quantite_demandee
        ", [$idVille, $idTypeBesoin]);
    }
}
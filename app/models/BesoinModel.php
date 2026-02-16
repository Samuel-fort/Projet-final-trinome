<?php

namespace app\models;

class BesoinModel extends BaseModel
{
    public function findAll(): array
    {
        return $this->db->fetchAll("
            SELECT bv.*, v.nom_ville, tb.nom AS type_nom, tb.unite, tb.prix_unitaire,
                   cb.nom_categorie,
                   (bv.quantite_demandee * tb.prix_unitaire) AS valeur_totale,
                   (bv.quantite_recue * tb.prix_unitaire) AS valeur_recue
            FROM besoin_ville bv
            JOIN ville v ON bv.id_ville = v.id_ville
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            ORDER BY bv.date_saisie DESC
        ");
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchRow("
            SELECT bv.*, v.nom_ville, tb.nom AS type_nom, tb.unite, tb.prix_unitaire, cb.nom_categorie
            FROM besoin_ville bv
            JOIN ville v ON bv.id_ville = v.id_ville
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            WHERE bv.id_besoin = ?
        ", [$id]) ?: null;
    }

    public function findByVille(int $idVille): array
    {
        return $this->db->fetchAll("
            SELECT bv.*, tb.nom AS type_nom, tb.unite, tb.prix_unitaire, cb.nom_categorie,
                   (bv.quantite_demandee * tb.prix_unitaire) AS valeur_totale,
                   (bv.quantite_recue * tb.prix_unitaire) AS valeur_recue
            FROM besoin_ville bv
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            WHERE bv.id_ville = ?
            ORDER BY cb.nom_categorie, tb.nom
        ", [$idVille]);
    }

    public function create(int $idVille, int $idTypeBesoin, float $quantite): bool
    {
        $this->db->runQuery(
            "INSERT INTO besoin_ville (id_ville, id_type_besoin, quantite_demandee) VALUES (?, ?, ?)",
            [$idVille, $idTypeBesoin, $quantite]
        );
        return true;
    }

    public function update(int $id, int $idVille, int $idTypeBesoin, float $quantite): bool
    {
        $this->db->runQuery(
            "UPDATE besoin_ville SET id_ville = ?, id_type_besoin = ?, quantite_demandee = ? WHERE id_besoin = ?",
            [$idVille, $idTypeBesoin, $quantite, $id]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->runQuery("DELETE FROM besoin_ville WHERE id_besoin = ?", [$id]);
        return true;
    }
}
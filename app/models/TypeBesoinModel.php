<?php

namespace app\models;

class TypeBesoinModel extends BaseModel
{
    public function findAll(): array
    {
        return $this->db->fetchAll("
            SELECT tb.*, cb.nom_categorie
            FROM type_besoin tb
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            ORDER BY cb.nom_categorie, tb.nom
        ");
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchRow("
            SELECT tb.*, cb.nom_categorie
            FROM type_besoin tb
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            WHERE tb.id_type_besoin = ?
        ", [$id]) ?: null;
    }

    public function findAllWithCategorie(): array
    {
        return $this->db->fetchAll("
            SELECT tb.*, cb.nom_categorie
            FROM type_besoin tb
            JOIN categorie_besoin cb ON tb.id_categorie = cb.id_categorie
            ORDER BY cb.nom_categorie, tb.nom
        ");
    }

    public function getCategories(): array
    {
        return $this->db->fetchAll("SELECT * FROM categorie_besoin ORDER BY nom_categorie");
    }

    public function create(int $idCategorie, string $nom, string $unite, float $prixUnitaire): bool
    {
        $this->db->runQuery(
            "INSERT INTO type_besoin (id_categorie, nom, unite, prix_unitaire) VALUES (?, ?, ?, ?)",
            [$idCategorie, $nom, $unite, $prixUnitaire]
        );
        return true;
    }

    public function update(int $id, int $idCategorie, string $nom, string $unite, float $prixUnitaire): bool
    {
        $this->db->runQuery(
            "UPDATE type_besoin SET id_categorie = ?, nom = ?, unite = ?, prix_unitaire = ? WHERE id_type_besoin = ?",
            [$idCategorie, $nom, $unite, $prixUnitaire, $id]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->runQuery("DELETE FROM type_besoin WHERE id_type_besoin = ?", [$id]);
        return true;
    }
}
<?php

namespace app\models;

class VilleModel extends BaseModel
{
    public function findAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM ville ORDER BY nom_ville");
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchRow("SELECT * FROM ville WHERE id_ville = ?", [$id]) ?: null;
    }

    public function create(string $nom, string $region): bool
    {
        $this->db->runQuery(
            "INSERT INTO ville (nom_ville, region) VALUES (?, ?)",
            [$nom, $region]
        );
        return true;
    }

    public function update(int $id, string $nom, string $region): bool
    {
        $this->db->runQuery(
            "UPDATE ville SET nom_ville = ?, region = ? WHERE id_ville = ?",
            [$nom, $region, $id]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->runQuery("DELETE FROM ville WHERE id_ville = ?", [$id]);
        return true;
    }

    /** Tableau de bord : besoins + dons par ville */
    public function getDashboardData(): array
    {
        return $this->db->fetchAll("
            SELECT
                v.id_ville,
                v.nom_ville,
                v.region,
                COUNT(DISTINCT bv.id_besoin) AS nb_besoins,
                COALESCE(SUM(bv.quantite_demandee * tb.prix_unitaire), 0) AS valeur_besoins,
                COALESCE(SUM(bv.quantite_recue * tb.prix_unitaire), 0) AS valeur_recue
            FROM ville v
            LEFT JOIN besoin_ville bv ON v.id_ville = bv.id_ville
            LEFT JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            GROUP BY v.id_ville, v.nom_ville, v.region
            ORDER BY valeur_besoins DESC
        ");
    }
}
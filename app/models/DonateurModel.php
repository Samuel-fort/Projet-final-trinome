<?php

namespace app\models;

class DonateurModel extends BaseModel
{
    public function findAll(): array
    {
        return $this->db->fetchAll("
            SELECT d.*, COUNT(dn.id_don) AS nb_dons,
                   COALESCE(SUM(dn.quantite * tb.prix_unitaire), 0) AS valeur_totale
            FROM donateur d
            LEFT JOIN don dn ON d.id_donateur = dn.id_donateur
            LEFT JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
            GROUP BY d.id_donateur
            ORDER BY d.date_inscription DESC
        ");
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetchRow("SELECT * FROM donateur WHERE id_donateur = ?", [$id]) ?: null;
    }

    public function create(string $nom, string $prenom, string $organisation, string $telephone, string $email, string $type): bool
    {
        $this->db->runQuery(
            "INSERT INTO donateur (nom, prenom, organisation, telephone, email, type_donateur) VALUES (?, ?, ?, ?, ?, ?)",
            [$nom ?: null, $prenom ?: null, $organisation ?: null, $telephone ?: null, $email ?: null, $type]
        );
        return true;
    }

    public function update(int $id, string $nom, string $prenom, string $organisation, string $telephone, string $email, string $type): bool
    {
        $this->db->runQuery(
            "UPDATE donateur SET nom = ?, prenom = ?, organisation = ?, telephone = ?, email = ?, type_donateur = ? WHERE id_donateur = ?",
            [$nom ?: null, $prenom ?: null, $organisation ?: null, $telephone ?: null, $email ?: null, $type, $id]
        );
        return true;
    }

    public function delete(int $id): bool
    {
        $this->db->runQuery("DELETE FROM donateur WHERE id_donateur = ?", [$id]);
        return true;
    }
}
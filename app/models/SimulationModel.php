<?php

namespace app\models;

class SimulationModel extends BaseModel
{
    /**
     * Crée une nouvelle simulation
     */
    public function creerSimulation(int $idDonArgent, array $details, float $montantTotal): int
    {
        // Nettoyer les anciennes simulations en cours (plus de 1 heure)
        $this->db->runQuery("
            DELETE FROM simulation 
            WHERE statut = 'en_cours' 
            AND date_creation < DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");

        $this->db->runQuery("
            INSERT INTO simulation (id_don_argent, details_json, montant_total, statut)
            VALUES (?, ?, ?, 'en_cours')
        ", [$idDonArgent, json_encode($details), $montantTotal]);

        return (int)$this->db->lastInsertId();
    }

    /**
     * Récupère une simulation
     */
    public function getSimulation(int $idSimulation): ?array
    {
        $sim = $this->db->fetchRow("
            SELECT * FROM simulation WHERE id_simulation = ?
        ", [$idSimulation]);

        if ($sim) {
            // Convertir Collection en array
            $sim = json_decode(json_encode($sim), true);
            $sim['details'] = json_decode($sim['details_json'], true);
        }

        return $sim;
    }

    /**
     * Valide une simulation (transforme en achats réels)
     */
    public function validerSimulation(int $idSimulation): bool
    {
        $sim = $this->getSimulation($idSimulation);
        if (!$sim || $sim['statut'] !== 'en_cours') {
            return false;
        }

        // Créer les achats réels
        $achatModel = new AchatModel($this->db);
        $details = $sim['details'];

        foreach ($details['achats'] as $achat) {
            $achatModel->creerAchat(
                $sim['id_don_argent'],
                $achat['id_type_besoin'],
                $achat['quantite'],
                $achat['prix_unitaire'],
                $details['frais_pourcentage']
            );
        }

        // Marquer la simulation comme validée
        $this->db->runQuery("
            UPDATE simulation 
            SET statut = 'validee' 
            WHERE id_simulation = ?
        ", [$idSimulation]);

        return true;
    }

    /**
     * Annule une simulation
     */
    public function annulerSimulation(int $idSimulation): bool
    {
        $this->db->runQuery("
            UPDATE simulation 
            SET statut = 'annulee' 
            WHERE id_simulation = ?
        ", [$idSimulation]);

        return true;
    }

    /**
     * Récupère les simulations en cours
     */
    public function getSimulationsEnCours(): array
    {
        $result = $this->db->fetchAll("
            SELECT s.*, 
                   CONCAT(COALESCE(d.prenom,''), ' ', COALESCE(d.nom,'')) AS donateur_nom
            FROM simulation s
            JOIN don dn ON s.id_don_argent = dn.id_don
            LEFT JOIN donateur d ON dn.id_donateur = d.id_donateur
            WHERE s.statut = 'en_cours'
            ORDER BY s.date_creation DESC
        ");
        
        // Convertir Collection en array
        return json_decode(json_encode($result), true);
    }
}
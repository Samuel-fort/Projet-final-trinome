<?php

namespace app\models;

class ConfigModel extends BaseModel
{
    /**
     * Récupère la valeur d'une configuration par son nom
     */
    public function getConfig(string $nomConfig): ?array
    {
        return $this->db->fetchRow("
            SELECT * FROM config_frais 
            WHERE nom_config = ?
        ", [$nomConfig]);
    }

    /**
     * Récupère le pourcentage de frais d'achat
     */
    public function getFraisAchat(): float
    {
        $config = $this->getConfig('frais_achat_pourcentage');
        return $config ? (float)$config['valeur'] : 10.00; // 10% par défaut
    }

    /**
     * Met à jour une configuration
     */
    public function updateConfig(string $nomConfig, float $valeur): bool
    {
        $this->db->runQuery("
            UPDATE config_frais 
            SET valeur = ?, date_modification = NOW()
            WHERE nom_config = ?
        ", [$valeur, $nomConfig]);
        return true;
    }

    /**
     * Récupère toutes les configurations
     */
    public function getAllConfigs(): array
    {
        return $this->db->fetchAll("
            SELECT * FROM config_frais 
            ORDER BY nom_config
        ");
    }
}
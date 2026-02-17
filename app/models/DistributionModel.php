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

    /**
     * ============================================================================
     * NOUVEAUX ALGORITHMES DE DISTRIBUTION AUTOMATIQUE (V3)
     * ============================================================================
     */

    /**
     * Récupère tous les besoins non satisfaits pour un type de besoin donné
     * (tous dons confondus de ce type)
     */
    public function getBesoinsNonSatisfaitsPourType(int $idTypeBesoin): array
    {
        return $this->db->fetchAll("
            SELECT bv.id_besoin, bv.id_ville, bv.quantite_demandee, bv.quantite_recue, bv.date_saisie,
                   (bv.quantite_demandee - bv.quantite_recue) AS quantite_manquante,
                   v.nom_ville,
                   tb.nom AS type_nom, tb.unite
            FROM besoin_ville bv
            JOIN ville v ON bv.id_ville = v.id_ville
            JOIN type_besoin tb ON bv.id_type_besoin = tb.id_type_besoin
            WHERE bv.id_type_besoin = ?
              AND bv.quantite_recue < bv.quantite_demandee
            ORDER BY bv.date_saisie ASC
        ", [$idTypeBesoin]);
    }

    /**
     * Simule une distribution automatique selon le mode choisi
     * 
     * @param int $idDon ID du don à distribuer
     * @param string $mode Mode de distribution : 'anciennete', 'demande_min', 'proportionnalite'
     * @return array Résultat de la simulation
     */
    public function simulerDistributionAuto(int $idDon, string $mode): array
    {
        // Récupérer le don et sa quantité disponible
        $don = $this->db->fetchRow("
            SELECT dn.id_don, dn.quantite, dn.id_type_besoin,
                   tb.nom AS type_nom, tb.unite,
                   COALESCE((SELECT SUM(d.quantite_attribuee) FROM distribution d WHERE d.id_don = ?), 0) AS deja_distribue,
                   (dn.quantite - COALESCE((SELECT SUM(d.quantite_attribuee) FROM distribution d WHERE d.id_don = ?), 0)) AS quantite_disponible
            FROM don dn
            JOIN type_besoin tb ON dn.id_type_besoin = tb.id_type_besoin
            WHERE dn.id_don = ?
        ", [$idDon, $idDon, $idDon]);

        if (!$don || $don['quantite_disponible'] <= 0) {
            return [
                'success' => false,
                'error' => 'Don introuvable ou épuisé',
                'distributions' => [],
            ];
        }

        $quantiteDispo = (float)$don['quantite_disponible'];
        $idTypeBesoin = (int)$don['id_type_besoin'];

        // Récupérer tous les besoins de ce type
        $besoins = $this->getBesoinsNonSatisfaitsPourType($idTypeBesoin);

        if (empty($besoins)) {
            return [
                'success' => false,
                'error' => 'Aucun besoin à satisfaire pour ce type',
                'distributions' => [],
            ];
        }

        // Appeler l'algorithme correspondant
        $distributions = match($mode) {
            'anciennete' => $this->algoAnciennete($besoins, $quantiteDispo),
            'demande_min' => $this->algoDemandeMinimale($besoins, $quantiteDispo),
            'proportionnalite' => $this->algoProportionnalite($besoins, $quantiteDispo),
            default => [],
        };

        // Calculer les statistiques
        $stats = $this->calculerStatistiques($besoins, $distributions, $quantiteDispo);

        return [
            'success' => true,
            'id_don' => $idDon,
            'type_nom' => $don['type_nom'],
            'unite' => $don['unite'],
            'quantite_totale' => $quantiteDispo,
            'distributions' => $distributions,
            'stats' => $stats,
        ];
    }

    /**
     * ALGORITHME 1 : Distribution par ancienneté
     * Distribue aux besoins les plus anciens en premier
     */
    private function algoAnciennete(array $besoins, float $quantiteDispo): array
    {
        $distributions = [];
        $reste = $quantiteDispo;

        // Besoins déjà triés par date_saisie ASC dans la requête
        foreach ($besoins as $besoin) {
            if ($reste <= 0) break;

            $quantiteManquante = (float)$besoin['quantite_manquante'];
            $quantiteAttribuee = min($quantiteManquante, $reste);

            $distributions[] = [
                'id_besoin' => $besoin['id_besoin'],
                'id_ville' => $besoin['id_ville'],
                'nom_ville' => $besoin['nom_ville'],
                'quantite_manquante' => $quantiteManquante,
                'quantite_attribuee' => $quantiteAttribuee,
                'date_saisie' => $besoin['date_saisie'],
            ];

            $reste -= $quantiteAttribuee;
        }

        return $distributions;
    }

    /**
     * ALGORITHME 2 : Distribution par demande minimale
     * Distribue d'abord aux villes qui demandent le moins
     */
    private function algoDemandeMinimale(array $besoins, float $quantiteDispo): array
    {
        // Trier par quantité manquante croissante
        usort($besoins, function($a, $b) {
            return $a['quantite_manquante'] <=> $b['quantite_manquante'];
        });

        $distributions = [];
        $reste = $quantiteDispo;

        foreach ($besoins as $besoin) {
            if ($reste <= 0) break;

            $quantiteManquante = (float)$besoin['quantite_manquante'];
            $quantiteAttribuee = min($quantiteManquante, $reste);

            $distributions[] = [
                'id_besoin' => $besoin['id_besoin'],
                'id_ville' => $besoin['id_ville'],
                'nom_ville' => $besoin['nom_ville'],
                'quantite_manquante' => $quantiteManquante,
                'quantite_attribuee' => $quantiteAttribuee,
                'date_saisie' => $besoin['date_saisie'],
            ];

            $reste -= $quantiteAttribuee;
        }

        return $distributions;
    }

    /**
     * ALGORITHME 3 : Distribution proportionnelle
     * Distribue proportionnellement selon les besoins, arrondi à l'inférieur
     * Le reste va au plus gros besoin
     */
    private function algoProportionnalite(array $besoins, float $quantiteDispo): array
    {
        // Calculer le total des besoins
        $totalBesoins = array_reduce($besoins, function($sum, $b) {
            return $sum + (float)$b['quantite_manquante'];
        }, 0);

        if ($totalBesoins == 0) return [];

        $distributions = [];
        $totalDistribue = 0;

        // Calculer les parts proportionnelles (arrondi inférieur)
        foreach ($besoins as $besoin) {
            $quantiteManquante = (float)$besoin['quantite_manquante'];
            $proportion = $quantiteManquante / $totalBesoins;
            $quantiteAttribuee = floor($proportion * $quantiteDispo);

            $distributions[] = [
                'id_besoin' => $besoin['id_besoin'],
                'id_ville' => $besoin['id_ville'],
                'nom_ville' => $besoin['nom_ville'],
                'quantite_manquante' => $quantiteManquante,
                'quantite_attribuee' => $quantiteAttribuee,
                'date_saisie' => $besoin['date_saisie'],
            ];

            $totalDistribue += $quantiteAttribuee;
        }

        // Distribuer le reste au plus gros besoin
        $reste = $quantiteDispo - $totalDistribue;
        if ($reste > 0) {
            // Trouver l'index du plus gros besoin
            $indexMaxBesoin = 0;
            $maxBesoin = 0;
            foreach ($distributions as $index => $dist) {
                if ($dist['quantite_manquante'] > $maxBesoin) {
                    $maxBesoin = $dist['quantite_manquante'];
                    $indexMaxBesoin = $index;
                }
            }

            // Ajouter le reste au plus gros besoin (sans dépasser son besoin)
            $ajout = min($reste, $distributions[$indexMaxBesoin]['quantite_manquante'] - $distributions[$indexMaxBesoin]['quantite_attribuee']);
            $distributions[$indexMaxBesoin]['quantite_attribuee'] += $ajout;
        }

        return $distributions;
    }

    /**
     * Calcule les statistiques de la distribution
     */
    private function calculerStatistiques(array $besoins, array $distributions, float $quantiteDispo): array
    {
        $nbBesoinsTotal = count($besoins);
        $nbBesoinsServis = count(array_filter($distributions, fn($d) => $d['quantite_attribuee'] > 0));
        
        $totalDistribue = array_reduce($distributions, fn($sum, $d) => $sum + $d['quantite_attribuee'], 0);
        $totalDemande = array_reduce($besoins, fn($sum, $b) => $sum + (float)$b['quantite_manquante'], 0);

        $tauxSatisfaction = $totalDemande > 0 ? ($totalDistribue / $totalDemande) * 100 : 0;

        return [
            'nb_villes_total' => $nbBesoinsTotal,
            'nb_villes_servies' => $nbBesoinsServis,
            'quantite_distribuee' => $totalDistribue,
            'quantite_restante' => $quantiteDispo - $totalDistribue,
            'taux_satisfaction' => round($tauxSatisfaction, 2),
        ];
    }

    /**
     * Valide une simulation : enregistre toutes les distributions en base
     */
    public function validerSimulation(int $idDon, array $distributions): bool
    {
        try {
            foreach ($distributions as $dist) {
                if ($dist['quantite_attribuee'] <= 0) continue;

                $this->distribuer(
                    $idDon,
                    $dist['id_ville'],
                    $dist['id_besoin'],
                    $dist['quantite_attribuee']
                );
            }
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Erreur lors de la validation : " . $e->getMessage());
        }
    }
}
# BNGRC - Todolist du Projet

**Projet:** Système de Gestion des Dons pour Sinistrés  
**Équipe:** Voara (004587), Samuel (003889), Lionel (003972)  


---

## Répartition des tâches

| Personne | Rôle |
|----------|------|
| **Lionel** | Backend - PHP/MySQL, Logique métier, API |
| **Samuel** | Frontend - CSS, Affichage, Corrections |
| **Voara** | Architecture - Structure MVC, Documentation, Config |

---

## Fonctionnalités complétées ✓

### Infrastructure
- ✓ Projet Flight PHP avec routing complet
- ✓ Database MySQL avec 10 tables et foreign keys
- ✓ PdoWrapper pour requêtes sécurisées
- ✓ Autoloading PSR-4 avec Composer
- ✓ Middleware de sécurité (CSP, headers)
- ✓ Configuration d'environnement

### Interface utilisateur
- ✓ Layout responsive Bootstrap 5.3.2
- ✓ Navigation principale avec header/footer
- ✓ CSS personnalisé cohérent
- ✓ Icons intégrées
- ✓ Design moderne et fonctionnel

### Gestion des données (CRUD complets)
- ✓ Villes (5 entrées)
- ✓ Types de besoins (11 types, 3 catégories)
- ✓ Donateurs (4 entrées)
- ✓ Dons (9 entrées: produits + donations argent)
- ✓ Besoins par ville (14 entrées)
- ✓ Distributions (cascade delete configuré)
- ✓ Achats et Simulations

### Modules spécialisés
- ✓ Dashboard avec statistiques et graphiques
- ✓ Module Simulation pour planifier les achats
- ✓ Module Achats avec calcul automatique (frais 10%)
- ✓ Récapitulation avec taux de couverture
- ✓ Todolist exportable en PDF

---

## Travaux restants (optionnels)

| Tâche | Priorité |
|-------|----------|
| Authentification utilisateur | Basse |
| Rôles & permissions | Basse |
| Graphiques interactifs | Moyenne |
| Notifications email | Basse |
| Recherche avancée | Basse |
| Tests automatisés | Moyenne |
| Documentation API | Basse |

---

## Points clés de la base de données

**Tables principales:**
- `ville` - Localités
- `categorie_besoin` - Catégories (Nature, Matériaux, Argent)
- `type_besoin` - Types de besoins avec prix unitaire
- `donateur` - Contributeurs
- `don` - Dons effectués avec suivi quantité
- `besoin_ville` - Besoins locaux
- `distribution` - Distributions réalisées
- `achat` - Achats simulés/réalisés

**Contraintes appliquées:**
- Foreign keys sécurisées
- Cascade delete pour distributions
- Validations prix/quantités

---

## Tests et validation

**Données de test:**
- 5 villes avec 14 besoins total
- 9 dons (produits + donations argent > 70M Ar)
- Dépense test: 12 kg Riz distribué

**Fonctionnement vérifié:**
- ✓ Tous les CRUD opérationnels
- ✓ Calculs automatiques fonctionnels
- ✓ Dashboard et statistiques correctes
- ✓ Sécurité headers appliquée
- ✓ Export PDF de la todolist


---

# BNGRC - Todolist du Projet

**Projet:** Système de Gestion des Dons pour Sinistrés  
**Équipe:** Voara (004587), Samuel (003889), Lionel (003972)  
**Dernière mise à jour:** 16 Février 2026

---

## Répartition des tâches

**Lionel (003972):** Développement côté serveur
- Architecture PHP/Flight
- Base de données MySQL
- Logique métier et calculs
- API endpoints et routing
- Middleware de sécurité

**Samuel (003889):** Debug et affichage
- Correction des bugs
- Interface utilisateur
- Styling et CSS
- Tests et validation
- Affichage des données

**Voara (004587):** Disposition du code
- Structure MVC
- Organisation des dossiers
- Nomenclature et conventions
- Documentation code
- Configuration générale

---

## État d'avancement global

**Complétude:** ~95%
- Architecture MVC fonctionnelle
- Base de données structurée
- Toutes les entités CRUD complètes
- Interface utilisateur responsive
- Sécurité et authentification

---

## Fonctionnalités complétées

### Infrastructure & Configuration
- OK (Lionel) Projet Flight PHP avec routing complet
- OK (Lionel) Database MySQL avec LAMPP (socket Unix)
- OK (Lionel) PdoWrapper pour gestion des requêtes
- OK (Voara) Autoloading PSR-4 avec Composer
- OK (Lionel) Configuration d'environnement (.env)
- OK (Lionel) Middleware de sécurité (CSP, headers)

### Interface Utilisateur (UI/UX)
- OK (Voara) Layout principal avec header/footer
- OK (Samuel) Navigation responsive (Bootstrap 5.3.2)
- OK (Samuel) Styling personnalisé (600+ lignes CSS)
- OK (Samuel) Icons Bootstrap Icons (CDN)
- OK (Samuel) Google Fonts intégrées
- OK (Samuel) Favicon créé
- OK (Samuel) Design cohérent across all pages

### Gestion des Villes
- OK (Samuel) Listing des villes
- OK (Lionel) Création nouvelle ville
- OK (Lionel) Édition ville
- OK (Lionel) Suppression ville
- OK (Samuel) Validation des champs
- OK (Voara) Pagination/tri

### Gestion des Types de Besoin
- OK (Samuel) Listing des types (3 catégories: Nature, Matériaux, Argent)
- OK (Lionel) Création nouveau type
- OK (Lionel) Édition type
- OK (Lionel) Suppression type
- OK (Lionel) Prix unitaire géré

### Gestion des Donateurs
- OK (Samuel) Listing des donateurs
- OK (Lionel) Création nouveau donateur
- OK (Lionel) Édition donateur
- OK (Lionel) Suppression donateur
- OK (Samuel) Support donations anonymes

### Gestion des Dons
- OK (Samuel) Listing dons avec status (En attente, Partiel, Distribué)
- OK (Lionel) Création don
- OK (Lionel) Suppression don
- OK (Lionel) Calcul valeur automatique (quantité × prix unitaire)
- OK (Samuel) Tracking quantité distribuée
- OK (Lionel) 9 dons dans la base (5 produits + 4 donations argent)

### Gestion des Distributions
- OK (Samuel) Listing distributions avec détails
- OK (Lionel) Création distribution avec dropdown dynamique
- OK (Lionel) Dropdown "Besoin à satisfaire" chargé depuis DB
- OK (Lionel) Suppression distribution avec cascade
- OK (Samuel) Validation budget
- OK (Lionel) 1 distribution test (12 kg Riz à Antsirabe)

### Module Simulation
- OK (Samuel) Interface de simulation (planning des achats)
- OK (Lionel) Sélection don argent avec budget disponible
- OK (Lionel) Sélection ville et chargement besoins dynamique
- OK (Samuel) Quantité/prix input avec validation
- OK (Samuel) Affichage budget disponible/restant
- OK (Lionel) Calcul montant base + frais (10%)
- OK (Samuel) Bouton "Charger les besoins" fonctionnel
- OK (Samuel) Tableau des articles à acheter
- OK (Lionel) Validation montant total ≤ budget

### Module Achats  
- OK (Samuel) Formulaire "Nouvel achat"
- OK (Lionel) Dropdown dons argent avec budget
- OK (Samuel) Sélection type besoin
- OK (Samuel) Input quantité avec validation
- OK (Lionel) Calcul automatique base + frais (10%)
- OK (Samuel) Listing des achats effectués
- OK (Lionel) Suppression achat
- OK (Samuel) Validation budget insuffisant

### Dashboard & Analytics
- OK (Samuel) Dashboard principal avec 4 stat cards
- OK (Lionel) Statistiques globales (dons totaux, besoins, distributions)
- OK (Samuel) Tableau besoins par ville avec taux couverture
- OK (Samuel) Barres de progression
- OK (Lionel) Page Récapitulation avec AJAX
- OK (Lionel) Calcul pourcentages couverture
- OK (Samuel) Besoins par ville
- OK (Samuel) Besoins restants par catégorie

### Base de données
- OK (Lionel) Table `ville` (5 villes)
- OK (Lionel) Table `categorie_besoin` (3 catégories)
- OK (Lionel) Table `type_besoin` (11 types)
- OK (Lionel) Table `donateur` (4 donateurs)
- OK (Lionel) Table `don` (9 dons)
- OK (Lionel) Table `besoin_ville` (14 besoins)
- OK (Lionel) Table `distribution` (1 distribution test)
- OK (Lionel) Table `simulation` (pour planification achats)
- OK (Lionel) Table `achat` (pour achats réalisés)
- OK (Lionel) Table `config_frais` (frais achat 10%)
- OK (Lionel) Foreign keys et contraintes

### Conversion de données
- OK (Lionel) Collection → Array conversion (DataConverter utility)
- OK (Lionel) JSON encoding pour API endpoints
- OK (Samuel) Gestion NULL values dans calculs

### Sécurité
- OK (Lionel) Content-Security-Policy (CSP) configurée
- OK (Lionel) Referrer-Policy stricte
- OK (Lionel) Headers de sécurité
- OK (Lionel) HTTPS allowed
- OK (Lionel) LocalHost autorisé en dev
- OK (Lionel) External CDNs whitelisted

---

## Travaux en cours / À finaliser

### Améliorations possibles
- A FAIRE (Lionel) Authentification utilisateur complète
- A FAIRE (Voara) Rôles & permissions (Admin, Donateur, Viewer)
- A FAIRE (Samuel) Export PDF/Excel pour rapports
- A FAIRE (Samuel) Graphiques interactifs (Chart.js)
- A FAIRE (Lionel) Notifications email
- A FAIRE (Lionel) Historique des modifications (audit trail)
- A FAIRE (Samuel) Recherche avancée multi-champs
- A FAIRE (Voara) Pagination pour longs listes
- A FAIRE (Samuel) Upload fichiers (photos, documents)
- A FAIRE (Lionel) API REST complète (endpoints OpenAPI)

### Tests
- A FAIRE (Lionel) Unit tests PHP
- A FAIRE (Lionel) Integration tests (PHPUnit)
- A FAIRE (Samuel) E2E tests (Selenium/Cypress)
- A FAIRE (Lionel) Test de charge
- A FAIRE (Samuel) Accessibilité (WCAG)

### Documentation
- OK (Voara) README.md principal
- OK (Voara) Code comments
- A FAIRE (Lionel) Documentation API Swagger
- A FAIRE (Samuel) Tutoriels vidéo
- A FAIRE (Voara) Guide utilisateur

---

## Problèmes connus / Résolus

### Résolus
- OK (Lionel) PDOException #2002 - Socket Unix `/opt/lampp/var/mysql/mysql.sock`
- OK (Lionel) Collection-to-array conversions (json_decode pattern)
- OK (Lionel) Distribution form dropdown vide (besoin IDs hardcodés)
- OK (Lionel) Simulation "Charger besoins" non-fonctionnel
- OK (Lionel) CSP blocking external resources
- OK (Samuel) Dashboard pourcentages affichant 0% (maintenant correct)
- OK (Voara) Model file naming (PSR-4)
- OK (Samuel) Tracy debugger bugs

### Cosmétiques non-bloquants
- OK (Lionel) Clés numérotées en doublon dans JSON (Flight Collection artifact)
- OK (Samuel) Quelques CSS warnings de Bootstrap (non-blocking)

---

## Structure du projet

```
/home/itu/Documents/Projet-final-trinome/
├── app/
│   ├── commands/           # CLI commands
│   ├── config/             # Routes, services, bootstrap
│   ├── controllers/        # MVC Controllers
│   ├── middlewares/        # Security middleware
│   ├── models/             # Database models
│   ├── utils/              # Utilities (DataConverter)
│   └── views/              # PHP templates
│       ├── components/     # header, footer
│       ├── dashboard/      # Dashboard pages
│       ├── achat/          # Purchase module
│       ├── simulation/     # Simulation module
│       └── ... (autres modules)
├── public/
│   ├── index.php           # Entry point
│   ├── css/style.css       # Custom styling
│   ├── bootstrap/          # Offline Bootstrap
│   └── favicon.ico
├── vendor/                 # Composer dependencies
├── database.sql            # Database schema
├── composer.json           # Dependencies
├── .env                    # Environment config
└── README.md               # Project documentation
```

---

## Déploiement

### Prérequis
- PHP 8.3+
- MySQL 5.7+
- Composer
- LAMPP (recommandé)

### Installation locale
```bash
cd /home/itu/Documents/Projet-final-trinome
composer install
php -S localhost:8000 -t public/
```

### Base de données
```bash
mysql -u root -S /opt/lampp/var/mysql/mysql.sock bngrc_dons < database.sql
```

---

## Statistiques de test

**Données actuelles:**
- Villes: 5 (3 avec besoins)
- Besoins: 14 (total demand: 14,595 unités)
- Dons: 9 (5 produits + 4 donations argent = 50M+ Ar)
- Distributions: 1 test (12 kg Riz)
- Couverture: 0.08% (12/14,595 units reçus)

**Calculs de test:**
- Total besoins: 52.45M Ar
- Total dons: 73.6M Ar  
- Distribué: 24K Ar
- Manquant: 52.4M Ar

---

## Notes développement

### Technologie utilisée
- Framework: Flight PHP
- Database: MySQL 5.7
- Frontend: Bootstrap 5.3.2, Custom CSS
- Build: Composer (PSR-4 Autoloading)
- Middleware: Security headers, CSP

### Patterns utilisés
- MVC (Model-View-Controller)
- Service Container (Dependency Injection)
- Collection-to-Array conversion
- AJAX pour données dynamiques
- Middleware pipeline

### Prochains pas recommandés
1. Ajouter authentification utilisateur
2. Implémenter rôles & permissions
3. Ajouter tests unitaires
4. Créer API REST endpoints
5. Ajouter notifications email
6. Mettre en place search avancée
7. Ajouter export PDF

---

## Checklist de fin de sprint

- OK (Lionel/Samuel) Tous les CRUD créés
- OK (Samuel) UI/UX responsive fonctionnelle
- OK (Lionel) Base de données structurée
- OK (Lionel) Routes configurées
- OK (Lionel) Sécurité middleware
- OK (Lionel) Calculs pourcentages
- OK (Lionel) Conversion données propre
- OK (Samuel) Dashboard complet
- OK (Samuel) Tests manuels passés
- A FAIRE (Lionel) Documentation API
- A FAIRE (Lionel) Tests automatisés
- A FAIRE (Lionel/Samuel) Déploiement production

---

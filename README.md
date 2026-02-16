# BNGRC - Système de Gestion des Dons pour Sinistrés

Bienvenue dans le projet **BNGRC** (Bureau National de Gestion des Ressources Catastrophe), un système web complet de gestion des dons et distributions pour les sinistrés. Ce projet est développé avec **Flight PHP** et **MySQL**.

## À propos du projet

BNGRC est une application web moderne et responsive qui permet de :

- **Gérer les donations** : Enregistrer les dons reçus (nature, matériaux, argent)
- **Suivre les distributions** : Affecter les dons aux villes et besoins identifiés
- **Planifier les achats** : Simuler et valider les achats avec les donations en argent
- **Analyser les données** : Visualiser la couverture des besoins et l'état des distributions
- **Gérer les entités** : Villes, donateurs, types de besoins, distributions

## Équipe de développement

| Nom | Rôle | Email |
|-----|------|-------|
| **Voara** (004587) | Disposition du code & Architecture | voaraandriantsitohaina30@gmail.com |
| **Samuel** (003889) | Debug & Interface utilisateur | samuelfortunat4@gmail.com |
| **Lionel** (003972) | Développement serveur & BD | lionelmendrika8@gmail.com |

## Technologies utilisées

- **Backend** : PHP 8.3 + Flight PHP framework
- **Base de données** : MySQL 5.7 (via LAMPP)
- **Frontend** : Bootstrap 5.3.2, CSS personnalisé
- **Gestion des paquets** : Composer
- **Architecture** : MVC (Model-View-Controller)
- **Sécurité** : Content-Security-Policy, Headers de sécurité

## Installation et démarrage

### Prérequis

- PHP 8.3 ou plus récent
- MySQL 5.7 ou plus récent
- Composer
- LAMPP (recommandé pour développement local)

### Installation

1. **Cloner le projet**
```bash
git clone https://github.com/Samuel-fort/Projet-final-trinome.git
cd Projet-final-trinome
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configurer la base de données**
```bash
mysql -u root -S /opt/lampp/var/mysql/mysql.sock bngrc_dons < database.sql
```

4. **Configurer l'environnement** (si nécessaire)
Éditez `.env` avec vos paramètres locaux

5. **Démarrer le serveur**
```bash
php -S localhost:8000 -t public/
```

6. **Accéder à l'application**
Ouvrez votre navigateur sur `http://localhost:8000`

### Avec Docker (optionnel)

```bash
docker-compose up -d
```

Puis accédez à `http://localhost:8000`

### Avec Vagrant (optionnel)

```bash
vagrant up
```

## Structure du projet

```
Projet-final-trinome/
├── app/
│   ├── commands/              # Commandes CLI
│   ├── config/                # Routes, services, configuration
│   ├── controllers/           # Contrôleurs MVC
│   │   ├── DashboardController.php
│   │   ├── DistributionController.php
│   │   ├── SimulationController.php
│   │   ├── AchatController.php
│   │   └── ... (autres contrôleurs)
│   ├── middlewares/           # Middleware de sécurité
│   ├── models/                # Modèles de données
│   ├── utils/                 # Utilitaires (DataConverter)
│   └── views/                 # Templates PHP
│       ├── components/        # Composants réutilisables
│       ├── dashboard/         # Pages du tableau de bord
│       ├── achat/             # Module d'achats
│       ├── simulation/        # Module de simulation
│       └── ... (autres vues)
├── public/
│   ├── index.php              # Point d'entrée
│   ├── css/                   # Styles personnalisés
│   ├── bootstrap/             # Bootstrap offline
│   └── favicon.ico
├── vendor/                    # Dépendances Composer
├── database.sql               # Schéma de la base de données
├── composer.json              # Configuration Composer
├── docker-compose.yml         # Configuration Docker
├── Vagrantfile                # Configuration Vagrant
├── todolist.md                # Suivi des tâches
└── README.md                  # Ce fichier
```

## Fonctionnalités principales

### Tableau de bord (Dashboard)
- Vue d'ensemble des statistiques
- Cartes de synthèse (dons totaux, besoins, distributions)
- Graphiques de progression
- Besoins par ville avec taux de couverture

### Gestion des entités
- **Villes** : Listing, création, édition, suppression
- **Types de besoins** : 3 catégories (Nature, Matériaux, Argent)
- **Donateurs** : Enregistrement et gestion
- **Dons** : Suivi des donations avec statut
- **Distributions** : Affectation dynamique des dons

### Module de simulation
- Planification des achats avec donations en argent
- Calcul automatique des frais (10%)
- Validation du budget
- Chargement dynamique des besoins par ville

### Module d'achats
- Formulaire de création d'achats
- Calcul base + frais
- Validation du budget disponible
- Listing et suppression

## Documentation supplémentaire

- **[Todolist](todolist.md)** : Suivi des tâches par personne et statut
- **[Schema Base de données](database.sql)** : Structure complète des tables
- **Routes API** : Endpoints AJAX pour les données dynamiques

## État du projet

**Complétude :** ~95%

**Statut :** Production-Ready pour MVP

**Dernière mise à jour :** 16 Février 2026

### Tâches complétées
- Architecture MVC fonctionnelle
- Interface responsive avec Bootstrap
- Toutes les fonctionnalités CRUD
- Dashboard avec statistiques
- Modules simulation et achats
- Sécurité (CSP, headers)

### À faire
- Authentification utilisateur complète
- Rôles et permissions
- Export PDF/Excel
- Tests automatisés
- Documentation API Swagger

## Configuration de la base de données

La base de données `bngrc_dons` contient les tables suivantes :

| Table | Description | Enregistrements |
|-------|-------------|-----------------|
| `ville` | Villes sinistrées | 5 |
| `categorie_besoin` | Catégories de besoins | 3 |
| `type_besoin` | Types de besoins détaillés | 11 |
| `donateur` | Donateurs enregistrés | 4+ |
| `don` | Donations reçues | 9+ |
| `besoin_ville` | Besoins par ville | 14+ |
| `distribution` | Distributions effectuées | 1+ |
| `simulation` | Simulations en cours | Var |
| `achat` | Achats validés | Var |
| `config_frais` | Configuration (frais 10%) | 1 |

## Déploiement

### Pour développement local
```bash
php -S localhost:8000 -t public/
```

### Pour production
1. Configurer un serveur web (Apache/Nginx)
2. Mettre à jour la configuration du VirtualHost
3. Protéger les fichiers sensibles (.env, config.php)
4. Mettre à jour le CSP si nécessaire
5. Configurer la base de données en production

## Sécurité

- Content-Security-Policy (CSP) configurée
- Headers de sécurité activés
- Referrer-Policy stricte
- Validation des données côté serveur
- Protection CSRF via middleware

## Support et contact

Pour toute question ou problème :
- **Email équipe** : voaraandriantsitohaina30@gmail.com
- **Repository GitHub** : [Samuel-fort/Projet-final-trinome](https://github.com/Samuel-fort/Projet-final-trinome)
- **Consulter** : [Todolist](todolist.md) pour l'état des tâches

## Licence

Ce projet est développé par VSL.

---

**Bon développement! 🚀**

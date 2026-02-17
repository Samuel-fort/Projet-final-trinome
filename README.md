# BNGRC - Système de Gestion des Dons pour Sinistrés

Système web complet de gestion des dons et distributions pour les sinistrés. Développé avec **Flight PHP** et **MySQL**.

## 📋 Vue d'ensemble

BNGRC permet de:
- ✅ Gérer les donations (nature, matériaux, argent)
- ✅ Suivre les distributions (villes, besoins)
- ✅ Planifier les achats avec simulations
- ✅ Analyser les données et couverture des besoins
- ✅ Gérer les entités (villes, donateurs, types de besoins)

---

## 👥 Équipe

| Nom | Rôle |
|-----|------|
| **Voara** (004587) | Architecture & Organisation |
| **Samuel** (003889) | Debug & Interface |
| **Lionel** (003972) | Backend & Base de Données |

---

## 🛠️ Technologies

- **Backend:** PHP 7.4+ / Flight PHP
- **Base de données:** MySQL 5.7+
- **Frontend:** Bootstrap 5.3.2
- **Gestion paquets:** Composer
- **Architecture:** MVC

---

## 🚀 Installation Locale

### Prérequis
- PHP 7.4+
- MySQL 5.7+
- Composer

### Étapes

```bash
# 1. Cloner et installer
git clone https://github.com/Samuel-fort/Projet-final-trinome.git
cd Projet-final-trinome
composer install

# 2. Créer la base de données
mysql -u root < database.sql

# 3. Configurer (copier et éditer config)
cp app/config/config_sample.php app/config/config.php

# 4. Lancer le serveur
php -S localhost:8000 -t public/
```

Ouvrez: `http://localhost:8000`

---

## 🌐 Déploiement Production

**Serveur:** 172.16.7.97  
**URL:** http://172.16.7.97/ETU003889/Projet-final-trinome  
**Dossier:** /home/ETU003889/public_html/Projet-final-trinome

### Étapes Rapides

1. **Uploader les fichiers** via FTP (déjà fait)

2. **Créer la base de données** via phpMyAdmin:
   - Créer: `bngrc_dons`
   - Importer: `database.sql`

3. **Configurer** `app/config/config.php`:
   ```php
   define('DB_USER', 'ETU003889');
   define('DB_PASS', 'votre_password');
   define('DB_NAME', 'bngrc_dons');
   ```

4. **Lancer l'installation**:
   ```bash
   bash deploy.sh
   ```

5. **Tester**: http://172.16.7.97/ETU003889/Projet-final-trinome

**Documentation complète:** Consultez [DEPLOYEMENT.md](DEPLOYEMENT.md)

---

## 📁 Structure

```
Projet-final-trinome/
├── public/                    ← Dossier web
│   ├── index.php
│   ├── .htaccess
│   ├── css/ et js/
├── app/
│   ├── config/                ← À configurer
│   ├── controllers/
│   ├── models/
│   ├── views/
├── vendor/                    ← Dépendances
├── database.sql
├── deploy.sh                  ← Script installation
├── DEPLOYEMENT.md             ← Guide détaillé
└── README.md                  ← Ce fichier
```

---

## 🎯 Fonctionnalités

- **Dashboard:** Statistiques et vue d'ensemble
- **Dons:** Enregistrement et suivi
- **Distributions:** Affectation aux villes/besoins
- **Simulations:** Planification des achats
- **Achats:** Gestion avec budget
- **Gestion entités:** Villes, donateurs, types de besoins

---

## 🔧 Configuration

### Prérequis Serveur
- PHP 7.4+
- MySQL 5.7+
- Apache avec mod_rewrite

### Fichiers Clés
- `app/config/config.php` - Identifiants MySQL
- `public/.htaccess` - Réécriture d'URL
- `database.sql` - Schéma BD

### Permissions
```bash
chmod 755 public/
chmod 644 app/config/config.php
```

---

## 🐛 Résolution Problèmes

| Erreur | Solution |
|--------|----------|
| Cannot connect to MySQL | Vérifiez identifiants dans config.php |
| 404 Not Found | Activez mod_rewrite, vérifiez .htaccess |
| Permission denied | chmod -R 755 /path/to/projet |
| Page blanche | Activez DEBUG_MODE dans config.php |

---

## 📚 Documentation

- **[DEPLOYEMENT.md](DEPLOYEMENT.md)** - Guide complet de déploiement
- **[deploy.sh](deploy.sh)** - Script d'installation automatique
- **[database.sql](database.sql)** - Schéma de la base de données
- **[todolist.md](todolist.md)** - Suivi des tâches

---

## ✅ Checklist Déploiement

- [ ] Fichiers uploadés via FTP
- [ ] Base de données `bngrc_dons` créée et importée
- [ ] `app/config/config.php` configuré
- [ ] Permissions correctes (755/644)
- [ ] `.htaccess` dans `public/`
- [ ] URL accessible sans erreur
- [ ] Dashboard fonctionne
- [ ] Toutes les pages accessibles

---

## 🔒 Sécurité

- Headers de sécurité activés
- Middleware de sécurité
- Validation des données
- Protections CORS

En production: désactivez DEBUG_MODE et activez HTTPS.

---

## 📞 Support

Consultez [DEPLOYEMENT.md](DEPLOYEMENT.md) pour l'aide complète.

---

**Version:** 1.0

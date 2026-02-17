# Guide de Déploiement - BNGRC Projet Final

##  Informations du Serveur
- **IP:** 172.16.7.97
- **URL d'accès:** http://172.16.7.97/ETU003889/Projet-final-trinome
- **Chemin:** /home/ETU003889/public_html/Projet-final-trinome
- **Username:** ETU003889
- **Version PHP:** 7.4+ ou 8.0+
- **Base de données:** MySQL 5.7+

---

##  Étapes de Déploiement

### 1️ Téléversement des fichiers
 **DÉJÀ FAIT via FileZilla**
- Les fichiers du projet ont été envoyés dans: `/Projet-final-trinome`
- Le dossier `public` doit être accessible via le web

### 2️⃣ Configuration du Serveur Web

#### Pour Apache (.htaccess)
Assurez-vous que `.htaccess` est activé et créez/vérifiez le fichier `public/.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

#### Pour Nginx (configuration serveur)
```nginx
location ~ \.php$ {
    fastcgi_pass unix:/var/run/php-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}

location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 3️⃣ Configuration de la Base de Données

#### Créer la base de données
```sql
-- Connectez-vous à MySQL avec vos identifiants de hosting
-- Puis exécutez:
SOURCE /home/ETU003889/public_html/Projet-final-trinome/database.sql;
```

**OU** exécutez le script d'installation:
```bash
bash deployement.sh
```

### 4️⃣ Configuration de l'Application

#### A. Installer les dépendances Composer
```bash
cd /home/ETU003889/public_html/Projet-final-trinome
composer install
```

#### B. Configurer la connexion MySQL
Modifiez `app/config/config.php`:

```php
// Database Configuration
define('DB_HOST', 'localhost');      // Généralement 'localhost'
define('DB_USER', 'ETU003889');       // Votre user MySQL
define('DB_PASS', 'YourPassword');    // Votre mot de passe MySQL
define('DB_NAME', 'bngrc_dons');      // Nom de la base de données
```

> ⚠️ **Important:** Si vous recevez une erreur de connexion MySQL:
> - Vérifiez que MySQL est en cours d'exécution
> - Confirmez votre nom d'utilisateur et mot de passe
> - Assurez-vous que la base de données `bngrc_dons` existe

#### C. Configurer l'URL de base (optionnel)
Modifiez dans `app/config/config.php`:

```php
$app->set('flight.base_url', '/ETU003889/Projet-final-trinome/public/');
```

### 5️⃣ Permissions des Dossiers

Les dossiers suivants doivent avoir les bonnes permissions:

```bash
# Définir les permissions (à exécuter dans le répertoire du projet)
chmod 755 public/
chmod 755 app/
chmod 755 app/views/
chmod 755 app/config/
```

### 6️⃣ Vérifier l'Installation

Accédez à: **http://172.16.7.97/ETU003889/Projet-final-trinome**

Vous devriez voir:
- ✅ La page d'accueil du Dashboard
- ✅ La liste des villes, dons, distributions, etc.
- ✅ Aucune erreur PHP

---

## 🔧 Configuration Avancée

### Activer le mode Debug (développement)
Dans `app/config/config.php`:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Désactiver le mode Debug (production)
```php
error_reporting(0);
ini_set('display_errors', 0);
```

### Extensions PHP Requises
Vérifiez que ces extensions sont activées:
- ✅ `php-pdo`
- ✅ `php-mysql` ou `php-mysqli`
- ✅ `php-json`
- ✅ `php-curl` (optionnel)

---

## 🐛 Résolution des Problèmes

### Erreur: "Cannot connect to MySQL"
1. Vérifiez que MySQL est en cours d'exécution
2. Confirmez les identifiants dans `config.php`
3. Assurez-vous que l'utilisateur MySQL a accès à `bngrc_dons`

### Erreur: "404 Not Found" sur toutes les pages
1. Vérifiez que Apache a `mod_rewrite` activé
2. Vérifiez que `.htaccess` est dans le dossier `public/`
3. Assurez-vous que la `base_url` est correctement configurée

### Erreur: "Permission denied"
```bash
chmod -R 755 /home/ETU003889/public_html/Projet-final-trinome/
chmod -R 755 /home/ETU003889/public_html/Projet-final-trinome/public/
```

### Erreur: "Composer not found"
```bash
# Installer Composer globalement
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
```

---

## 📝 Structure du Projet

```
Projet-final-trinome/
├── public/
│   ├── index.php          ← Point d'entrée
│   ├── .htaccess          ← Réécriture URL
│   ├── css/
│   ├── js/
│   └── bootstrap/
├── app/
│   ├── config/
│   │   ├── config.php     ← À CONFIGURER
│   │   ├── routes.php
│   │   ├── bootstrap.php
│   │   └── services.php
│   ├── controllers/
│   ├── models/
│   ├── views/
│   ├── middlewares/
│   └── utils/
├── vendor/                ← Dépendances Composer
├── database.sql           ← Script SQL
├── composer.json
├── deployement.sh         ← Script d'installation
└── DEPLOYEMENT.md         ← Ce fichier
```

---

## ✅ Checklist de Déploiement

- [ ] Fichiers téléchargés via FileZilla
- [ ] Base de données `bngrc_dons` créée
- [ ] `app/config/config.php` configuré avec les identifiants MySQL
- [ ] Dépendances Composer installées (`composer install`)
- [ ] Permissions des dossiers correctes (`chmod 755`)
- [ ] Apache/Nginx configuré avec réécriture d'URL
- [ ] `.htaccess` présent dans `public/`
- [ ] URL d'accès fonctionne: http://172.16.7.97/ETU003889/Projet-final-trinome
- [ ] Dashboard affiche sans erreur
- [ ] Toutes les pages sont accessibles

---

## 📞 Support

Si vous rencontrez des problèmes:
1. Vérifiez les logs Apache/Nginx
2. Vérifiez les logs PHP
3. Consultez la section "Résolution des Problèmes"
4. Vérifiez que tous les fichiers `.php` sont présents
5. Assurez-vous que MySQL est actif et accessible

---


**Version:** 1.0

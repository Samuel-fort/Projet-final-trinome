#!/bin/bash

################################################################################
#                  SCRIPT DE DÉPLOIEMENT - BNGRC PROJET FINAL                  #
#                                                                              #
# Ce script automatise les étapes de déploiement sur le serveur               #
# Usage: bash deployement.sh                                                  #
################################################################################

# Couleurs pour l'affichage
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Variables
PROJECT_DIR=$(pwd)
APP_CONFIG="$PROJECT_DIR/app/config/config.php"
DATABASE_SQL="$PROJECT_DIR/database.sql"
PUBLIC_DIR="$PROJECT_DIR/public"

################################################################################
# Fonctions
################################################################################

print_header() {
    echo -e "\n${BLUE}=========================================${NC}"
    echo -e "${BLUE}$1${NC}"
    echo -e "${BLUE}=========================================${NC}\n"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_info() {
    echo -e "${BLUE}ℹ $1${NC}"
}

################################################################################
# Vérifications préalables
################################################################################

print_header "VÉRIFICATIONS PRÉALABLES"

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "$PROJECT_DIR/composer.json" ]; then
    print_error "Impossible de trouver composer.json"
    print_info "Assurez-vous d'être dans le répertoire racine du projet"
    exit 1
fi
print_success "Localisation du projet vérifiée"

# Vérifier PHP
if ! command -v php &> /dev/null; then
    print_error "PHP n'est pas installé"
    exit 1
fi
PHP_VERSION=$(php -v | head -n 1)
print_success "PHP détecté: $PHP_VERSION"

# Vérifier Composer
if ! command -v composer &> /dev/null; then
    print_warning "Composer n'est pas installé globalement"
    print_info "Tentative d'installation de Composer..."
    if [ -f "composer.phar" ]; then
        COMPOSER_CMD="php composer.phar"
        print_success "Utilisation de composer.phar local"
    else
        print_error "Composer introuvable et composer.phar non trouvé"
        print_info "Veuillez installer Composer: https://getcomposer.org"
        exit 1
    fi
else
    COMPOSER_CMD="composer"
    print_success "Composer détecté"
fi

# Vérifier MySQL/MariaDB
if ! command -v mysql &> /dev/null; then
    print_warning "Client MySQL/MariaDB non détecté"
    print_info "Vous devrez créer la base de données manuellement"
    MYSQL_AVAILABLE=false
else
    print_success "Client MySQL détecté"
    MYSQL_AVAILABLE=true
fi

################################################################################
# Installation des dépendances
################################################################################

print_header "INSTALLATION DES DÉPENDANCES COMPOSER"

if [ ! -d "$PROJECT_DIR/vendor" ]; then
    print_info "Installation des dépendances..."
    $COMPOSER_CMD install --no-interaction --prefer-dist
    if [ $? -eq 0 ]; then
        print_success "Dépendances installées avec succès"
    else
        print_error "Erreur lors de l'installation des dépendances"
        exit 1
    fi
else
    print_success "Dossier vendor détecté, mise à jour..."
    $COMPOSER_CMD update --no-interaction --prefer-dist
fi

################################################################################
# Configuration des permissions
################################################################################

print_header "CONFIGURATION DES PERMISSIONS"

# Dossiers à sécuriser
DIRS_TO_CHMOD=(
    "$PROJECT_DIR/public"
    "$PROJECT_DIR/app"
    "$PROJECT_DIR/app/views"
    "$PROJECT_DIR/app/config"
    "$PROJECT_DIR/app/controllers"
    "$PROJECT_DIR/app/models"
)

for dir in "${DIRS_TO_CHMOD[@]}"; do
    if [ -d "$dir" ]; then
        chmod 755 "$dir"
        print_success "Permissions définies pour: $dir"
    fi
done

# Fichiers importants
if [ -f "$APP_CONFIG" ]; then
    chmod 644 "$APP_CONFIG"
    print_success "Permissions définies pour config.php"
fi

################################################################################
# Configuration de la base de données
################################################################################

print_header "CONFIGURATION DE LA BASE DE DONNÉES"

if [ "$MYSQL_AVAILABLE" = true ]; then
    print_info "MySQL est disponible. Souhaitez-vous créer la base de données maintenant?"
    print_info "Note: Vous aurez besoin des identifiants MySQL du serveur"
    echo -e "${YELLOW}Continuer? (y/n)${NC} "
    read -r response
    
    if [ "$response" = "y" ] || [ "$response" = "Y" ]; then
        echo -e "${YELLOW}Entrez le nom d'hôte MySQL (défaut: localhost):${NC} "
        read -r db_host
        db_host=${db_host:-localhost}
        
        echo -e "${YELLOW}Entrez l'utilisateur MySQL (défaut: root):${NC} "
        read -r db_user
        db_user=${db_user:-root}
        
        echo -e "${YELLOW}Entrez le mot de passe MySQL (laisser vide si aucun):${NC} "
        read -rs db_pass
        echo ""
        
        print_info "Création de la base de données..."
        
        if [ -z "$db_pass" ]; then
            mysql -h "$db_host" -u "$db_user" < "$DATABASE_SQL"
        else
            mysql -h "$db_host" -u "$db_user" -p"$db_pass" < "$DATABASE_SQL"
        fi
        
        if [ $? -eq 0 ]; then
            print_success "Base de données créée avec succès"
        else
            print_error "Erreur lors de la création de la base de données"
            print_warning "Vous devrez importer database.sql manuellement"
        fi
    fi
else
    print_warning "MySQL n'est pas disponible en ligne de commande"
    print_info "Vous devrez importer $DATABASE_SQL manuellement via phpMyAdmin ou autre"
fi

################################################################################
# Configuration de l'application
################################################################################

print_header "CONFIGURATION DE L'APPLICATION"

if [ ! -f "$APP_CONFIG" ]; then
    print_warning "Fichier config.php non trouvé"
    print_info "Copie de config_sample.php..."
    
    if [ -f "$PROJECT_DIR/app/config/config_sample.php" ]; then
        cp "$PROJECT_DIR/app/config/config_sample.php" "$APP_CONFIG"
        print_success "Fichier de configuration créé"
    else
        print_error "Fichier config_sample.php non trouvé"
    fi
fi

print_warning "⚠ IMPORTANT: Configuration manuelle requise ⚠"
print_info "Éditez le fichier: app/config/config.php"
echo ""
print_info "Points à configurer:"
echo "  1. DB_HOST - Adresse du serveur MySQL (généralement: localhost)"
echo "  2. DB_USER - Nom d'utilisateur MySQL"
echo "  3. DB_PASS - Mot de passe MySQL"
echo "  4. DB_NAME - Nom de la base de données (défaut: bngrc_dons)"
echo "  5. flight.base_url - URL de base si le projet est dans un sous-dossier"
echo ""

################################################################################
# Vérification du fichier .htaccess
################################################################################

print_header "CONFIGURATION DE LA RÉÉCRITURE D'URL"

if [ -f "$PUBLIC_DIR/.htaccess" ]; then
    print_success "Fichier .htaccess détecté"
else
    print_warning "Fichier .htaccess non trouvé dans public/"
    print_info "Création du fichier .htaccess..."
    
    cat > "$PUBLIC_DIR/.htaccess" << 'EOF'
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
    
    if [ $? -eq 0 ]; then
        print_success "Fichier .htaccess créé"
    else
        print_error "Impossible de créer .htaccess"
    fi
fi

################################################################################
# Résumé et prochaines étapes
################################################################################

print_header "DÉPLOIEMENT COMPLÉTÉ ✓"

echo ""
print_success "Étapes de déploiement automatisées terminées!"
echo ""

print_info "PROCHAINES ÉTAPES MANUELLES:"
echo "  1. Ouvrez app/config/config.php"
echo "  2. Configurez les identifiants MySQL (DB_HOST, DB_USER, DB_PASS)"
echo "  3. Assurez-vous que la base de données 'bngrc_dons' existe"
echo "  4. Vérifiez que .htaccess est dans le dossier public/"
echo "  5. Assurez-vous que Apache a mod_rewrite activé"
echo "  6. Testez l'URL: http://172.16.7.97/ETU003889/Projet-final-trinome"
echo ""

print_info "VÉRIFICATIONS RAPIDES:"
echo "  - Dépendances: $([ -d "$PROJECT_DIR/vendor" ] && echo "✓" || echo "✗")"
echo "  - Configuration: $([ -f "$APP_CONFIG" ] && echo "✓" || echo "✗")"
echo "  - .htaccess: $([ -f "$PUBLIC_DIR/.htaccess" ] && echo "✓" || echo "✗")"
echo "  - PHP: ✓"
echo ""

print_info "Pour plus d'aide, consultez: DEPLOYEMENT.md"
echo ""

exit 0

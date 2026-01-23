#!/bin/bash

# Script d'installation et configuration Domini sur serveur Linux
# Auteur: Domini Team
# Date: 2026-01-21

echo "🚀 Installation et Configuration de Domini"
echo "=========================================="
echo ""

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
success() {
    echo -e "${GREEN}✅ $1${NC}"
}

error() {
    echo -e "${RED}❌ $1${NC}"
}

warning() {
    echo -e "${YELLOW}⚠️  $1${NC}"
}

info() {
    echo -e "ℹ️  $1"
}

# Vérifier si on est root ou avec sudo
if [ "$EUID" -ne 0 ]; then 
    warning "Ce script doit être exécuté avec sudo"
    echo "Usage: sudo bash install-server.sh"
    exit 1
fi

# 1. Vérifier PHP
info "Vérification de PHP..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    success "PHP $PHP_VERSION installé"
else
    error "PHP n'est pas installé"
    exit 1
fi

# 2. Vérifier Composer
info "Vérification de Composer..."
if command -v composer &> /dev/null; then
    success "Composer installé"
else
    warning "Composer n'est pas installé. Installation..."
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    success "Composer installé"
fi

# 3. Vérifier Apache
info "Vérification d'Apache..."
if command -v apache2 &> /dev/null; then
    success "Apache installé"
else
    error "Apache n'est pas installé"
    exit 1
fi

# 4. Activer mod_rewrite
info "Activation de mod_rewrite..."
a2enmod rewrite
systemctl restart apache2
success "mod_rewrite activé"

# 5. Installer les dépendances
info "Installation des dépendances Composer..."
composer install --optimize-autoloader --no-dev
success "Dépendances installées"

# 6. Configuration du fichier .env
if [ ! -f .env ]; then
    info "Création du fichier .env..."
    cp .env.example .env
    success "Fichier .env créé"
else
    info "Fichier .env existe déjà"
fi

# 7. Générer la clé d'application
info "Génération de la clé d'application..."
php artisan key:generate
success "Clé générée"

# 8. Configurer les permissions
info "Configuration des permissions..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
chmod -R 755 public
success "Permissions configurées"

# 9. Créer le lien symbolique storage
info "Création du lien symbolique storage..."
php artisan storage:link
success "Lien symbolique créé"

# 10. Demander les informations de base de données
echo ""
echo "📊 Configuration de la Base de Données"
echo "======================================="
read -p "Nom de la base de données: " DB_NAME
read -p "Utilisateur MySQL: " DB_USER
read -sp "Mot de passe MySQL: " DB_PASS
echo ""

# Mettre à jour le .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

success "Configuration de la base de données mise à jour"

# 11. Demander si on doit créer la base de données
read -p "Voulez-vous créer la base de données? (o/n): " CREATE_DB
if [ "$CREATE_DB" = "o" ] || [ "$CREATE_DB" = "O" ]; then
    info "Création de la base de données..."
    mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    success "Base de données créée"
fi

# 12. Exécuter les migrations
read -p "Voulez-vous exécuter les migrations? (o/n): " RUN_MIGRATIONS
if [ "$RUN_MIGRATIONS" = "o" ] || [ "$RUN_MIGRATIONS" = "O" ]; then
    info "Exécution des migrations..."
    php artisan migrate --force
    success "Migrations exécutées"
fi

# 13. Exécuter les seeders
read -p "Voulez-vous exécuter les seeders (données de test)? (o/n): " RUN_SEEDERS
if [ "$RUN_SEEDERS" = "o" ] || [ "$RUN_SEEDERS" = "O" ]; then
    info "Exécution des seeders..."
    php artisan db:seed --force
    success "Seeders exécutés"
fi

# 14. Optimiser pour la production
info "Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
success "Optimisations appliquées"

# 15. Configuration Apache (optionnel)
echo ""
echo "🌐 Configuration Apache"
echo "======================="
read -p "Voulez-vous configurer un Virtual Host Apache? (o/n): " CONFIG_VHOST
if [ "$CONFIG_VHOST" = "o" ] || [ "$CONFIG_VHOST" = "O" ]; then
    read -p "Nom de domaine (ex: domini.com): " DOMAIN
    read -p "Chemin complet du projet (ex: /var/www/html/domini): " PROJECT_PATH
    
    VHOST_FILE="/etc/apache2/sites-available/$DOMAIN.conf"
    
    cat > $VHOST_FILE <<EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    ServerAlias www.$DOMAIN
    
    DocumentRoot $PROJECT_PATH/public
    
    <Directory $PROJECT_PATH/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/${DOMAIN}-error.log
    CustomLog \${APACHE_LOG_DIR}/${DOMAIN}-access.log combined
</VirtualHost>
EOF
    
    a2ensite $DOMAIN.conf
    systemctl reload apache2
    success "Virtual Host configuré pour $DOMAIN"
fi

# 16. Résumé final
echo ""
echo "================================================"
echo "✅ Installation terminée avec succès!"
echo "================================================"
echo ""
info "Informations importantes:"
echo "  - URL de l'application: http://votredomaine.com"
echo "  - URL admin: http://votredomaine.com/admin/login"
echo "  - Email admin: admin@domini.com"
echo "  - Mot de passe: password123"
echo ""
warning "N'oubliez pas de:"
echo "  1. Modifier APP_DEBUG=false dans .env (production)"
echo "  2. Configurer votre nom de domaine"
echo "  3. Installer un certificat SSL (Let's Encrypt)"
echo "  4. Changer le mot de passe admin par défaut"
echo "  5. Configurer les sauvegardes automatiques"
echo ""
success "Votre application Domini est prête! 🎉"

# 🚀 Guide de Déploiement - Domini sur Serveur Apache

## 🔧 Problème : Affichage des Dossiers et Fichiers

### Cause
Votre serveur Apache pointe vers la racine du projet (`domini/`) au lieu du dossier `public/`.

### ✅ Solution Appliquée

Un fichier `.htaccess` a été créé à la racine du projet pour rediriger automatiquement toutes les requêtes vers le dossier `public/`.

---

## 📁 Structure des Fichiers .htaccess

### 1. `.htaccess` (À LA RACINE)
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirige toutes les requêtes vers public/
    RewriteCond %{REQUEST_URI} !^/public/
    RewriteRule ^(.*)$ public/$1 [L,QSA]
    
    # Redirige vers index.php si le fichier n'existe pas
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/index.php [L,QSA]
</IfModule>

# Désactive le listage des dossiers
Options -Indexes

# Protège les fichiers sensibles
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Protège les fichiers Laravel
<FilesMatch "(artisan|composer\.json|composer\.lock|\.env|package\.json)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 2. `public/.htaccess` (Déjà présent)
Ce fichier gère les routes Laravel une fois dans le dossier public.

---

## 🔍 Vérifications à Faire

### 1. Vérifier que mod_rewrite est activé
```bash
# Sur Ubuntu/Debian
sudo a2enmod rewrite
sudo systemctl restart apache2

# Vérifier
apache2ctl -M | grep rewrite
# Devrait afficher: rewrite_module (shared)
```

### 2. Vérifier les permissions
```bash
cd /chemin/vers/domini

# Permissions des dossiers
sudo chmod -R 755 storage bootstrap/cache

# Propriétaire (remplacer www-data si nécessaire)
sudo chown -R www-data:www-data storage bootstrap/cache
```

### 3. Vérifier le fichier .env
```bash
# Copier .env.example si .env n'existe pas
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=domini_db
DB_USERNAME=votre_user
DB_PASSWORD=votre_password
```

### 4. Installer les dépendances
```bash
# Composer
composer install --optimize-autoloader --no-dev

# NPM (si nécessaire)
npm install
npm run build
```

### 5. Créer le lien symbolique pour storage
```bash
php artisan storage:link
```

### 6. Lancer les migrations
```bash
# Avec les seeders
php artisan migrate:fresh --seed

# Sans les seeders
php artisan migrate
```

---

## 🌐 Configuration Apache Optimale

### Option A : Modifier le DocumentRoot (RECOMMANDÉ)

Si vous avez accès à la configuration Apache :

**Fichier** : `/etc/apache2/sites-available/domini.conf` ou `000-default.conf`

```apache
<VirtualHost *:80>
    ServerName votredomaine.com
    ServerAlias www.votredomaine.com
    
    # IMPORTANT : Pointer vers le dossier public
    DocumentRoot /var/www/html/domini/public
    
    <Directory /var/www/html/domini/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # Logs
    ErrorLog ${APACHE_LOG_DIR}/domini-error.log
    CustomLog ${APACHE_LOG_DIR}/domini-access.log combined
</VirtualHost>
```

**Activer et redémarrer** :
```bash
sudo a2ensite domini.conf
sudo systemctl restart apache2
```

### Option B : Utiliser .htaccess (ACTUEL)

Si vous ne pouvez pas modifier la configuration Apache, le fichier `.htaccess` à la racine fait le travail.

---

## 🔒 Sécurité Importante

### 1. Protéger le fichier .env
```apache
# Déjà inclus dans .htaccess, mais vérifier
<FilesMatch "^\.env">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 2. Désactiver le mode debug en production
Dans `.env` :
```env
APP_ENV=production
APP_DEBUG=false
```

### 3. Cacher les erreurs PHP
Dans `php.ini` ou `.user.ini` :
```ini
display_errors = Off
log_errors = On
error_log = /var/log/php_errors.log
```

### 4. Optimiser pour la production
```bash
# Cache des configurations
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache
```

---

## 🐛 Dépannage

### Problème 1 : Page blanche
**Causes possibles** :
- Permissions incorrectes sur `storage/` et `bootstrap/cache/`
- Erreur PHP non affichée
- Pas de clé d'application

**Solutions** :
```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Vérifier les permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache

# Régénérer la clé
php artisan key:generate
```

### Problème 2 : Erreur 500
**Causes possibles** :
- .htaccess mal configuré
- mod_rewrite désactivé
- AllowOverride None dans Apache

**Solutions** :
```bash
# Vérifier mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2

# Vérifier AllowOverride dans la config Apache
# Doit être : AllowOverride All
```

### Problème 3 : Images/CSS ne se chargent pas
**Causes possibles** :
- Lien symbolique storage non créé
- Permissions incorrectes

**Solutions** :
```bash
# Créer le lien symbolique
php artisan storage:link

# Vérifier les permissions
sudo chmod -R 755 public/storage
```

### Problème 4 : "Route [login] not defined"
**Solution** :
```bash
# Vider le cache des routes
php artisan route:clear
php artisan cache:clear
php artisan config:clear
```

---

## 📊 Checklist de Déploiement

### Avant de mettre en ligne
- [ ] `.env` configuré avec les bonnes informations
- [ ] `APP_DEBUG=false` en production
- [ ] `APP_ENV=production`
- [ ] Base de données créée
- [ ] Migrations exécutées
- [ ] Seeders exécutés (si nécessaire)
- [ ] `composer install --optimize-autoloader --no-dev`
- [ ] `npm run build` (si assets frontend)
- [ ] `php artisan storage:link`
- [ ] Permissions correctes (755 pour dossiers, 644 pour fichiers)
- [ ] `storage/` et `bootstrap/cache/` en 775
- [ ] `.htaccess` présents (racine + public)
- [ ] mod_rewrite activé
- [ ] AllowOverride All dans la config Apache

### Optimisations
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] Compression Gzip activée
- [ ] Cache du navigateur configuré

### Sécurité
- [ ] `.env` protégé et non accessible
- [ ] Fichiers `.git` non accessibles
- [ ] Listage des dossiers désactivé
- [ ] HTTPS/SSL configuré (recommandé)
- [ ] Pare-feu configuré
- [ ] Sauvegardes automatiques configurées

---

## 🆘 Support

### Logs à vérifier en cas d'erreur
1. **Laravel** : `storage/logs/laravel.log`
2. **Apache** : `/var/log/apache2/error.log`
3. **PHP** : `/var/log/php_errors.log`

### Commandes de debug
```bash
# Vérifier la configuration Laravel
php artisan about

# Lister les routes
php artisan route:list

# Vérifier l'environnement
php artisan env

# Mode maintenance
php artisan down  # Activer
php artisan up    # Désactiver
```

---

## 🎯 Résultat Attendu

Après avoir appliqué ces configurations :

✅ **URL** : `http://votredomaine.com` → Affiche la page d'accueil Domini  
✅ **Admin** : `http://votredomaine.com/admin/login` → Page de connexion admin  
✅ **Pas de listage** de dossiers/fichiers  
✅ **Routes Laravel** fonctionnelles  
✅ **Assets** (CSS, JS, images) chargés correctement  
✅ **Sécurité** : Fichiers sensibles protégés  

---

## 📞 Contact & Ressources

- **Documentation Laravel** : https://laravel.com/docs
- **Forum Laravel** : https://laracasts.com/discuss
- **Stack Overflow** : Tag `laravel`

---

✅ **Votre application Domini est maintenant correctement déployée sur le serveur !**

📅 *Dernière mise à jour : 21 Janvier 2026*

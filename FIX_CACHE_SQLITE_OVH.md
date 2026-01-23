# 🔧 Résolution : Erreur Cache SQLite sur OVH

## ❌ Erreur Rencontrée

```
Database file at path [/home/uhkqfes/dominiweb/database/database.sqlite] does not exist.
```

---

## ✅ SOLUTION RAPIDE

Votre fichier `.env` sur le serveur OVH a `CACHE_DRIVER=database` mais SQLite n'est pas configuré.

### Via SSH (Vous y êtes déjà !) :

```bash
# 1. Ouvrir le fichier .env
nano .env

# 2. Chercher la ligne CACHE_DRIVER et la changer :
# AVANT :
CACHE_DRIVER=database

# APRÈS :
CACHE_DRIVER=file

# 3. Sauvegarder : Ctrl+X, puis Y, puis Enter

# 4. Effacer le cache
php artisan cache:clear
php artisan config:clear
```

---

## 🔍 Explication

### Pourquoi cette erreur ?

Laravel peut utiliser différents systèmes de cache :
- **file** : Stocke dans des fichiers (simple, pas de config)
- **database** : Stocke dans une base de données
- **redis** : Nécessite Redis
- **memcached** : Nécessite Memcached

Votre `.env` est configuré pour `database` mais SQLite n'est pas configuré correctement.

### Pourquoi `file` est la meilleure solution ?

Pour un hébergement mutualisé comme OVH :
- ✅ Aucune configuration supplémentaire
- ✅ Fonctionne immédiatement
- ✅ Performance correcte pour un site normal
- ✅ Pas de dépendance externe

---

## 📝 Configuration .env Complète pour OVH

Voici ce que votre `.env` devrait contenir sur le serveur :

```env
APP_NAME=Domini
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_DEBUG=false
APP_URL=http://votredomaine.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Base de données MySQL OVH
DB_CONNECTION=mysql
DB_HOST=votreserveurmysql.mysql.db    # Fourni par OVH
DB_PORT=3306
DB_DATABASE=votre_base_ovh
DB_USERNAME=votre_user_ovh
DB_PASSWORD=votre_pass_ovh

# Cache et Sessions - IMPORTANT
BROADCAST_DRIVER=log
CACHE_DRIVER=file          # ← CHANGEZ ICI
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file        # ← Et ICI aussi
SESSION_LIFETIME=7200

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

---

## 🚀 Commandes à Exécuter sur OVH

Copiez-collez ces commandes dans votre terminal SSH OVH :

```bash
# Naviguer vers votre projet
cd ~/dominiweb

# Modifier le .env
nano .env
# Changez CACHE_DRIVER=database en CACHE_DRIVER=file
# Changez SESSION_DRIVER=database en SESSION_DRIVER=file (si présent)
# Sauvegarder : Ctrl+X, Y, Enter

# Effacer tous les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Vérifier que tout fonctionne
php artisan about
```

---

## ✅ Vérifications

Après ces modifications, testez :

```bash
# Tester le cache
php artisan cache:clear
# Devrait afficher : "Cache cleared successfully"

# Tester l'optimisation
php artisan optimize
# Devrait s'exécuter sans erreur
```

Puis dans votre navigateur :
- Site : `http://votredomaine.com`
- Admin : `http://votredomaine.com/admin/login`

---

## 🔄 Alternative : Utiliser MySQL pour le Cache

Si vous VOULEZ vraiment utiliser MySQL pour le cache :

### 1. Créer la table cache

```bash
# Créer la migration cache
php artisan cache:table

# Exécuter la migration
php artisan migrate
```

### 2. Configurer le .env

```env
CACHE_DRIVER=database
DB_CONNECTION=mysql
# (garder les autres paramètres MySQL)
```

**Note :** Pour un site normal, `file` est largement suffisant et plus simple !

---

## 🆘 Autres Erreurs Possibles

### Erreur : "Permission denied" sur storage/

```bash
chmod -R 775 storage bootstrap/cache
```

### Erreur : "No such file or directory"

Vérifiez que vous êtes dans le bon dossier :
```bash
pwd
# Devrait afficher : /home/uhkqfes/dominiweb

ls -la
# Devrait montrer : artisan, .env, storage, etc.
```

### Erreur : "APP_KEY not specified"

```bash
php artisan key:generate
```

---

## 📋 Checklist OVH

- [ ] Fichier `.env` modifié : `CACHE_DRIVER=file`
- [ ] Fichier `.env` modifié : `SESSION_DRIVER=file`
- [ ] Paramètres MySQL OVH corrects dans `.env`
- [ ] `php artisan cache:clear` fonctionne
- [ ] `php artisan config:cache` fonctionne
- [ ] Site accessible dans le navigateur
- [ ] Admin accessible : `/admin/login`

---

## 🎯 Commandes Récapitulatives (Copier-Coller)

```bash
cd ~/dominiweb
nano .env
# Changez CACHE_DRIVER=database en CACHE_DRIVER=file
# Ctrl+X, Y, Enter pour sauvegarder
php artisan cache:clear
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📞 Informations OVH Utiles

### Trouver vos informations MySQL OVH

1. Connectez-vous à votre espace client OVH
2. Allez dans **Web Cloud** → **Hébergements**
3. Cliquez sur votre hébergement
4. Onglet **Bases de données**
5. Notez :
   - **Serveur** : `votreserveur.mysql.db`
   - **Nom de la base**
   - **Nom d'utilisateur**
   - **Mot de passe** (si oublié, vous pouvez le réinitialiser)

### Structure des chemins OVH

```
/home/uhkqfes/              # Votre home
/home/uhkqfes/dominiweb/    # Votre projet Laravel
/home/uhkqfes/www/          # Document root (lien vers dominiweb/public)
```

---

✅ **Après ces modifications, votre site devrait fonctionner parfaitement !**

Testez : `http://votredomaine.com` et `http://votredomaine.com/admin/login`

*Dernière mise à jour : 21 Janvier 2026*

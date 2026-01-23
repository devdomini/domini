# 🔑 Résolution : MissingAppKeyException sur Serveur

## ❌ Erreur
```
Illuminate\Encryption\MissingAppKeyException
No application encryption key has been specified.
```

## 🎯 Cause
Le fichier `.env` sur votre serveur n'a pas de clé d'application (`APP_KEY`) ou le fichier n'existe pas.

---

## ✅ Solutions (Par Ordre de Priorité)

### Solution 1 : Via SSH/Terminal (RECOMMANDÉ)

Si vous avez accès SSH à votre serveur :

```bash
# 1. Se connecter au serveur via SSH
ssh votre_user@votre_serveur.com

# 2. Aller dans le dossier du projet
cd /chemin/vers/domini

# 3. Vérifier si .env existe
ls -la .env

# Si .env n'existe pas, le créer depuis .env.example
cp .env.example .env

# 4. Générer la clé d'application
php artisan key:generate

# 5. Vérifier que la clé a été générée
cat .env | grep APP_KEY
# Devrait afficher quelque chose comme : APP_KEY=base64:xxxxxxxxxxxxxxxx

# 6. Effacer le cache
php artisan config:clear
php artisan cache:clear

# 7. Redémarrer (si nécessaire)
# Pour Apache
sudo systemctl restart apache2
# OU pour Nginx
sudo systemctl restart nginx
```

**✅ C'est fait ! Votre site devrait maintenant fonctionner.**

---

### Solution 2 : Via FTP/cPanel (Sans SSH)

Si vous n'avez PAS accès SSH mais seulement FTP :

#### Étape 1 : Créer le fichier .env localement

Sur votre ordinateur local :

```bash
cd "C:\Users\JEAN SERI\Desktop\domini\domini"
php artisan key:generate
```

Cela va générer une clé dans votre `.env` local.

#### Étape 2 : Copier le contenu de .env

Ouvrez votre fichier `.env` local et copiez TOUT son contenu.

#### Étape 3 : Créer .env sur le serveur

Via FTP (FileZilla, WinSCP, etc.) :
1. Connectez-vous à votre serveur
2. Allez dans le dossier racine de Domini
3. Créez un nouveau fichier nommé `.env` (attention au point au début)
4. Collez le contenu copié
5. **IMPORTANT** : Modifiez les lignes suivantes selon votre serveur :

```env
# Environnement
APP_ENV=production
APP_DEBUG=false
APP_URL=http://votredomaine.com

# Base de données (À MODIFIER selon votre hébergeur)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=votre_nom_base_de_donnees
DB_USERNAME=votre_user_mysql
DB_PASSWORD=votre_password_mysql
```

6. Sauvegardez le fichier

#### Étape 4 : Vérifier les permissions

Le fichier `.env` doit avoir les bonnes permissions :
- Via FTP : Clic droit sur `.env` → Permissions → `644` (rw-r--r--)

---

### Solution 3 : Via cPanel File Manager

Si vous utilisez cPanel :

#### Option A : Copier depuis .env.example

1. Connectez-vous à cPanel
2. Ouvrez **File Manager**
3. Allez dans le dossier `domini`
4. Trouvez le fichier `.env.example`
5. Clic droit → **Copy**
6. Nommez la copie : `.env`
7. Clic droit sur `.env` → **Edit**
8. Cherchez la ligne `APP_KEY=`
9. Remplacez-la par (générez une clé ci-dessous) :

#### Générer une clé manuellement

Allez sur ce site : https://generate-random.org/laravel-key-generator

OU utilisez cette commande PHP :

```php
<?php
echo 'base64:'.base64_encode(random_bytes(32));
?>
```

Exemple de clé générée :
```
APP_KEY=base64:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx=
```

10. Collez cette clé dans votre `.env`
11. **Modifiez aussi les paramètres de base de données** (voir ci-dessus)
12. Sauvegardez

---

## 📋 Exemple de fichier .env minimal

Créez un fichier `.env` avec au minimum ce contenu :

```env
APP_NAME=Domini
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE_ICI
APP_DEBUG=false
APP_URL=http://votredomaine.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=votre_base_de_donnees
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
```

---

## ⚠️ IMPORTANT : Sécurité

### 1. Protéger le fichier .env

Vérifiez que votre `.htaccess` à la racine contient :

```apache
# Protéger les fichiers sensibles
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 2. Vérifier que .env n'est PAS accessible

Testez dans votre navigateur :
```
http://votredomaine.com/.env
```

**Vous devriez avoir une erreur 403 Forbidden** (c'est normal et souhaité !).

Si le fichier s'affiche, **URGENT** : contactez votre hébergeur ou ajoutez ceci dans `.htaccess` :

```apache
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

---

## 🔍 Vérifications Finales

Après avoir configuré le `.env` :

### 1. Tester le site
Accédez à : `http://votredomaine.com`

✅ **Ça fonctionne ?** Parfait !  
❌ **Erreur 500 ?** Vérifiez les logs (voir ci-dessous)

### 2. Tester l'admin
Accédez à : `http://votredomaine.com/admin/login`

### 3. Vérifier les logs

Si vous avez encore des erreurs :

**Via SSH :**
```bash
tail -f storage/logs/laravel.log
```

**Via FTP :**
Téléchargez et ouvrez : `storage/logs/laravel.log`

---

## 🆘 Autres Erreurs Possibles

### Erreur : "Permission denied" sur storage/

**Solution :**
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

Ou via cPanel : Permissions `775` pour les dossiers `storage` et `bootstrap/cache`

### Erreur : Images/CSS ne chargent pas

**Solution :**
```bash
php artisan storage:link
```

Sans SSH, créez un lien symbolique manuellement ou contactez l'hébergeur.

---

## 📞 Support Hébergeur

Si rien ne fonctionne, contactez votre hébergeur avec ces informations :

```
Bonjour,

J'ai déployé une application Laravel et j'ai besoin de :
1. Vérifier que mod_rewrite est activé
2. Vérifier que AllowOverride All est configuré
3. Exécuter la commande : php artisan key:generate
4. Configurer les permissions : chmod -R 775 storage bootstrap/cache

Merci de votre aide.
```

---

## ✅ Checklist Rapide

- [ ] Fichier `.env` existe sur le serveur
- [ ] `APP_KEY` est renseigné dans `.env`
- [ ] Paramètres de base de données corrects dans `.env`
- [ ] `APP_DEBUG=false` en production
- [ ] `APP_ENV=production` en production
- [ ] Permissions correctes sur `storage/` et `bootstrap/cache/`
- [ ] `.env` n'est PAS accessible via navigateur
- [ ] Site fonctionne : `http://votredomaine.com`
- [ ] Admin fonctionne : `http://votredomaine.com/admin/login`

---

## 🎯 Commandes Récapitulatives (SSH)

```bash
# Tout en une fois
cd /chemin/vers/domini
cp .env.example .env
php artisan key:generate
php artisan config:clear
php artisan cache:clear
chmod -R 775 storage bootstrap/cache
php artisan storage:link

# Si vous avez les droits sudo
sudo chown -R www-data:www-data storage bootstrap/cache
sudo systemctl restart apache2
```

---

✅ **Votre application devrait maintenant fonctionner !**

Si vous avez toujours des problèmes, vérifiez le fichier `storage/logs/laravel.log` pour plus de détails sur l'erreur.

📅 *Dernière mise à jour : 21 Janvier 2026*

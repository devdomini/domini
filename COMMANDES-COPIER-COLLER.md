# 📋 Commandes à Copier-Coller - Domini

## 🎯 Choisissez votre situation

---

## 1️⃣ Vous avez ACCÈS SSH

### Étape 1 : Se connecter et naviguer
```bash
ssh votre_user@votre_serveur.com
cd /chemin/vers/domini
```

### Étape 2 : Créer .env et générer la clé
```bash
cp .env.example .env
php artisan key:generate
```

### Étape 3 : Configurer la base de données
```bash
nano .env
```

Modifiez ces lignes (utilisez les flèches pour naviguer) :
```env
DB_DATABASE=votre_base_de_donnees
DB_USERNAME=votre_user
DB_PASSWORD=votre_password
APP_DEBUG=false
APP_ENV=production
```

**Sauvegarder :** `Ctrl+X`, puis `Y`, puis `Enter`

### Étape 4 : Configurer les permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
php artisan storage:link
```

### Étape 5 : Effacer les caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Étape 6 : Lancer les migrations (première fois seulement)
```bash
php artisan migrate --force
php artisan db:seed --force
```

### ✅ C'EST FAIT !
Testez : `http://votredomaine.com`

---

## 2️⃣ Vous avez ACCÈS cPanel avec Terminal

### Étape 1 : Ouvrir le Terminal dans cPanel
- Connectez-vous à cPanel
- Cherchez "Terminal" ou "SSH Access"
- Cliquez pour ouvrir

### Étape 2 : Naviguer vers votre projet
```bash
cd public_html/domini
# OU
cd www/domini
# OU
cd votredomaine.com/domini
```

### Étape 3 : Copier-coller TOUT en une fois
```bash
cp .env.example .env && \
php artisan key:generate && \
chmod -R 775 storage bootstrap/cache && \
php artisan storage:link && \
php artisan config:clear && \
php artisan cache:clear
```

### Étape 4 : Éditer .env
```bash
nano .env
```

Modifiez :
```env
DB_DATABASE=votre_base_de_donnees
DB_USERNAME=votre_user
DB_PASSWORD=votre_password
APP_DEBUG=false
APP_ENV=production
```

**Sauvegarder :** `Ctrl+X`, `Y`, `Enter`

### ✅ TERMINÉ !

---

## 3️⃣ Vous avez SEULEMENT FTP/FileZilla

### Étape 1 : Sur votre ordinateur LOCAL
```bash
cd "C:\Users\JEAN SERI\Desktop\domini\domini"
php artisan key:generate
```

### Étape 2 : Ouvrir le fichier .env local
- Ouvrez `.env` avec Notepad++
- Copiez TOUT le contenu

### Étape 3 : Via FTP
1. Connectez-vous avec FileZilla
2. Naviguez vers `/public_html/domini` (ou similaire)
3. Clic droit → "Créer un nouveau fichier"
4. Nommez-le `.env` (avec le point)
5. Clic droit sur `.env` → "Voir/Éditer"
6. Collez le contenu copié
7. Modifiez ces lignes :

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://votredomaine.com

DB_DATABASE=votre_base_cpanel
DB_USERNAME=votre_user_cpanel
DB_PASSWORD=votre_pass_cpanel
```

8. Sauvegardez et fermez
9. FileZilla demandera de "Uploader", cliquez OUI

### Étape 4 : Permissions des dossiers
Dans FileZilla :
- Clic droit sur dossier `storage` → Permissions → `775`
- Clic droit sur dossier `bootstrap/cache` → Permissions → `775`

### ✅ FAIT !

---

## 4️⃣ Vous avez ACCÈS cPanel File Manager SEULEMENT

### Étape 1 : Générer une clé APP_KEY

Allez sur : **https://generate-random.org/laravel-key-generator**

Copiez la clé générée (ex: `base64:abc123...`)

### Étape 2 : Dans cPanel File Manager

1. Naviguez vers `/public_html/domini` ou `/www/domini`
2. Trouvez le fichier `.env.example`
3. Clic droit → **Copy**
4. Nommez la copie : `.env`
5. Clic droit sur `.env` → **Edit**

### Étape 3 : Modifier le fichier .env

Remplacez/Modifiez ces lignes :

```env
APP_KEY=base64:COLLEZ_LA_CLE_GENEREE_ICI
APP_ENV=production
APP_DEBUG=false
APP_URL=http://votredomaine.com

DB_DATABASE=nom_base_donnees_cpanel
DB_USERNAME=user_mysql_cpanel
DB_PASSWORD=password_mysql_cpanel
```

6. Cliquez "Save Changes"

### Étape 4 : Permissions

1. Clic droit sur dossier `storage` → **Change Permissions** → `775`
2. Cochez "Recurse into subdirectories"
3. Clic droit sur dossier `bootstrap/cache` → **Change Permissions** → `775`

### ✅ TERMINÉ !

---

## 🔍 Vérifications Communes (Toutes Méthodes)

### 1. Tester le site
```
http://votredomaine.com
```

**✅ Ça marche ?** Parfait !  
**❌ Page blanche ?** Vérifiez les logs

### 2. Tester l'admin
```
http://votredomaine.com/admin/login

Email : admin@domini.com
Mot de passe : password123
```

### 3. Vérifier que .env est protégé
```
http://votredomaine.com/.env
```

**Doit afficher : 403 Forbidden** ✅

### 4. Vérifier que .htaccess existe

Dans votre FTP/File Manager, vérifiez que vous avez :
- `.htaccess` à la racine de `domini/`
- `.htaccess` dans `domini/public/`

**Pas visible ?** Activez "Afficher les fichiers cachés" dans votre FTP

---

## 🆘 Si Ça Ne Marche Toujours Pas

### Vérifier les logs Laravel

**Via SSH :**
```bash
tail -50 storage/logs/laravel.log
```

**Via FTP/cPanel :**
Téléchargez : `storage/logs/laravel.log` et ouvrez-le

### Vérifier les logs Apache

**Via SSH :**
```bash
sudo tail -50 /var/log/apache2/error.log
```

**Via cPanel :**
Cherchez "Error Log" et consultez les erreurs récentes

### Activer le debug temporairement

Dans `.env`, changez :
```env
APP_DEBUG=true
```

Rechargez la page, notez l'erreur, puis **remettez à false**

---

## 🎯 Commandes de Maintenance

### Effacer tous les caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Optimiser pour la production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Mettre en maintenance
```bash
php artisan down --message="Maintenance en cours" --retry=60
```

### Sortir de maintenance
```bash
php artisan up
```

### Créer un admin supplémentaire (via Tinker)
```bash
php artisan tinker
```

Puis copiez-collez :
```php
$admin = new App\Models\User();
$admin->name = 'Nom Admin';
$admin->email = 'nouveau@admin.com';
$admin->password = Hash::make('motdepasse123');
$admin->role = 'admin';
$admin->is_active = true;
$admin->save();
exit
```

---

## 📞 Message à Envoyer à Votre Hébergeur

Si rien ne fonctionne, copiez-collez ce message :

```
Bonjour,

J'ai déployé une application Laravel 11 et j'ai besoin de votre aide pour :

1. Vérifier que mod_rewrite est activé pour Apache
2. Vérifier que AllowOverride est configuré à "All" dans ma configuration Apache
3. Vérifier que ces extensions PHP sont activées :
   - BCMath
   - Ctype
   - JSON
   - Mbstring
   - OpenSSL
   - PDO
   - Tokenizer
   - XML
4. Configurer les permissions 775 pour les dossiers :
   - storage/
   - bootstrap/cache/

Merci de votre aide !
```

---

## ✅ Checklist Finale

- [ ] Fichier `.env` créé
- [ ] `APP_KEY` généré
- [ ] Base de données configurée
- [ ] Permissions 775 sur storage/
- [ ] Permissions 775 sur bootstrap/cache/
- [ ] .htaccess présent à la racine
- [ ] .htaccess présent dans public/
- [ ] Site accessible
- [ ] Admin accessible
- [ ] Images/CSS chargent
- [ ] .env protégé (403 Forbidden)

---

🎉 **Votre site Domini est maintenant en ligne !**

**Login par défaut :**
- URL : `http://votredomaine.com/admin/login`
- Email : `admin@domini.com`
- Password : `password123`

⚠️ **CHANGEZ CE MOT DE PASSE immédiatement !**

---

*Besoin d'aide ? Consultez `SOLUTION-RAPIDE-SERVEUR.md` ou `DEPLOIEMENT_SERVEUR.md`*

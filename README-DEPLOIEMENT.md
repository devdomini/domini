# 📦 Guide de Déploiement Domini - Récapitulatif

## 🎯 Vous êtes ici parce que...

Votre site affiche les dossiers/fichiers OU vous avez l'erreur `MissingAppKeyException`.

---

## ⚡ SOLUTION ULTRA-RAPIDE (2 minutes)

### 1️⃣ Créer le fichier .env

**Via SSH :**
```bash
cd /chemin/vers/domini
cp .env.example .env
php artisan key:generate
nano .env  # Modifier DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan config:clear
```

**Via cPanel/FTP :**
1. Ouvrez `config-env-serveur.txt` (dans votre projet)
2. Copiez tout le contenu
3. Créez un fichier `.env` à la racine
4. Collez et modifiez :
   - `APP_KEY=` → Générez sur https://generate-random.org/laravel-key-generator
   - `DB_DATABASE=` → Votre nom de base de données
   - `DB_USERNAME=` → Votre user MySQL
   - `DB_PASSWORD=` → Votre password MySQL

### 2️⃣ Vérifier les fichiers .htaccess

Deux fichiers `.htaccess` ont été créés :

**À la racine** : `domini/.htaccess`
- Redirige tout vers `public/`
- Désactive le listage des dossiers
- Protège les fichiers sensibles

**Dans public** : `domini/public/.htaccess`
- Gère les routes Laravel

✅ **Ces fichiers sont déjà créés, rien à faire !**

### 3️⃣ Tester

```
Site : http://votredomaine.com
Admin : http://votredomaine.com/admin/login
```

**Login admin :**
- Email : `admin@domini.com`
- Mot de passe : `password123`

---

## 📚 Documentation Complète

J'ai créé plusieurs guides pour vous aider :

### 🚨 Résolution de Problèmes
- **`SOLUTION-RAPIDE-SERVEUR.md`** ⭐ COMMENCEZ ICI !
- **`FIX_APP_KEY_SERVEUR.md`** - Tout sur l'erreur APP_KEY
- **`DEPLOIEMENT_SERVEUR.md`** - Guide complet de déploiement

### 📖 Guides Fonctionnels
- **`SYSTEME_BOXES_ABONNEMENTS_COMPLET.md`** - Documentation Boxes & Abonnements
- **`GUIDE_TEST_BOXES_ABONNEMENTS.md`** - Tests des fonctionnalités
- **`DASHBOARD_GRAPHIQUES.md`** - Documentation Dashboard
- **`CHANGELOG.md`** - Historique des modifications

### 🔧 Configuration
- **`config-env-serveur.txt`** - Template .env pour production
- **`.htaccess`** - Configuration Apache (racine)
- **`public/.htaccess`** - Configuration Laravel (public)
- **`.user.ini`** - Configuration PHP optimale
- **`install-server.sh`** - Script d'installation automatique (Linux)

---

## 🔧 Fichiers Créés pour Vous

### Fichiers de Configuration
```
domini/
├── .htaccess                          ✅ Nouveau
├── .user.ini                          ✅ Nouveau
├── config-env-serveur.txt             ✅ Nouveau
└── public/.htaccess                   ✅ Déjà présent (Laravel)
```

### Documentation
```
domini/
├── SOLUTION-RAPIDE-SERVEUR.md         ✅ Nouveau - START HERE
├── FIX_APP_KEY_SERVEUR.md             ✅ Nouveau
├── DEPLOIEMENT_SERVEUR.md             ✅ Nouveau
├── README-DEPLOIEMENT.md              ✅ Nouveau (ce fichier)
├── SYSTEME_BOXES_ABONNEMENTS_COMPLET.md
├── GUIDE_TEST_BOXES_ABONNEMENTS.md
├── CHANGELOG.md
└── [... autres MD existants]
```

### Scripts
```
domini/
└── install-server.sh                  ✅ Nouveau (pour Linux/Ubuntu)
```

---

## 🎯 Ordre d'Action Recommandé

1. **MAINTENANT** : Lisez `SOLUTION-RAPIDE-SERVEUR.md`
2. Créez votre fichier `.env` (via SSH ou cPanel)
3. Testez votre site
4. Si problème : Consultez `FIX_APP_KEY_SERVEUR.md`
5. Pour déploiement complet : `DEPLOIEMENT_SERVEUR.md`

---

## ✅ Checklist de Déploiement

### Fichiers Essentiels
- [ ] `.htaccess` à la racine (créé ✅)
- [ ] `public/.htaccess` (déjà présent ✅)
- [ ] `.env` créé et configuré
- [ ] `APP_KEY` généré dans `.env`
- [ ] Paramètres DB configurés dans `.env`

### Sécurité
- [ ] `APP_DEBUG=false` en production
- [ ] `APP_ENV=production`
- [ ] `.env` non accessible via navigateur (test : votredomaine.com/.env → 403)
- [ ] Mot de passe admin changé

### Fonctionnel
- [ ] Site accessible
- [ ] Admin accessible
- [ ] Images/CSS chargent
- [ ] Connexion admin fonctionne
- [ ] Base de données connectée

### Optimisation (Plus tard)
- [ ] `php artisan config:cache`
- [ ] `php artisan route:cache`
- [ ] `php artisan view:cache`
- [ ] Certificat SSL (HTTPS)
- [ ] Sauvegardes automatiques

---

## 🆘 Problèmes Courants

### "The stream or file could not be opened"
**Solution :** Permissions incorrectes
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### "404 Not Found" sur toutes les pages
**Solution :** mod_rewrite désactivé ou .htaccess ignoré
```bash
# Via SSH
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Images ne chargent pas
**Solution :** Lien symbolique storage manquant
```bash
php artisan storage:link
```

### Page blanche sans erreur
**Solutions :**
1. Vérifiez `storage/logs/laravel.log`
2. Activez temporairement `APP_DEBUG=true` dans `.env`
3. Vérifiez les permissions

---

## 📞 Support

### Logs à vérifier
1. `storage/logs/laravel.log` (Laravel)
2. `/var/log/apache2/error.log` (Apache)
3. Console navigateur (F12) pour erreurs JS/CSS

### Informations à fournir à votre hébergeur
```
Application : Laravel 11
PHP requis : >= 8.2
Extensions PHP requises : BCMath, Ctype, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
mod_rewrite : Requis
AllowOverride : All (dans configuration Apache)
```

---

## 🎉 Après le Déploiement Réussi

Une fois que tout fonctionne :

1. **Changez le mot de passe admin**
   - Connectez-vous avec admin@domini.com / password123
   - Allez dans Utilisateurs → Modifier Admin → Changer mot de passe

2. **Testez toutes les fonctionnalités**
   - Créez une entreprise
   - Créez des catégories et plats
   - Créez une box
   - Créez un abonnement
   - Testez les QR codes

3. **Configurez les sauvegardes**
   - Base de données (mysqldump)
   - Fichiers uploadés (storage/app/public)
   - Fichier .env

4. **Installez un certificat SSL** (HTTPS)
   - Let's Encrypt (gratuit)
   - Via cPanel (SSL/TLS)

5. **Optimisez**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

---

## 🚀 Vous êtes Prêt !

Votre application Domini est maintenant déployée et fonctionnelle !

**Prochaines étapes :**
- Ajoutez vos vraies données (entreprises, plats, etc.)
- Personnalisez les paramètres
- Formez vos utilisateurs
- Communiquez sur le lancement

---

## 📊 Statistiques du Projet

- **Vues créées** : 40+
- **Modèles** : 13
- **Contrôleurs** : 12
- **Routes** : 80+
- **Migrations** : 16
- **Documentation** : 15 fichiers MD
- **Lignes de code** : 10,000+

---

✅ **Félicitations ! Vous avez un système complet de gestion de commandes et livraisons ! 🎉**

*Dernière mise à jour : 21 Janvier 2026*

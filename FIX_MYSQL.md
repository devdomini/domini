# 🔧 Guide de Dépannage MySQL - Erreur "MySQL shutdown unexpectedly"

## 🚨 Problème

MySQL s'arrête de manière inattendue avec l'erreur :
```
Error: MySQL shutdown unexpectedly.
This may be due to a blocked port, missing dependencies, 
improper privileges, a crash, or a shutdown by another method.
```

## ✅ Solutions par Ordre de Priorité

### Solution 1 : Vérifier le Port 3306 (Le Plus Courant)

#### Étape 1 : Vérifier si le port est utilisé
```bash
netstat -ano | findstr :3306
```

Si vous voyez une entrée, notez le PID (dernier nombre).

#### Étape 2 : Arrêter le processus qui utilise le port
```bash
# Remplacez PID par le numéro trouvé
taskkill /PID PID /F
```

#### Étape 3 : Redémarrer MySQL depuis XAMPP

### Solution 2 : Vérifier les Logs MySQL

1. Dans XAMPP, cliquez sur **Logs** à côté de MySQL
2. Ouvrez le dernier fichier de log
3. Cherchez les erreurs récentes

**Emplacement des logs :**
```
C:\xampp\mysql\data\*.err
```

### Solution 3 : Vérifier l'Espace Disque

MySQL peut s'arrêter si l'espace disque est insuffisant.

```bash
# Vérifier l'espace disque disponible
dir C:\
```

Assurez-vous d'avoir au moins **500 MB** d'espace libre.

### Solution 4 : Réparer les Tables MySQL

1. Arrêtez MySQL dans XAMPP
2. Ouvrez le terminal en tant qu'**Administrateur**
3. Naviguez vers le dossier MySQL :
```bash
cd C:\xampp\mysql\bin
```

4. Réparez les tables :
```bash
mysqlcheck --all-databases --auto-repair -u root -p
```

(Si aucun mot de passe, appuyez juste sur Entrée)

### Solution 5 : Réinitialiser MySQL (Dernier Recours)

⚠️ **ATTENTION** : Cela supprimera toutes les données MySQL !

1. Arrêtez MySQL dans XAMPP
2. Sauvegardez votre base de données si nécessaire :
```bash
cd C:\xampp\mysql\bin
mysqldump -u root -p domini > backup_domini.sql
```

3. Supprimez le dossier `data` :
   - Fermez XAMPP complètement
   - Allez dans `C:\xampp\mysql\data`
   - Supprimez tous les dossiers SAUF :
     - `mysql`
     - `performance_schema`
     - `phpmyadmin` (si présent)

4. Réinitialisez MySQL :
```bash
cd C:\xampp\mysql\bin
mysqld --initialize-insecure --console
```

5. Redémarrez MySQL depuis XAMPP
6. Recréez votre base de données :
```bash
mysql -u root -e "CREATE DATABASE domini CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Solution 6 : Vérifier les Permissions

1. Clic droit sur le dossier `C:\xampp\mysql\data`
2. Propriétés → Sécurité
3. Assurez-vous que **SYSTEM** et **Administrateurs** ont tous les droits
4. Appliquez les modifications

### Solution 7 : Vérifier le Fichier my.ini

1. Ouvrez `C:\xampp\mysql\bin\my.ini`
2. Vérifiez que les chemins sont corrects :
```ini
basedir="C:/xampp/mysql"
datadir="C:/xampp/mysql/data"
```

3. Vérifiez que le port est 3306 :
```ini
port=3306
```

### Solution 8 : Désactiver l'Antivirus Temporairement

Parfois, l'antivirus bloque MySQL. Désactivez-le temporairement et testez.

### Solution 9 : Vérifier les Services Windows

1. Appuyez sur `Win + R`
2. Tapez `services.msc` et Entrée
3. Cherchez **MySQL** ou **mysql**
4. Si trouvé, arrêtez-le et désactivez-le
5. Redémarrez MySQL depuis XAMPP

### Solution 10 : Réinstaller MySQL dans XAMPP

1. Sauvegardez vos bases de données :
```bash
cd C:\xampp\mysql\bin
mysqldump -u root -p --all-databases > all_databases_backup.sql
```

2. Désinstallez MySQL depuis XAMPP (si possible)
3. Téléchargez et réinstallez XAMPP
4. Restaurez vos bases de données :
```bash
mysql -u root -p < all_databases_backup.sql
```

## 🔍 Diagnostic Avancé

### Vérifier les Erreurs dans l'Observateur d'Événements Windows

1. Appuyez sur `Win + R`
2. Tapez `eventvwr.msc` et Entrée
3. Allez dans **Journaux Windows** → **Application**
4. Cherchez les erreurs MySQL récentes

### Tester MySQL Manuellement

1. Ouvrez le terminal en tant qu'Administrateur
2. Naviguez vers MySQL :
```bash
cd C:\xampp\mysql\bin
```

3. Démarrez MySQL manuellement :
```bash
mysqld --console
```

4. Regardez les erreurs affichées dans la console

## 📋 Checklist de Dépannage

- [ ] Port 3306 libre
- [ ] Espace disque suffisant (>500 MB)
- [ ] Permissions correctes sur `mysql\data`
- [ ] Fichier `my.ini` correct
- [ ] Aucun autre service MySQL en cours
- [ ] Antivirus ne bloque pas MySQL
- [ ] Logs MySQL consultés
- [ ] Observateur d'événements Windows vérifié

## 🚀 Solution Rapide (Essayer en Premier)

```bash
# 1. Arrêter MySQL dans XAMPP

# 2. Ouvrir le terminal en Administrateur

# 3. Arrêter tous les processus MySQL
taskkill /F /IM mysqld.exe

# 4. Attendre 5 secondes

# 5. Redémarrer MySQL depuis XAMPP
```

## 💡 Prévention

1. **Sauvegardes régulières** :
```bash
cd C:\xampp\mysql\bin
mysqldump -u root -p domini > backup_%date%.sql
```

2. **Surveiller l'espace disque**
3. **Ne pas arrêter MySQL brutalement** (utiliser le bouton Stop dans XAMPP)
4. **Mettre à jour XAMPP régulièrement**

## 📞 Si Rien ne Fonctionne

1. Copiez les logs MySQL complets
2. Copiez les erreurs de l'Observateur d'événements Windows
3. Vérifiez la version de XAMPP et MySQL
4. Consultez les forums XAMPP : https://community.apachefriends.org/

## 🔗 Commandes Utiles

```bash
# Vérifier si MySQL fonctionne
mysql -u root -e "SELECT VERSION();"

# Voir toutes les bases de données
mysql -u root -e "SHOW DATABASES;"

# Vérifier les processus MySQL
tasklist | findstr mysqld

# Arrêter MySQL proprement
cd C:\xampp\mysql\bin
mysqladmin -u root shutdown
```

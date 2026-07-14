# ⚡ Solution Rapide - Problème Tablespace MySQL

## 🎯 Solution la plus simple

**Exécutez ces commandes dans MySQL** :

```bash
# 1. Connectez-vous à MySQL
mysql -u root -p

# 2. Dans MySQL, exécutez :
USE laravel;
ALTER TABLE migrations DISCARD TABLESPACE;
DROP TABLE IF EXISTS migrations;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS cache_locks;
exit;

# 3. Relancez les migrations Laravel
php artisan migrate
```

## 📝 Alternative : Script SQL

Si vous préférez utiliser un fichier SQL :

```bash
# Exécutez le fichier SQL fourni
mysql -u root -p laravel < fix_tablespace_mysql.sql

# Puis relancez les migrations
php artisan migrate
```

## 🔄 Solution complète : Recréer la base

Si rien ne fonctionne, recréez complètement la base de données :

```bash
# Dans MySQL
mysql -u root -p

# Exécutez :
DROP DATABASE IF EXISTS laravel;
CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;

# Puis relancez toutes les migrations
php artisan migrate
```

## ✅ Vérification

Après avoir appliqué une solution :

```bash
php artisan migrate:status
```

Si tout est OK, vous devriez voir la liste des migrations.

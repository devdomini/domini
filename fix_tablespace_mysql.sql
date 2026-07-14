-- Script SQL pour corriger le problème de tablespace MySQL
-- Exécutez ce script directement dans MySQL (mysql -u root -p laravel < fix_tablespace_mysql.sql)
-- OU connectez-vous à MySQL et copiez-collez ces commandes

USE laravel;

-- Supprimer le tablespace de la table migrations si elle existe
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables 
                      WHERE table_schema = 'laravel' AND table_name = 'migrations');

SET @sql = IF(@table_exists > 0, 
    'ALTER TABLE migrations DISCARD TABLESPACE', 
    'SELECT "Table migrations does not exist" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Supprimer la table migrations
DROP TABLE IF EXISTS migrations;

-- Supprimer aussi les autres tables système qui pourraient avoir le même problème
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS failed_jobs;

-- Maintenant vous pouvez exécuter: php artisan migrate

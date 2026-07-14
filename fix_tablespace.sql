-- Script de réparation des tablespaces MySQL
USE domini;

-- Supprimer toutes les tables problématiques
DROP TABLE IF EXISTS migrations;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS failed_jobs;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS personal_access_tokens;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS plats;
DROP TABLE IF EXISTS commandes;
DROP TABLE IF EXISTS commande_items;
DROP TABLE IF EXISTS favoris;
DROP TABLE IF EXISTS paiements;
DROP TABLE IF EXISTS livraisons;
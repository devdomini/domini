<?php

/**
 * Script pour corriger définitivement le problème de tablespace MySQL
 * 
 * Ce script doit être exécuté AVANT les migrations Laravel
 * Usage: php fix_mysql_tablespace.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "🔧 Correction du problème de tablespace MySQL...\n\n";

try {
    // Obtenir les informations de connexion
    $config = config('database.connections.mysql');
    
    echo "📊 Connexion à la base de données: {$config['database']}\n";
    
    // Créer une connexion PDO directe pour plus de contrôle
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    
    // Vérifier si la table migrations existe
    $stmt = $pdo->query("SHOW TABLES LIKE 'migrations'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "⚠️  La table migrations existe. Tentative de suppression du tablespace...\n";
        
        try {
            $pdo->exec("ALTER TABLE migrations DISCARD TABLESPACE");
            echo "✅ Tablespace supprimé.\n";
        } catch (PDOException $e) {
            echo "ℹ️  Tablespace déjà supprimé ou erreur: " . $e->getMessage() . "\n";
        }
        
        try {
            $pdo->exec("DROP TABLE migrations");
            echo "✅ Table migrations supprimée.\n";
        } catch (PDOException $e) {
            echo "⚠️  Erreur lors de la suppression: " . $e->getMessage() . "\n";
        }
    } else {
        echo "ℹ️  La table migrations n'existe pas.\n";
    }
    
    // Supprimer aussi les autres tables système qui pourraient avoir le même problème
    $systemTables = ['cache', 'cache_locks', 'sessions', 'jobs', 'failed_jobs'];
    foreach ($systemTables as $table) {
        try {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($stmt->rowCount() > 0) {
                try {
                    $pdo->exec("ALTER TABLE {$table} DISCARD TABLESPACE");
                } catch (PDOException $e) {
                    // Ignorer
                }
                $pdo->exec("DROP TABLE IF EXISTS {$table}");
                echo "✅ Table {$table} supprimée.\n";
            }
        } catch (PDOException $e) {
            // Ignorer les erreurs
        }
    }
    
    echo "\n✅ Nettoyage terminé avec succès!\n";
    echo "📝 Vous pouvez maintenant exécuter: php artisan migrate\n";
    
} catch (PDOException $e) {
    echo "\n❌ Erreur de connexion MySQL: " . $e->getMessage() . "\n";
    echo "\n💡 Solution alternative:\n";
    echo "1. Connectez-vous à MySQL avec: mysql -u root -p\n";
    echo "2. Utilisez la base de données: USE laravel;\n";
    echo "3. Exécutez: ALTER TABLE migrations DISCARD TABLESPACE;\n";
    echo "4. Exécutez: DROP TABLE IF EXISTS migrations;\n";
    echo "5. Relancez: php artisan migrate\n";
    exit(1);
} catch (\Exception $e) {
    echo "\n❌ Erreur: " . $e->getMessage() . "\n";
    exit(1);
}

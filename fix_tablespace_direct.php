<?php

// Script pour corriger le problème de tablespace MySQL
// Exécuter: php fix_tablespace_direct.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Tentative de suppression du tablespace migrations...\n";
    
    // Essayer de supprimer le tablespace directement
    try {
        DB::statement("ALTER TABLE migrations DISCARD TABLESPACE");
        echo "Tablespace migrations supprimé.\n";
    } catch (\Exception $e) {
        echo "Table migrations n'existe pas ou tablespace déjà supprimé.\n";
    }
    
    // Supprimer la table si elle existe
    try {
        DB::statement("DROP TABLE IF EXISTS migrations");
        echo "Table migrations supprimée.\n";
    } catch (\Exception $e) {
        echo "Erreur lors de la suppression de la table: " . $e->getMessage() . "\n";
    }
    
    // Supprimer aussi les autres tables problématiques
    $tables = ['cache', 'cache_locks', 'sessions', 'jobs', 'failed_jobs'];
    foreach ($tables as $table) {
        try {
            DB::statement("DROP TABLE IF EXISTS {$table}");
            echo "Table {$table} supprimée.\n";
        } catch (\Exception $e) {
            // Ignorer les erreurs
        }
    }
    
    echo "\n✅ Nettoyage terminé. Vous pouvez maintenant exécuter: php artisan migrate\n";
    
} catch (\Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Essayez de vous connecter directement à MySQL et exécutez:\n";
    echo "ALTER TABLE migrations DISCARD TABLESPACE;\n";
    echo "DROP TABLE IF EXISTS migrations;\n";
}

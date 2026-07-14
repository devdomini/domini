<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Supprimer le tablespace si la table existe
        try {
            if (Schema::hasTable('migrations')) {
                DB::statement('ALTER TABLE migrations DISCARD TABLESPACE');
                DB::statement('DROP TABLE IF EXISTS migrations');
            }
        } catch (\Exception $e) {
            // Ignorer les erreurs
        }
        
        // Créer la table migrations normalement
        Schema::create('migrations', function ($table) {
            $table->id();
            $table->string('migration');
            $table->integer('batch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('migrations');
    }
};

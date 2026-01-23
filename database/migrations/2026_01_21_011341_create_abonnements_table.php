<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('abonnements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_entreprise')->constrained('entreprises')->onDelete('cascade');
            $table->string('representant'); // Nom du représentant
            $table->string('numero'); // Numéro de téléphone
            $table->string('fonction'); // Fonction dans l'entreprise
            $table->enum('status', ['actif', 'suspendu', 'resilie', 'expire'])->default('actif');
            $table->enum('statut_subvention_commande', ['totale', 'partielle', 'aucune'])->default('totale');
            $table->decimal('pourcentage', 5, 2)->default(100.00); // Pourcentage de subvention (0-100)
            $table->integer('nbre_employe')->default(0); // Nombre d'employés couverts
            $table->date('date_debut');
            $table->date('date_fin');
            $table->date('date_resiliation')->nullable();
            $table->text('raison_resiliation')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};

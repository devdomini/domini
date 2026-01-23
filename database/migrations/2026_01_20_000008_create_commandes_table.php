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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id();
            $table->string('ref')->unique()->nullable();
            $table->foreignId('id_employe')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('montant_total', 10, 2)->nullable();
            $table->enum('statut_commande', ['en_attente', 'confirmee', 'annulee', 'terminee'])->default('en_attente')->nullable();
            $table->enum('statut_preparation', ['en_attente', 'en_cours', 'prete'])->default('en_attente')->nullable();
            $table->enum('statut_livraison', ['en_attente', 'en_cours', 'livree', 'echec'])->default('en_attente')->nullable();
            $table->string('lieu')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('long', 10, 7)->nullable();
            $table->text('consigne_cuisinier')->nullable();
            $table->text('consigne_livreur')->nullable();
            $table->enum('statut_paiement', ['en_attente', 'paye', 'rembourse'])->default('en_attente')->nullable();
            $table->enum('mode_paiement', ['especes', 'carte', 'mobile_money', 'wallet'])->nullable();
            $table->string('numero_telephone')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};

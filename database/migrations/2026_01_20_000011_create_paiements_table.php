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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['abonnement', 'commande', 'remboursement', 'paiement_livraison']);
            $table->decimal('montant', 10, 2);
            $table->string('ref')->unique();
            $table->enum('mode_paiement', ['especes', 'carte', 'mobile_money', 'wallet', 'virement']);
            $table->enum('statut', ['en_attente', 'valide', 'echoue', 'rembourse'])->default('en_attente');
            $table->foreignId('commande_id')->nullable()->constrained('commandes')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};

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
        Schema::table('commandes', function (Blueprint $table) {
            $table->boolean('is_lunch')->default(false)->after('montant_total');
            $table->date('date_livraison')->nullable()->after('is_lunch');
            $table->string('creneau')->nullable()->after('date_livraison'); // 'midi' ou autre si besoin
            $table->foreignId('adresse_id')->nullable()->after('numero_telephone')->constrained('adresses')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('commandes', function (Blueprint $table) {
            $table->dropForeign(['adresse_id']);
            $table->dropColumn(['is_lunch', 'date_livraison', 'creneau', 'adresse_id']);
        });
    }
};

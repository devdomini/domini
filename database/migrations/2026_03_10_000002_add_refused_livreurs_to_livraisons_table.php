<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('livraisons', function (Blueprint $table) {
            $table->json('refused_livreur_ids')->nullable()->after('commentaire');
        });
    }

    public function down(): void
    {
        Schema::table('livraisons', function (Blueprint $table) {
            $table->dropColumn(['refused_livreur_ids']);
        });
    }
};


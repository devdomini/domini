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
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('qualite', ['classic', 'pro', 'premium'])->default('classic')->after('est_disponible');
        });

        Schema::table('plats', function (Blueprint $table) {
            $table->enum('qualite', ['classic', 'pro', 'premium'])->default('classic')->after('est_disponible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('qualite');
        });

        Schema::table('plats', function (Blueprint $table) {
            $table->dropColumn('qualite');
        });
    }
};

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
        Schema::create('casiers', function (Blueprint $table) {
            $table->id();
            $table->string('ref')->unique();
            $table->string('qr_code')->unique();
            $table->foreignId('id_box')->constrained('boxes')->onDelete('cascade');
            $table->foreignId('id_employe')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('statut', ['libre', 'occupe', 'reserve', 'hors_service'])->default('libre');
            $table->integer('numero_casier'); // Numéro du casier dans la box
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('casiers');
    }
};

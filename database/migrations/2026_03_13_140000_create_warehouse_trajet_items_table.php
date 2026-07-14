<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_trajet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('entreprise_id')->constrained('entreprises')->cascadeOnDelete();
            $table->unsignedInteger('position');
            $table->timestamps();

            $table->unique(['warehouse_id', 'entreprise_id']);
            $table->index(['warehouse_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_trajet_items');
    }
};

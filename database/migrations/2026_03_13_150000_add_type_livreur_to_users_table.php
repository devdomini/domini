<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'type_livreur')) {
                $table
                    ->enum('type_livreur', ['classique', 'entreprise'])
                    ->nullable()
                    ->after('warehouse_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'type_livreur')) {
                $table->dropColumn('type_livreur');
            }
        });
    }
};


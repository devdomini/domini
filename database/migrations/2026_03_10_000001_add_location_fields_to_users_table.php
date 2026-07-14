<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('current_lat', 10, 8)->nullable()->after('telephone_verified_at');
            $table->decimal('current_long', 11, 8)->nullable()->after('current_lat');
            $table->timestamp('last_location_at')->nullable()->after('current_long');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['current_lat', 'current_long', 'last_location_at']);
        });
    }
};


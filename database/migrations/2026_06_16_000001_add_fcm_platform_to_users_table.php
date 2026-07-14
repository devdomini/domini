<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'fcm_platform')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'fcm_token')) {
                $table->string('fcm_platform', 32)->nullable()->after('fcm_token');
            } else {
                $table->string('fcm_platform', 32)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'fcm_platform')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('fcm_platform');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'fcm_token')) {
                $table->text('fcm_token')->nullable();
            }
            if (! Schema::hasColumn('users', 'fcm_platform')) {
                $table->string('fcm_platform', 32)->nullable()->after('fcm_token');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('users', 'fcm_platform') ? 'fcm_platform' : null,
                Schema::hasColumn('users', 'fcm_token') ? 'fcm_token' : null,
            ]);
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};

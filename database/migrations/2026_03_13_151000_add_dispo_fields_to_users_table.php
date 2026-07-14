<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'is_dispo')) {
                $table->boolean('is_dispo')->default(true)->after('is_active');
            }
            if (! Schema::hasColumn('users', 'indispo_reason')) {
                $table->string('indispo_reason', 500)->nullable()->after('is_dispo');
            }
            if (! Schema::hasColumn('users', 'indispo_at')) {
                $table->timestamp('indispo_at')->nullable()->after('indispo_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'indispo_at')) {
                $table->dropColumn('indispo_at');
            }
            if (Schema::hasColumn('users', 'indispo_reason')) {
                $table->dropColumn('indispo_reason');
            }
            if (Schema::hasColumn('users', 'is_dispo')) {
                $table->dropColumn('is_dispo');
            }
        });
    }
};


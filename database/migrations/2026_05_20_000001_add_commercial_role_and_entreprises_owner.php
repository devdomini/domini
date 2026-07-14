<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'entreprise', 'livreur', 'employe', 'commercial') NOT NULL DEFAULT 'employe'");

        Schema::table('entreprises', function (Blueprint $table) {
            $table->foreignId('commercial_id')->nullable()->after('id')->constrained('users')->nullOnDelete();
            $table->index('commercial_id');
        });
    }

    public function down(): void
    {
        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropConstrainedForeignId('commercial_id');
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'entreprise', 'livreur', 'employe') NOT NULL DEFAULT 'employe'");
    }
};

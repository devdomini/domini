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
        Schema::table('users', function (Blueprint $table) {
            $table->string('code_verification', 6)->nullable()->after('telephone');
            $table->timestamp('code_expires_at')->nullable()->after('code_verification');
            $table->timestamp('telephone_verified_at')->nullable()->after('code_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['code_verification', 'code_expires_at', 'telephone_verified_at']);
        });
    }
};

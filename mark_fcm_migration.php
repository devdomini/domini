<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

if (!DB::table('migrations')->where('migration', '2026_04_09_000001_add_fcm_columns_to_users_table')->exists()) {
    DB::table('migrations')->insert([
        'migration' => '2026_04_09_000001_add_fcm_columns_to_users_table',
        'batch' => (int) DB::table('migrations')->max('batch') + 1,
    ]);
    echo "Migration marked as run\n";
} else {
    echo "Migration already recorded\n";
}

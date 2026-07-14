<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$users = User::query()
    ->whereNotNull('fcm_token')
    ->where('fcm_token', '!=', '')
    ->orderByDesc('updated_at')
    ->limit(20)
    ->get(['id', 'name', 'role', 'telephone', 'fcm_token', 'updated_at']);

echo "Users with FCM token: {$users->count()}\n\n";
foreach ($users as $u) {
    echo sprintf(
        "#%d | %s | role=%s | tel=%s | token_len=%d | updated=%s\n",
        $u->id,
        $u->name,
        $u->role,
        $u->telephone ?? 'null',
        strlen((string) $u->fcm_token),
        $u->updated_at
    );
}

$recent = User::query()
    ->where('role', 'employe')
    ->orderByDesc('updated_at')
    ->limit(15)
    ->get(['id', 'name', 'telephone', 'fcm_token']);

echo "\nRecent employe users:\n";
foreach ($recent as $u) {
    echo sprintf(
        "#%d | %s | tel=%s | fcm=%s\n",
        $u->id,
        $u->name,
        $u->telephone ?? 'null',
        $u->fcm_token ? 'yes' : 'no'
    );
}

<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$needle = $argv[1] ?? '748528787';

$users = User::query()
    ->where('telephone', 'like', '%'.$needle.'%')
    ->orWhere('email', 'like', '%'.$needle.'%')
    ->orWhere('name', 'like', '%'.$needle.'%')
    ->get(['id', 'name', 'role', 'telephone', 'email', 'fcm_token']);

if ($users->isEmpty()) {
    echo "NO_MATCH for {$needle}\n";
    exit(1);
}

foreach ($users as $u) {
    echo sprintf(
        "#%d | %s | role=%s | tel=%s | fcm=%s | platform=%s\n",
        $u->id,
        $u->name,
        $u->role,
        $u->telephone ?? 'null',
        $u->fcm_token ? ('yes('.strlen($u->fcm_token).')') : 'no',
        'n/a'
    );
}

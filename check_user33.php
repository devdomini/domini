<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::find(33);
if (!$user) {
    echo "USER 33 NOT FOUND\n";
    exit(1);
}

echo "id={$user->id} name={$user->name} tel={$user->telephone}\n";
echo 'fcm_token: '.($user->fcm_token ? substr($user->fcm_token, 0, 40).'... (len='.strlen($user->fcm_token).')' : 'NULL')."\n";
echo 'fcm_platform: '.($user->fcm_platform ?? 'NULL')."\n";

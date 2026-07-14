<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

$user = User::query()
    ->where('telephone', 'like', '%748526787%')
    ->orWhere('telephone', 'like', '%748528787%')
    ->first();

if (!$user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

$token = PersonalAccessToken::where('tokenable_id', $user->id)
    ->where('tokenable_type', User::class)
    ->orderByDesc('last_used_at')
    ->orderByDesc('created_at')
    ->first();

echo "user_id={$user->id} tel={$user->telephone}\n";
echo 'token='.($token ? $token->plainTextToken ?? '[hash only id='.$token->id.']' : 'NONE')."\n";

if ($token && empty($token->plainTextToken)) {
    echo "plain token not stored; create new token...\n";
    $new = $user->createToken('push-test');
    echo 'new_token='.$new->plainTextToken."\n";
}

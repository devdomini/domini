<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\FcmNotificationService;

$phone = $argv[1] ?? '2250748528787';

$user = User::query()
    ->where('telephone', $phone)
    ->orWhere('telephone', '0748528787')
    ->orWhere('telephone', 'like', '%748528787%')
    ->first();

if (!$user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

echo "User #{$user->id} {$user->name} ({$user->role}) tel={$user->telephone}\n";
echo 'FCM configured: '.(FcmNotificationService::isConfigured() ? 'yes' : 'no')."\n";
echo 'FCM platform: '.($user->fcm_platform ?? 'null')."\n";
echo 'FCM token length: '.strlen((string) $user->fcm_token)."\n";

if (empty($user->fcm_token)) {
    echo "NO_FCM_TOKEN: connectez-vous sur l'app mobile (serveur local) pour enregistrer le jeton.\n";
    exit(2);
}

$ok = FcmNotificationService::sendToUser(
    $user,
    'Test Domini',
    'Notification push de test — '.now()->format('H:i:s'),
    ['type' => 'test', 'user_id' => (string) $user->id]
);

echo $ok ? "PUSH_SENT_OK\n" : "PUSH_FAILED\n";
exit($ok ? 0 : 3);

<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Services\FcmNotificationService;
use Illuminate\Support\Facades\Http;

$phones = ['2250748528787', '+2250748528787', '0748528787', '+2250748526787', '2250748526787'];

$user = null;
foreach ($phones as $p) {
    $user = User::where('telephone', $p)->first();
    if ($user) break;
}
if (!$user) {
    $user = User::where('telephone', 'like', '%748526787%')->first();
}

if (!$user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

echo "Target: #{$user->id} {$user->name} ({$user->telephone})\n";

if (empty($user->fcm_token)) {
    echo "NO_FCM_TOKEN yet — trying to re-register via API if app is logged in...\n";
    $access = $user->createToken('push-test-cli');
    $plain = $access->plainTextToken;

    $response = Http::withToken($plain)->post('http://127.0.0.1:8078/api/device/fcm-token', [
        'fcm_token' => 'TEST_TOKEN_PLACEHOLDER',
        'platform' => 'android',
    ]);

    echo 'Register test status: '.$response->status()."\n";
    echo 'Register test body: '.$response->body()."\n";

    $user->refresh();
    if ($user->fcm_token === 'TEST_TOKEN_PLACEHOLDER') {
        echo "DB write OK — waiting for real token from app...\n";
        $user->update(['fcm_token' => null, 'fcm_platform' => null]);
        echo "Cleared placeholder token.\n";
    }
    $access->accessToken->delete();
}

$user->refresh();
if (empty($user->fcm_token)) {
    echo "STILL_NO_FCM_TOKEN: ouvrez l'app, connectez-vous avec {$user->telephone}, acceptez les notifications.\n";
    exit(2);
}

echo 'FCM token length: '.strlen($user->fcm_token)."\n";
$ok = FcmNotificationService::sendToUser(
    $user,
    'Test Domini',
    'Notification push de test — '.now()->format('H:i:s d/m/Y'),
    ['type' => 'test']
);

echo $ok ? "PUSH_SENT_OK\n" : "PUSH_FAILED (voir storage/logs/laravel.log)\n";
exit($ok ? 0 : 3);

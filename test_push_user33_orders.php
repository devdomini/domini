<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use App\Services\FcmNotificationService;

$userId = (int) ($argv[1] ?? 33);
$user = User::find($userId);

if (! $user) {
    echo "USER_NOT_FOUND\n";
    exit(1);
}

echo "User #{$user->id} {$user->name} ({$user->role}) tel={$user->telephone}\n";
echo 'FCM: '.($user->fcm_token ? 'yes ('.strlen($user->fcm_token).')' : 'no')."\n";

if (empty($user->fcm_token)) {
    echo "NO_FCM_TOKEN — ouvrez l'app connectée avec ce compte.\n";
    exit(2);
}

if (! FcmNotificationService::isConfigured()) {
    echo "FCM_NOT_CONFIGURED\n";
    exit(3);
}

$commande = Commande::query()
    ->where('id_employe', $user->id)
    ->latest('id')
    ->first();

if (! $commande) {
    $commande = Commande::query()->latest('id')->first();
}

$commandeId = $commande?->id ?? 0;
$commandeRef = $commande?->ref ?? $commandeId;

$livraison = $commande
    ? Livraison::where('commande_id', $commande->id)->latest('id')->first()
    : null;

$livraisonId = $livraison?->id ?? 0;

echo "Commande test: #{$commandeRef} (id={$commandeId})\n";
echo "Livraison test: id={$livraisonId}\n\n";

$tests = [];

if ($user->role === 'employe') {
    $tests[] = [
        'label' => 'Commande confirmée (client)',
        'title' => 'Commande Confirmée',
        'body' => "Votre commande #{$commandeRef} a été confirmée.",
        'data' => [
            'type' => 'order_status_changed',
            'commande_id' => (string) $commandeId,
            'status_type' => 'commande',
            'new_status' => 'confirmee',
        ],
    ];
    $tests[] = [
        'label' => 'Livraison en cours (client)',
        'title' => 'Livraison en cours',
        'body' => "Votre commande #{$commandeRef} est en route vers vous.",
        'data' => [
            'type' => 'order_status_changed',
            'commande_id' => (string) $commandeId,
            'status_type' => 'livraison',
            'new_status' => 'en_cours',
        ],
    ];
    $tests[] = [
        'label' => 'Livreur assigné (client)',
        'title' => 'Livreur assigné',
        'body' => "Un livreur a été assigné à votre commande #{$commandeRef}.",
        'data' => [
            'type' => 'livreur_assigned',
            'commande_id' => (string) $commandeId,
        ],
    ];
}

if ($user->role === 'livreur') {
    $tests[] = [
        'label' => 'Nouvelle livraison (livreur)',
        'title' => 'Nouvelle livraison',
        'body' => "Commande #{$commandeRef} vous est assignée.",
        'data' => [
            'type' => 'livraison_assigned',
            'commande_id' => (string) $commandeId,
            'livraison_id' => (string) $livraisonId,
            'is_lunch' => '0',
        ],
    ];
    $tests[] = [
        'label' => 'Statut livraison (livreur)',
        'title' => 'Statut livraison mis à jour',
        'body' => "La livraison de la commande #{$commandeRef} est en cours.",
        'data' => [
            'type' => 'order_status_changed',
            'commande_id' => (string) $commandeId,
            'livraison_id' => (string) $livraisonId,
            'status_type' => 'livraison',
            'new_status' => 'en_cours',
            'audience' => 'livreur',
        ],
    ];
}

if ($tests === []) {
    // employe par défaut si rôle autre
    $tests[] = [
        'label' => 'Test commande',
        'title' => 'Test commande Domini',
        'body' => 'Push test commande — '.now()->format('H:i:s'),
        'data' => [
            'type' => 'order_status_changed',
            'commande_id' => (string) $commandeId,
            'status_type' => 'commande',
            'new_status' => 'confirmee',
        ],
    ];
}

$okCount = 0;
foreach ($tests as $i => $test) {
    echo ($i + 1).'. '.$test['label']."\n";
    $ok = FcmNotificationService::sendToUser(
        $user,
        $test['title'],
        $test['body'],
        $test['data']
    );
    echo $ok ? "   PUSH_SENT_OK\n" : "   PUSH_FAILED\n";
    if ($ok) {
        $okCount++;
    }
    if ($i < count($tests) - 1) {
        sleep(2);
    }
}

echo "\nRésumé: {$okCount}/".count($tests)." envoyé(s).\n";
exit($okCount === count($tests) ? 0 : 4);

<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use App\Services\FcmNotificationService;

$userId = (int) ($argv[1] ?? 0);
$user = null;

if ($userId > 0) {
    $user = User::find($userId);
}

if (! $user) {
    $user = User::query()
        ->where('role', 'livreur')
        ->whereNotNull('fcm_token')
        ->where('fcm_token', '!=', '')
        ->orderByDesc('updated_at')
        ->first();
}

if (! $user) {
    echo "NO_LIVREUR_WITH_FCM\n";
    echo "Connectez-vous sur l'app avec un compte livreur pour enregistrer le jeton FCM.\n";
    exit(1);
}

echo "Livreur #{$user->id} {$user->name} tel={$user->telephone}\n";
echo 'FCM: yes ('.strlen($user->fcm_token).")\n";

$livraison = Livraison::query()
    ->where('livreur_id', $user->id)
    ->with('commande')
    ->latest('id')
    ->first();

if (! $livraison) {
    $livraison = Livraison::query()->with('commande')->latest('id')->first();
}

$commandeId = $livraison?->commande_id ?? 0;
$livraisonId = $livraison?->id ?? 0;
$ref = $livraison?->commande?->ref ?? $commandeId;

echo "Livraison test: id={$livraisonId} commande={$ref} (id={$commandeId})\n\n";

$tests = [
    [
        'label' => 'Nouvelle livraison assignée',
        'title' => 'Nouvelle livraison',
        'body' => "Commande #{$ref} vous est assignée.",
        'data' => [
            'type' => 'livraison_assigned',
            'commande_id' => (string) $commandeId,
            'livraison_id' => (string) $livraisonId,
            'is_lunch' => '0',
        ],
    ],
    [
        'label' => 'Lot entreprise',
        'title' => 'Nouveau lot entreprise',
        'body' => '3 livraisons entreprise vous sont assignées.',
        'data' => [
            'type' => 'livraison_batch_assigned',
            'count' => '3',
        ],
    ],
    [
        'label' => 'Statut livraison (admin)',
        'title' => 'Statut livraison mis à jour',
        'body' => "La livraison de la commande #{$ref} est en cours.",
        'data' => [
            'type' => 'order_status_changed',
            'commande_id' => (string) $commandeId,
            'livraison_id' => (string) $livraisonId,
            'status_type' => 'livraison',
            'new_status' => 'en_cours',
            'audience' => 'livreur',
        ],
    ],
    [
        'label' => 'Commande prête à récupérer',
        'title' => 'Commande prête',
        'body' => "La commande #{$ref} est prête à être récupérée.",
        'data' => [
            'type' => 'order_status_changed',
            'commande_id' => (string) $commandeId,
            'livraison_id' => (string) $livraisonId,
            'status_type' => 'preparation',
            'new_status' => 'prete',
            'audience' => 'livreur',
        ],
    ],
];

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

echo "\nRésumé: {$okCount}/".count($tests)." envoyé(s) au livreur #{$user->id}.\n";
exit($okCount === count($tests) ? 0 : 4);

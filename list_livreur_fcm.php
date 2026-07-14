<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Livraison;
use App\Models\User;

$u33 = User::find(33);
echo "User 33: {$u33?->name} role={$u33?->role}\n";

$liv = Livraison::find(401);
if ($liv) {
    $lv = User::find($liv->livreur_id);
    echo "Livraison 401 livreur: #{$liv->livreur_id} {$lv?->name} fcm=".($lv?->fcm_token ? 'yes' : 'no')."\n";
}

$all = User::query()
    ->where('role', 'livreur')
    ->whereNotNull('fcm_token')
    ->where('fcm_token', '!=', '')
    ->get(['id', 'name', 'telephone']);

echo "Livreurs avec FCM: {$all->count()}\n";
foreach ($all as $x) {
    echo "#{$x->id} {$x->name} {$x->telephone}\n";
}

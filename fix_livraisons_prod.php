<?php
/**
 * fix_livraisons_prod.php
 * Uploader ce fichier à la RACINE du projet sur le serveur de prod
 * Accéder via : https://domini-food.com/fix_livraisons_prod.php?key=domini2024
 * SUPPRIMER après utilisation !
 */

if (($_GET['key'] ?? '') !== 'domini2024') {
    die('Accès refusé');
}

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Livraison;
use App\Models\Commande;
use App\Models\User;

echo "<pre>\n";

// 1. Trouver les vrais livreurs en prod
$livreurs = User::where('role', 'livreur')->where('is_active', 1)->get(['id', 'name', 'telephone']);
echo "=== LIVREURS ACTIFS EN PROD ===\n";
foreach ($livreurs as $l) {
    echo "  ID={$l->id} | {$l->name} | {$l->telephone}\n";
}
echo "\n";

// 2. Compter les livraisons en en_attente
$enAttente = Livraison::where('statut', 'en_attente')->count();
echo "Livraisons en 'en_attente' : {$enAttente}\n";

if ($enAttente === 0) {
    echo "Aucune livraison à corriger.\n";
    echo "</pre>";
    exit;
}

if (!isset($_GET['fix'])) {
    echo "\nAjouter &fix=1 à l'URL pour appliquer la correction.\n";
    echo "Exemple : ?key=domini2024&fix=1\n";
    echo "</pre>";
    exit;
}

// 3. Récupérer les IDs livreurs actifs en prod
$livreurIds = $livreurs->pluck('id')->toArray();
if (empty($livreurIds)) {
    echo "ERREUR : Aucun livreur actif trouvé en prod !\n";
    echo "</pre>";
    exit;
}

echo "\nApplication de la correction...\n";

// 4. Répartir les livraisons entre les livreurs dispo
$livraisons = Livraison::where('statut', 'en_attente')->get();
$count = $livraisons->count();
$nbLivreurs = count($livreurIds);

$updated = 0;
foreach ($livraisons as $i => $livraison) {
    $newLivreurId = $livreurIds[$i % $nbLivreurs];
    
    $livraison->update([
        'statut'       => 'assignee',
        'livreur_id'   => $newLivreurId,
        'heure_assignation' => now(),
    ]);

    // Mettre à jour la commande associée
    if ($livraison->commande_id) {
        Commande::where('id', $livraison->commande_id)->update([
            'statut_commande'    => 'confirmee',
            'statut_preparation' => 'en_cours',
            'statut_livraison'   => 'assignee',
        ]);
    }

    $updated++;
}

echo "✅ {$updated} livraisons mises à jour vers 'assignee'\n";
echo "✅ Commandes passées en 'confirmee'\n\n";

// 5. Résumé final
foreach ($livreurIds as $lid) {
    $nb = Livraison::where('livreur_id', $lid)->where('statut', 'assignee')->count();
    $livreur = $livreurs->firstWhere('id', $lid);
    echo "  Livreur {$livreur->name} (ID={$lid}) : {$nb} livraisons assignées\n";
}

echo "\n⚠️  SUPPRIMER ce fichier du serveur maintenant !\n";
echo "</pre>";

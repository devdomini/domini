<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;

// Routes publiques
Route::get('/', function () {
    return view('welcome');
});

Route::get('/notre-menu', [App\Http\Controllers\MenuPublicController::class, 'index'])->name('menu.public');

Route::get('/support', function () {
    return view('support');
});

Route::get('/inscription', function () {
    return view('inscription');
});

Route::get('/devenir-livreur', function () {
    return view('devenir-livreur');
});

// Route de login simple (alias pour Laravel)
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

// Routes Admin
Route::prefix('admin')->name('admin.')->group(function () {
    // Routes accessibles sans authentification
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminController::class, 'login'])->name('login.post');
    
    // Routes protégées (nécessitent authentification)
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/logout', [AdminController::class, 'logout'])->name('logout');
        
        // Gestion des utilisateurs
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\UserController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\UserController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\UserController::class, 'update'])->name('update');
            Route::get('/{id}/change-password', [App\Http\Controllers\UserController::class, 'changePassword'])->name('change-password');
            Route::patch('/{id}/password', [App\Http\Controllers\UserController::class, 'updatePassword'])->name('update-password');
            Route::patch('/{id}/toggle-status', [App\Http\Controllers\UserController::class, 'toggleStatus'])->name('toggle-status');
        });

        // Gestion des entreprises
        Route::prefix('entreprises')->name('entreprises.')->group(function () {
            Route::get('/', [App\Http\Controllers\EntrepriseController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\EntrepriseController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\EntrepriseController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [App\Http\Controllers\EntrepriseController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\EntrepriseController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\EntrepriseController::class, 'destroy'])->name('destroy');
        });

        // Gestion des livreurs
        Route::prefix('livreurs')->name('livreurs.')->group(function () {
            Route::get('/', [App\Http\Controllers\LivreurController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\LivreurController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\LivreurController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\LivreurController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [App\Http\Controllers\LivreurController::class, 'edit'])->name('edit');
            Route::put('/{id}', [App\Http\Controllers\LivreurController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status', [App\Http\Controllers\LivreurController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{id}/affect-entreprises', [App\Http\Controllers\LivreurController::class, 'affectEntreprises'])->name('affect-entreprises');
            Route::delete('/{id}/remove-entreprise', [App\Http\Controllers\LivreurController::class, 'removeEntreprise'])->name('remove-entreprise');
            Route::delete('/{id}/remove-all-entreprises', [App\Http\Controllers\LivreurController::class, 'removeAllEntreprises'])->name('remove-all-entreprises');
            Route::get('/{id}/change-password', [App\Http\Controllers\LivreurController::class, 'changePassword'])->name('change-password');
            Route::patch('/{id}/password', [App\Http\Controllers\LivreurController::class, 'updatePassword'])->name('update-password');
        });

        // Gestion du Menu
        Route::prefix('menu')->name('menu.')->group(function () {
            Route::get('/', [App\Http\Controllers\MenuController::class, 'index'])->name('index');
            
            // Routes pour les catégories
            Route::prefix('categories')->name('categories.')->group(function () {
                Route::get('/', [App\Http\Controllers\CategorieController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\CategorieController::class, 'store'])->name('store');
                Route::put('/{categorie}', [App\Http\Controllers\CategorieController::class, 'update'])->name('update');
                Route::patch('/{categorie}/toggle', [App\Http\Controllers\CategorieController::class, 'toggle'])->name('toggle');
                Route::delete('/{categorie}', [App\Http\Controllers\CategorieController::class, 'destroy'])->name('destroy');
            });
            
            // Routes pour les plats
            Route::prefix('plats')->name('plats.')->group(function () {
                Route::get('/', [App\Http\Controllers\PlatController::class, 'index'])->name('index');
                Route::post('/', [App\Http\Controllers\PlatController::class, 'store'])->name('store');
                Route::put('/{plat}', [App\Http\Controllers\PlatController::class, 'update'])->name('update');
                Route::patch('/{plat}/toggle', [App\Http\Controllers\PlatController::class, 'toggle'])->name('toggle');
                Route::delete('/{plat}', [App\Http\Controllers\PlatController::class, 'destroy'])->name('destroy');
                
                // Routes pour les accompagnements
                Route::post('/{plat}/accompagnements', [App\Http\Controllers\PlatController::class, 'storeAccompagnement'])->name('accompagnements.store');
                Route::delete('/accompagnements/{accompagnement}', [App\Http\Controllers\PlatController::class, 'destroyAccompagnement'])->name('accompagnements.destroy');
                
                // Routes pour les options
                Route::post('/{plat}/options', [App\Http\Controllers\PlatController::class, 'storeOption'])->name('options.store');
                Route::delete('/options/{option}', [App\Http\Controllers\PlatController::class, 'destroyOption'])->name('options.destroy');
            });
        });

        // Gestion des commandes
        Route::prefix('commandes')->name('commandes.')->group(function () {
            Route::get('/', [App\Http\Controllers\CommandeController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\CommandeController::class, 'show'])->name('show');
            Route::post('/{id}/affecter-livreur', [App\Http\Controllers\CommandeController::class, 'affecterLivreur'])->name('affecter-livreur');
            Route::post('/{id}/changer-statut-commande', [App\Http\Controllers\CommandeController::class, 'changerStatutCommande'])->name('changer-statut-commande');
            Route::post('/{id}/changer-statut-preparation', [App\Http\Controllers\CommandeController::class, 'changerStatutPreparation'])->name('changer-statut-preparation');
            Route::post('/{id}/changer-statut-livraison', [App\Http\Controllers\CommandeController::class, 'changerStatutLivraison'])->name('changer-statut-livraison');
            Route::post('/{id}/annuler', [App\Http\Controllers\CommandeController::class, 'annuler'])->name('annuler');
            Route::post('/{id}/valider', [App\Http\Controllers\CommandeController::class, 'valider'])->name('valider');
        });

        // Gestion des livraisons
        Route::prefix('livraisons')->name('livraisons.')->group(function () {
            Route::get('/', [App\Http\Controllers\LivraisonController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\LivraisonController::class, 'show'])->name('show');
            Route::post('/{id}/changer-statut', [App\Http\Controllers\LivraisonController::class, 'changerStatut'])->name('changer-statut');
        });

        // Gestion des paiements
        Route::prefix('paiements')->name('paiements.')->group(function () {
            Route::get('/', [App\Http\Controllers\PaiementController::class, 'index'])->name('index');
        });

        // Gestion des abonnements
        Route::prefix('abonnements')->name('abonnements.')->group(function () {
            Route::get('/', [App\Http\Controllers\AbonnementController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\AbonnementController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\AbonnementController::class, 'store'])->name('store');
            Route::get('/{abonnement}', [App\Http\Controllers\AbonnementController::class, 'show'])->name('show');
            Route::get('/{abonnement}/edit', [App\Http\Controllers\AbonnementController::class, 'edit'])->name('edit');
            Route::patch('/{abonnement}', [App\Http\Controllers\AbonnementController::class, 'update'])->name('update');
            Route::patch('/{abonnement}/resilier', [App\Http\Controllers\AbonnementController::class, 'resilier'])->name('resilier');
            Route::patch('/{abonnement}/suspendre', [App\Http\Controllers\AbonnementController::class, 'suspendre'])->name('suspendre');
            Route::patch('/{abonnement}/reactiver', [App\Http\Controllers\AbonnementController::class, 'reactiver'])->name('reactiver');
            Route::patch('/{abonnement}/renouveler', [App\Http\Controllers\AbonnementController::class, 'renouveler'])->name('renouveler');
        });

        // Gestion des boxes et casiers
        Route::prefix('boxes')->name('boxes.')->group(function () {
            Route::get('/', [App\Http\Controllers\BoxController::class, 'index'])->name('index');
            Route::get('/create', [App\Http\Controllers\BoxController::class, 'create'])->name('create');
            Route::post('/', [App\Http\Controllers\BoxController::class, 'store'])->name('store');
            Route::get('/{box}', [App\Http\Controllers\BoxController::class, 'show'])->name('show');
            Route::get('/{box}/edit', [App\Http\Controllers\BoxController::class, 'edit'])->name('edit');
            Route::patch('/{box}', [App\Http\Controllers\BoxController::class, 'update'])->name('update');
            Route::delete('/{box}', [App\Http\Controllers\BoxController::class, 'destroy'])->name('destroy');
            Route::patch('/{box}/toggle', [App\Http\Controllers\BoxController::class, 'toggleStatus'])->name('toggle');
            Route::post('/{box}/casiers', [App\Http\Controllers\BoxController::class, 'ajouterCasiers'])->name('casiers.ajouter');
        });
    });
});

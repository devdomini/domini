<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

// Site vitrine (fichiers statiques dans public/vitrine/)
Route::get('/vitrine', function () {
    return redirect('/vitrine/index.html', 302);
});

// Routes publiques — page d’accueil = landing Domini
Route::get('/', function () {
    return redirect('/vitrine/index.html', 302);
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

// Route pour la réinitialisation du mot de passe (nécessaire pour Password::sendResetLink)
Route::get('/reset-password/{token}', function () {
    return redirect('/admin/login');
})->name('password.reset');

// Routes Admin + commercial (URLs sous /admin, noms commercial.* distincts de admin.*)
Route::prefix('admin')->group(function () {
    Route::name('admin.')->group(function () {
        Route::get('/login', [AdminController::class, 'showLoginForm'])->name('login');
        Route::middleware('throttle:5,1')->group(function () {
            Route::post('/login', [AdminController::class, 'login'])->name('login.post');
        });
    });

    Route::middleware(['auth', 'backoffice'])->group(function () {
        Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

        Route::prefix('messenger-api')
            ->withoutMiddleware([ValidateCsrfToken::class])
            ->group(function () {
            Route::get('/threads/{id}', [App\Http\Controllers\MessengerWebController::class, 'show']);
            Route::post('/threads/{id}/messages', [App\Http\Controllers\MessengerWebController::class, 'sendMessage']);
            Route::post('/threads/{id}/close', [App\Http\Controllers\MessengerWebController::class, 'close']);
            Route::post('/threads/{id}/reopen', [App\Http\Controllers\MessengerWebController::class, 'reopen']);
        });

        // Espace commercial (même login /admin/login) — noms route('commercial.*')
        Route::prefix('commercial')->name('commercial.')->group(function () {
            Route::get('/dashboard', [App\Http\Controllers\Commercial\DashboardController::class, 'index'])->name('dashboard');
            Route::prefix('entreprises')->name('entreprises.')->group(function () {
                Route::get('/', [App\Http\Controllers\Commercial\EntrepriseController::class, 'index'])->name('index');
                Route::get('/create', [App\Http\Controllers\Commercial\EntrepriseController::class, 'create'])->name('create');
                Route::post('/', [App\Http\Controllers\Commercial\EntrepriseController::class, 'store'])->name('store');
                Route::get('/{id}', [App\Http\Controllers\Commercial\EntrepriseController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [App\Http\Controllers\Commercial\EntrepriseController::class, 'edit'])->name('edit');
                Route::put('/{id}', [App\Http\Controllers\Commercial\EntrepriseController::class, 'update'])->name('update');
            });
            Route::post('/entreprises/{entrepriseId}/employes', [App\Http\Controllers\Commercial\EmployeController::class, 'store'])->name('employes.store');
            Route::put('/employes/{employeId}', [App\Http\Controllers\Commercial\EmployeController::class, 'update'])->name('employes.update');
            Route::get('/boxes/create/{entrepriseId}', [App\Http\Controllers\Commercial\BoxController::class, 'create'])->name('boxes.create');
            Route::post('/boxes', [App\Http\Controllers\Commercial\BoxController::class, 'store'])->name('boxes.store');
            Route::get('/boxes/{id}', [App\Http\Controllers\Commercial\BoxController::class, 'show'])->name('boxes.show');
            Route::post('/boxes/{boxId}/casiers/{casierId}/assign', [App\Http\Controllers\Commercial\BoxController::class, 'assignEmploye'])->name('boxes.casiers.assign');
            Route::post('/boxes/{boxId}/casiers/{casierId}/unassign', [App\Http\Controllers\Commercial\BoxController::class, 'unassignEmploye'])->name('boxes.casiers.unassign');
            Route::get('/abonnements', [App\Http\Controllers\Commercial\AbonnementController::class, 'index'])->name('abonnements.index');
            Route::get('/paiements', [App\Http\Controllers\Commercial\PaiementController::class, 'index'])->name('paiements.index');
            Route::get('/impayes', [App\Http\Controllers\Commercial\PaiementController::class, 'impayes'])->name('impayes.index');
            Route::prefix('support')->name('support.')->group(function () {
                Route::get('/', [App\Http\Controllers\Commercial\SupportController::class, 'index'])->name('index');
                Route::get('/{id}', [App\Http\Controllers\Commercial\SupportController::class, 'show'])->name('show');
                Route::post('/{id}/messages', [App\Http\Controllers\Commercial\SupportController::class, 'sendMessage'])->name('send');
                Route::post('/{id}/close', [App\Http\Controllers\Commercial\SupportController::class, 'close'])->name('close');
                Route::post('/{id}/reopen', [App\Http\Controllers\Commercial\SupportController::class, 'reopen'])->name('reopen');
            });
        });

        Route::middleware('admin.only')->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Campagnes push (promo, fidélisation, annonces)
        Route::get('/campagnes', [App\Http\Controllers\Admin\CampagneController::class, 'index'])->name('campagnes.index');
        Route::post('/campagnes/send', [App\Http\Controllers\Admin\CampagneController::class, 'send'])->name('campagnes.send');

        Route::prefix('support')->name('support.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('index');
            Route::get('/{id}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('show');
            Route::post('/{id}/messages', [App\Http\Controllers\Admin\SupportController::class, 'sendMessage'])->name('send');
            Route::post('/{id}/close', [App\Http\Controllers\Admin\SupportController::class, 'close'])->name('close');
            Route::post('/{id}/reopen', [App\Http\Controllers\Admin\SupportController::class, 'reopen'])->name('reopen');
        });
        
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

        // Gestion des entrepôts
        Route::resource('warehouses', App\Http\Controllers\Admin\WarehouseController::class);

        // Communes (liées à un entrepôt : nom, lat, long)
        Route::resource('communes', App\Http\Controllers\Admin\CommuneController::class)->except(['show']);

        // Trajets de livraison (ordre des entreprises par entrepôt)
        Route::get('trajets', [App\Http\Controllers\Admin\TrajetController::class, 'index'])->name('trajets.index');
        Route::get('trajets/{warehouse}/edit', [App\Http\Controllers\Admin\TrajetController::class, 'edit'])->name('trajets.edit');
        Route::get('trajets/{warehouse}/carte', [App\Http\Controllers\Admin\TrajetController::class, 'map'])->name('trajets.map');
        Route::post('trajets/{warehouse}/auto-proximite', [App\Http\Controllers\Admin\TrajetController::class, 'autoProximity'])->name('trajets.auto-proximite');
        Route::put('trajets/{warehouse}', [App\Http\Controllers\Admin\TrajetController::class, 'update'])->name('trajets.update');

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

        // Traffic livreur (carte temps réel)
        Route::get('/traffic-livreur', [App\Http\Controllers\Admin\TrafficLivreurController::class, 'index'])->name('traffic-livreur.index');
        Route::get('/traffic-livreur/data', [App\Http\Controllers\Admin\TrafficLivreurController::class, 'data'])->name('traffic-livreur.data');

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
            // Attribution employé ⇄ casier
            Route::get('/{box}/employes/search', [App\Http\Controllers\BoxController::class, 'searchEmployes'])->name('employes.search');
            Route::patch('/{box}/casiers/{casier}/assign', [App\Http\Controllers\BoxController::class, 'assignEmploye'])->name('casiers.assign');
            Route::patch('/{box}/casiers/{casier}/unassign', [App\Http\Controllers\BoxController::class, 'unassignEmploye'])->name('casiers.unassign');
            Route::patch('/{box}/casiers/{casier}/status', [App\Http\Controllers\BoxController::class, 'updateCasierStatus'])->name('casiers.status');
            // Impression QR casiers
            Route::get('/{box}/casiers/list', [App\Http\Controllers\BoxController::class, 'listCasiers'])->name('casiers.list');
            Route::get('/{box}/casiers/print', [App\Http\Controllers\BoxController::class, 'printCasiersQRCodes'])->name('casiers.print');
        });
        }); // admin.only
    });
});

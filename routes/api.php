<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CategorieApiController;
use App\Http\Controllers\Api\PlatApiController;
use App\Http\Controllers\Api\CommandeApiController;
use App\Http\Controllers\Api\FavoriApiController;
use App\Http\Controllers\Api\AdresseApiController;
use App\Http\Controllers\Api\EntrepriseApiController;
use App\Http\Controllers\Api\LivreurApiController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SupportApiController;
use App\Http\Controllers\Api\CommercialApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Throttle API : 60 requêtes/minute pour les routes publiques sensibles
Route::middleware('throttle:60,1')->group(function () {
// Routes publiques (sans authentification)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/forgot-password', [AuthApiController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthApiController::class, 'resetPassword']);
    Route::post('/verify-phone', [AuthApiController::class, 'verifyPhone']);
    Route::post('/resend-verification-code', [AuthApiController::class, 'resendVerificationCode']);
});
});

// Routes publiques pour le menu
Route::prefix('menu')->group(function () {
    Route::get('/categories', [CategorieApiController::class, 'index']);
    Route::get('/categories/{id}', [CategorieApiController::class, 'show']);
    Route::get('/plats', [PlatApiController::class, 'index']);
    Route::get('/plats/category/{categorieId}', [PlatApiController::class, 'byCategory']);
    Route::get('/plats/{id}', [PlatApiController::class, 'show']);
});

// Routes publiques pour les entreprises
Route::prefix('entreprises')->group(function () {
    Route::get('/', [EntrepriseApiController::class, 'index']);
    Route::get('/{id}', [EntrepriseApiController::class, 'show']);
});

// Configuration (Entrepôts, etc.)
Route::prefix('config')->group(function () {
    Route::get('/warehouse', [App\Http\Controllers\Api\WarehouseApiController::class, 'main']);
    Route::get('/warehouses', [App\Http\Controllers\Api\WarehouseApiController::class, 'index']);
});

// Route de test SMS — désactivée en production pour éviter les abus
if (!app()->environment('production')) {
    Route::post('/test-sms', function (Request $request) {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'phone' => 'required|string',
            'message' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation échouée',
                'errors' => $validator->errors()
            ], 400);
        }

        $smsService = new \App\Services\OrangeSmsService();
        $result = $smsService->sendSMS($request->phone, $request->message);

        return response()->json($result);
    });
}

// Routes protégées (nécessitent une authentification)
Route::middleware('auth:sanctum')->group(function () {
    // Authentification
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthApiController::class, 'me']);
        Route::put('/profile', [AuthApiController::class, 'updateProfile']);
        Route::post('/change-password', [AuthApiController::class, 'changePassword']);
        Route::post('/refresh-token', [AuthApiController::class, 'refreshToken']);
        Route::post('/logout', [AuthApiController::class, 'logout']);
        Route::post('/delete-account', [AuthApiController::class, 'deleteAccount']);
    });

    // Jeton FCM (notifications push)
    Route::post('/device/fcm-token', [DeviceTokenController::class, 'store']);
    Route::delete('/device/fcm-token', [DeviceTokenController::class, 'destroy']);

    // Commandes
    Route::prefix('commandes')->group(function () {
        Route::post('/', [CommandeApiController::class, 'store']);
        Route::get('/history', [CommandeApiController::class, 'history']);
        Route::get('/{id}', [CommandeApiController::class, 'show']);
        Route::get('/{id}/tracking', [CommandeApiController::class, 'tracking']);
        Route::post('/{id}/annuler', [CommandeApiController::class, 'annuler']);
    });

    // Favoris
    Route::prefix('favoris')->group(function () {
        Route::get('/', [FavoriApiController::class, 'index']);
        Route::post('/', [FavoriApiController::class, 'store']);
        Route::get('/check/{platId}', [FavoriApiController::class, 'check']);
        Route::delete('/{platId}', [FavoriApiController::class, 'destroy']);
    });

    // Adresses
    Route::prefix('adresses')->group(function () {
        Route::get('/', [AdresseApiController::class, 'index']);
        Route::post('/', [AdresseApiController::class, 'store']);
        Route::put('/{id}', [AdresseApiController::class, 'update']);
        Route::delete('/{id}', [AdresseApiController::class, 'destroy']);
    });

    // Support (messagerie admin)
    Route::prefix('support')->group(function () {
        Route::get('/thread', [SupportApiController::class, 'show']);
        Route::post('/messages', [SupportApiController::class, 'send']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // Commercial (app mobile)
    Route::prefix('commercial')->group(function () {
        Route::get('/dashboard', [CommercialApiController::class, 'dashboard']);
        Route::get('/communes', [CommercialApiController::class, 'communes']);
        Route::get('/entreprises', [CommercialApiController::class, 'entreprisesIndex']);
        Route::post('/entreprises', [CommercialApiController::class, 'entrepriseStore']);
        Route::get('/entreprises/{id}', [CommercialApiController::class, 'entrepriseShow']);
        Route::put('/entreprises/{id}', [CommercialApiController::class, 'entrepriseUpdate']);
        Route::get('/entreprises/{id}/boxes', [CommercialApiController::class, 'boxesIndex']);
        Route::get('/entreprises/{id}/employes', [CommercialApiController::class, 'employesIndex']);
        Route::post('/entreprises/{id}/employes', [CommercialApiController::class, 'employeStore']);
        Route::put('/employes/{employeId}', [CommercialApiController::class, 'employeUpdate']);
        Route::post('/boxes', [CommercialApiController::class, 'boxStore']);
        Route::get('/boxes/{boxId}', [CommercialApiController::class, 'boxShow']);
        Route::get('/boxes/{boxId}/casiers', [CommercialApiController::class, 'casiersIndex']);
        Route::post('/boxes/{boxId}/casiers/{casierId}/assign', [CommercialApiController::class, 'assignCasier']);
        Route::post('/boxes/{boxId}/casiers/{casierId}/unassign', [CommercialApiController::class, 'unassignCasier']);
        Route::get('/abonnements', [CommercialApiController::class, 'abonnementsIndex']);
        Route::get('/paiements', [CommercialApiController::class, 'paiementsIndex']);
        Route::get('/impayes', [CommercialApiController::class, 'impayesIndex']);
        Route::prefix('support')->group(function () {
            Route::get('/threads', [App\Http\Controllers\Api\CommercialSupportApiController::class, 'threads']);
            Route::get('/threads/{id}', [App\Http\Controllers\Api\CommercialSupportApiController::class, 'show']);
            Route::post('/threads/{id}/messages', [App\Http\Controllers\Api\CommercialSupportApiController::class, 'sendMessage']);
            Route::post('/threads/{id}/close', [App\Http\Controllers\Api\CommercialSupportApiController::class, 'close']);
            Route::post('/threads/{id}/reopen', [App\Http\Controllers\Api\CommercialSupportApiController::class, 'reopen']);
        });
    });

    // Livreurs
    Route::prefix('livreur')->group(function () {
        Route::post('/location', [LivreurApiController::class, 'updateLocation']);
        Route::post('/ping', [LivreurApiController::class, 'ping']);
        Route::post('/dispo', [LivreurApiController::class, 'setDispo']);
        Route::get('/livraisons/actives', [LivreurApiController::class, 'activeLivraisons']);
        Route::get('/livraisons/actives/classiques', [LivreurApiController::class, 'activeLivraisonsClassiques']);
        Route::get('/livraisons/actives/lots', [LivreurApiController::class, 'activeLivraisonsLots']);
        Route::get('/livraisons/historique', [LivreurApiController::class, 'historyLivraisons']);
        Route::post('/livraisons/accepter-lot', [LivreurApiController::class, 'accepterLot']);
        Route::post('/livraisons/confirmer-recuperation-lot', [LivreurApiController::class, 'confirmerRecuperationLot']);
        Route::post('/livraisons/confirmer-livraison-lot-par-qr', [LivreurApiController::class, 'confirmerLivraisonLotParQr']);
        Route::post('/livraisons/marquer-lot-livree', [LivreurApiController::class, 'marquerLotLivree']);
        Route::post('/livraisons/marquer-lot-paye', [LivreurApiController::class, 'marquerLotPaye']);
        Route::get('/livraisons/{id}', [LivreurApiController::class, 'showLivraison']);
        Route::post('/livraisons/{id}/accepter', [LivreurApiController::class, 'accepter']);
        Route::post('/livraisons/{id}/confirmer-recuperation', [LivreurApiController::class, 'confirmerRecuperation']);
        Route::post('/livraisons/{id}/confirmer-livraison-par-qr', [LivreurApiController::class, 'confirmerLivraisonParQr']);
        Route::post('/livraisons/{id}/refuser', [LivreurApiController::class, 'refuser']);
        Route::post('/livraisons/{id}/changer-statut', [LivreurApiController::class, 'changerStatut']);
        Route::post('/livraisons/{id}/marquer-paye', [LivreurApiController::class, 'marquerPaye']);
    });

});

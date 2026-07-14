<?php

namespace App\Services;

use App\Models\Commande;
use App\Notifications\OrderStatusChanged;
use Illuminate\Support\Facades\Log;

/**
 * Notifications changement de statut commande (admin) : BDD + push FCM client et livreur.
 */
class OrderStatusNotifier
{
    public static function notify(Commande $commande, string $statusType, string $newStatus): void
    {
        $commande->loadMissing(['employe', 'livraison.livreur']);

        if ($commande->employe) {
            $commande->employe->notify(new OrderStatusChanged($commande, $statusType, $newStatus));
        }

        if (! FcmNotificationService::isConfigured()) {
            return;
        }

        try {
            FcmNotificationService::notifyClientOrderStatusChanged($commande, $statusType, $newStatus);
            FcmNotificationService::notifyLivreurOrderStatusChanged($commande, $statusType, $newStatus);
        } catch (\Throwable $e) {
            Log::error('FCM changement statut commande: '.$e->getMessage());
        }
    }
}

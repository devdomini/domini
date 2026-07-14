<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use App\Notifications\LivreurLivraisonAssigned;
use Illuminate\Support\Facades\Log;

/**
 * Notifications livreur : Pusher temps réel, FCM push, entrée page Notifications (BDD).
 */
class LivraisonAssignmentNotifier
{
    public static function afterSingleAssignment(Livraison $livraison, Commande $commande, bool $notifyClient = true): void
    {
        $livraison = $livraison->fresh();
        $commande = $commande->fresh();
        if (! $livraison || ! $livraison->livreur_id) {
            return;
        }

        $livreur = User::find($livraison->livreur_id);
        if (! $livreur) {
            return;
        }

        self::triggerPusherAssigned($livraison, $commande);

        try {
            $livreur->notify(new LivreurLivraisonAssigned($commande, $livraison));
        } catch (\Throwable $e) {
            Log::error('Notification BDD assignation livreur: '.$e->getMessage());
        }

        try {
            if (FcmNotificationService::isConfigured()) {
                FcmNotificationService::notifyLivreurNewAssignment($livreur, $livraison, $commande);
                if ($notifyClient) {
                    $commande->loadMissing('employe');
                    FcmNotificationService::notifyClientLivreurAssigned($commande);
                }
            }
        } catch (\Throwable $e) {
            Log::error('FCM assignation livreur: '.$e->getMessage());
        }
    }

    /**
     * Fin d’un bloc dispatch lunch : une notif push + BDD (évite 200 FCM).
     */
    public static function afterLunchBatchAssigned(User $livreur, int $count, ?Commande $sampleCommande = null): void
    {
        if ($count <= 0) {
            return;
        }

        PusherService::trigger('livreur.'.$livreur->id, 'livraison.lot.batch.updated', [
            'livreur_id' => $livreur->id,
            'count' => $count,
            'kind' => 'assigned',
        ]);
        PusherService::trigger('traffic-livreur', 'livraison.lot.batch.updated', [
            'livreur_id' => $livreur->id,
            'count' => $count,
            'kind' => 'assigned',
        ]);

        $commande = $sampleCommande ?? Commande::query()
            ->where('is_lunch', true)
            ->whereHas('livraison', fn ($q) => $q->where('livreur_id', $livreur->id)->where('statut', 'assignee'))
            ->latest('id')
            ->first();

        if (! $commande) {
            $commande = new Commande(['is_lunch' => true, 'ref' => 'lot']);
        }

        try {
            $livreur->notify(new LivreurLivraisonAssigned($commande, null, $count));
        } catch (\Throwable $e) {
            Log::error('Notification BDD lot lunch: '.$e->getMessage());
        }

        try {
            if (FcmNotificationService::isConfigured()) {
                FcmNotificationService::notifyLivreurLunchBatch($livreur, $count);
            }
        } catch (\Throwable $e) {
            Log::error('FCM lot lunch: '.$e->getMessage());
        }
    }

    private static function triggerPusherAssigned(Livraison $livraison, Commande $commande): void
    {
        $payload = [
            'id' => $livraison->id,
            'statut' => $livraison->statut,
            'commande_id' => $livraison->commande_id,
            'livreur_id' => $livraison->livreur_id,
            'updated_at' => optional($livraison->updated_at)->toISOString(),
        ];

        PusherService::trigger('traffic-livreur', 'livraison.assigned', ['livraison' => $payload]);
        PusherService::trigger('livreur.'.$livraison->livreur_id, 'livraison.assigned', [
            'livraison' => [
                'id' => $livraison->id,
                'statut' => $livraison->statut,
                'commande_id' => $livraison->commande_id,
            ],
        ]);
        PusherService::trigger('commande.'.$commande->id, 'livraison.assigned', ['livraison' => $payload]);
    }
}

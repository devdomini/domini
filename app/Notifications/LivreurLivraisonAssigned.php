<?php

namespace App\Notifications;

use App\Models\Commande;
use App\Models\Livraison;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LivreurLivraisonAssigned extends Notification
{
    use Queueable;

    public function __construct(
        protected Commande $commande,
        protected ?Livraison $livraison = null,
        protected int $batchCount = 1,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $isLunch = (bool) $this->commande->is_lunch;
        $ref = $this->commande->ref ?? $this->commande->id;

        if ($this->batchCount > 1) {
            return [
                'title' => 'Nouveau lot entreprise',
                'message' => "{$this->batchCount} livraisons entreprise vous sont assignées.",
                'icon' => 'local_shipping',
                'color' => '#2C563C',
                'status_type' => 'livraison_assigned',
                'new_status' => 'assignee',
                'commande_id' => $this->commande->id,
                'livraison_id' => $this->livraison?->id,
                'is_lunch' => true,
                'batch_count' => $this->batchCount,
            ];
        }

        $typeLabel = $isLunch ? 'entreprise' : 'classique';

        return [
            'title' => $isLunch ? 'Livraison entreprise' : 'Nouvelle livraison',
            'message' => "Commande #{$ref} ({$typeLabel}) vous est assignée.",
            'icon' => $isLunch ? 'business' : 'delivery_dining',
            'color' => '#2C563C',
            'status_type' => 'livraison_assigned',
            'new_status' => 'assignee',
            'commande_id' => $this->commande->id,
            'livraison_id' => $this->livraison?->id,
            'is_lunch' => $isLunch,
            'batch_count' => 1,
        ];
    }
}

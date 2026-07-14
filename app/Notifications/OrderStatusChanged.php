<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class OrderStatusChanged extends Notification
{
    use Queueable;

    protected $commande;
    protected $statusType; // 'commande', 'preparation', 'livraison'
    protected $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Commande $commande, $statusType, $newStatus)
    {
        $this->commande = $commande;
        $this->statusType = $statusType;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Contenu partagé BDD + push FCM.
     *
     * @return array{title: string, message: string, commande_id: int, commande_ref: string|null, status_type: string, new_status: string, icon: string, color: string}
     */
    public static function buildPayload(Commande $commande, string $statusType, string $newStatus): array
    {
        $title = 'Mise à jour de commande';
        $message = '';
        $icon = 'info';

        if ($statusType === 'commande') {
            switch ($newStatus) {
                case 'confirmee':
                    $title = 'Commande Confirmée';
                    $message = "Votre commande #{$commande->ref} a été confirmée.";
                    $icon = 'check_circle';
                    break;
                case 'annulee':
                    $title = 'Commande Annulée';
                    $message = "Votre commande #{$commande->ref} a été annulée.";
                    $icon = 'cancel';
                    break;
                case 'terminee':
                    $title = 'Commande Terminée';
                    $message = "Merci ! Votre commande #{$commande->ref} est terminée.";
                    $icon = 'task_alt';
                    break;
            }
        } elseif ($statusType === 'preparation') {
            switch ($newStatus) {
                case 'en_cours':
                    $title = 'Préparation en cours';
                    $message = "Votre commande #{$commande->ref} est en cours de préparation en cuisine.";
                    $icon = 'soup_kitchen';
                    break;
                case 'prete':
                    $title = 'Commande Prête';
                    $message = "Votre commande #{$commande->ref} est prête !";
                    $icon = 'room_service';
                    break;
            }
        } elseif ($statusType === 'livraison') {
            switch ($newStatus) {
                case 'en_cours':
                    $title = 'Livraison en cours';
                    $message = "Votre commande #{$commande->ref} est en route vers vous.";
                    $icon = 'delivery_dining';
                    break;
                case 'livree':
                    $title = 'Commande Livrée';
                    $message = "Votre commande #{$commande->ref} a été livrée. Bon appétit !";
                    $icon = 'verified';
                    break;
                case 'echec':
                    $title = 'Échec de livraison';
                    $message = "La livraison de votre commande #{$commande->ref} a échoué.";
                    $icon = 'error';
                    break;
            }
        }

        if ($message === '') {
            $message = "Le statut de votre commande #{$commande->ref} a changé : {$newStatus}.";
        }

        return [
            'title' => $title,
            'message' => $message,
            'commande_id' => $commande->id,
            'commande_ref' => $commande->ref,
            'status_type' => $statusType,
            'new_status' => $newStatus,
            'icon' => $icon,
            'color' => '#2C563C',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return self::buildPayload($this->commande, $this->statusType, $this->newStatus);
    }
}

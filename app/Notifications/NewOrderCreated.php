<?php

namespace App\Notifications;

use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewOrderCreated extends Notification
{
    use Queueable;

    protected $commande;

    /**
     * Create a new notification instance.
     */
    public function __construct(Commande $commande)
    {
        $this->commande = $commande;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Commande Reçue',
            'message' => "Nous avons bien reçu votre commande #{$this->commande->ref}. Elle est en attente de confirmation.",
            'commande_id' => $this->commande->id,
            'commande_ref' => $this->commande->ref,
            'type' => 'new_order',
            'icon' => 'receipt_long',
            'color' => '#F9BD4B', // Secondary/Accent color
        ];
    }
}

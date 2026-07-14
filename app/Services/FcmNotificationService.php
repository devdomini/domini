<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Livraison;
use App\Models\User;
use App\Notifications\OrderStatusChanged;
use Google\Auth\Credentials\ServiceAccountCredentials;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class FcmNotificationService
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public static function isConfigured(): bool
    {
        return self::credentialsAbsolutePath() !== null;
    }

    public static function sendToUser(?User $user, string $title, string $body, array $data = []): bool
    {
        if (!$user || empty($user->fcm_token)) {
            return false;
        }

        return self::sendToToken($user->fcm_token, $title, $body, $data);
    }

    public static function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        $path = self::credentialsAbsolutePath();
        $projectId = self::resolveProjectId();

        if (!$path || !$projectId) {
            return false;
        }

        try {
            $creds = new ServiceAccountCredentials(self::SCOPE, $path);
            $httpHandler = HttpHandlerFactory::build(new Client);
            $auth = $creds->fetchAuthToken($httpHandler);
            $accessToken = $auth['access_token'] ?? null;
            if (!$accessToken) {
                Log::warning('FCM: jeton OAuth vide');

                return false;
            }

            $stringData = [];
            foreach ($data as $k => $v) {
                $stringData[(string) $k] = is_scalar($v) ? (string) $v : json_encode($v);
            }

            $message = [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'HIGH',
                    'notification' => [
                        'channel_id' => 'domini_push_sound_v3',
                        'sound' => 'default',
                        'notification_priority' => 'PRIORITY_MAX',
                        'visibility' => 'PUBLIC',
                        'default_vibrate_timings' => true,
                    ],
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10',
                        'apns-push-type' => 'alert',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $title,
                                'body' => $body,
                            ],
                            'sound' => 'default',
                            'badge' => 1,
                            'interruption-level' => 'time-sensitive',
                        ],
                    ],
                ],
            ];
            if (count($stringData) > 0) {
                $message['data'] = $stringData;
            }

            $payload = ['message' => $message];

            $url = 'https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send';
            $response = (new Client)->post($url, [
                'headers' => [
                    'Authorization' => 'Bearer '.$accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
                'http_errors' => false,
                'timeout' => 15,
            ]);

            $code = $response->getStatusCode();
            if ($code >= 200 && $code < 300) {
                return true;
            }

            Log::warning('FCM: envoi refusé', [
                'status' => $code,
                'body' => (string) $response->getBody(),
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('FCM: '.$e->getMessage());

            return false;
        }
    }

    public static function notifyLivreurNewAssignment(User $livreur, Livraison $livraison, Commande $commande): void
    {
        $isLunch = (bool) $commande->is_lunch;
        $ref = $commande->ref ?? $commande->id;

        self::sendToUser(
            $livreur->fresh(),
            $isLunch ? 'Livraison entreprise' : 'Nouvelle livraison',
            $isLunch
                ? "Commande entreprise #{$ref} vous est assignée."
                : 'Commande #'.$ref.' vous est assignée.',
            [
                'type' => 'livraison_assigned',
                'commande_id' => (string) $commande->id,
                'livraison_id' => (string) $livraison->id,
                'is_lunch' => $isLunch ? '1' : '0',
            ]
        );
    }

    public static function notifyLivreurLunchBatch(User $livreur, int $count): void
    {
        $body = $count === 1
            ? '1 nouvelle livraison entreprise vous est assignée.'
            : "{$count} livraisons entreprise vous sont assignées.";

        self::sendToUser(
            $livreur->fresh(),
            'Nouveau lot entreprise',
            $body,
            [
                'type' => 'livraison_batch_assigned',
                'count' => (string) $count,
            ]
        );
    }

    public static function notifyClientLivreurAssigned(Commande $commande): void
    {
        $client = $commande->employe;
        if (!$client) {
            return;
        }

        self::sendToUser(
            $client,
            'Livreur assigné',
            'Un livreur a été assigné à votre commande #'.($commande->ref ?? $commande->id).'.',
            [
                'type' => 'livreur_assigned',
                'commande_id' => (string) $commande->id,
            ]
        );
    }

    public static function notifyClientOrderCreated(Commande $commande): void
    {
        $client = $commande->employe;
        if (!$client) {
            return;
        }

        self::sendToUser(
            $client,
            'Commande enregistrée',
            'Votre commande #'.($commande->ref ?? $commande->id).' a bien été créée.',
            [
                'type' => 'commande_created',
                'commande_id' => (string) $commande->id,
            ]
        );
    }

    public static function notifyClientLivraisonEvent(Commande $commande, string $event, string $title, string $body): void
    {
        $client = $commande->employe;
        if (!$client) {
            return;
        }

        self::sendToUser(
            $client,
            $title,
            $body,
            [
                'type' => $event,
                'commande_id' => (string) $commande->id,
            ]
        );
    }

    public static function notifyClientOrderStatusChanged(Commande $commande, string $statusType, string $newStatus): void
    {
        $client = $commande->employe;
        if (! $client) {
            return;
        }

        $payload = OrderStatusChanged::buildPayload($commande, $statusType, $newStatus);

        self::sendToUser(
            $client,
            $payload['title'],
            $payload['message'],
            [
                'type' => 'order_status_changed',
                'commande_id' => (string) $commande->id,
                'status_type' => $statusType,
                'new_status' => $newStatus,
            ]
        );
    }

    public static function notifyLivreurOrderStatusChanged(Commande $commande, string $statusType, string $newStatus): void
    {
        $livreur = $commande->livraison?->livreur;
        if (! $livreur) {
            return;
        }

        $ref = $commande->ref ?? $commande->id;
        $title = 'Mise à jour commande';
        $body = null;

        if ($statusType === 'commande' && $newStatus === 'annulee') {
            $title = 'Commande annulée';
            $body = "La commande #{$ref} qui vous était assignée a été annulée.";
        } elseif ($statusType === 'preparation' && $newStatus === 'prete') {
            $title = 'Commande prête';
            $body = "La commande #{$ref} est prête à être récupérée.";
        } elseif ($statusType === 'livraison') {
            $title = 'Statut livraison mis à jour';
            $body = match ($newStatus) {
                'en_attente' => "La commande #{$ref} est en attente de livraison.",
                'en_cours' => "La livraison de la commande #{$ref} est en cours.",
                'livree' => "La commande #{$ref} a été marquée comme livrée.",
                'echec' => "La livraison de la commande #{$ref} a échoué.",
                default => "Le statut de livraison de la commande #{$ref} a changé.",
            };
        }

        if ($body === null) {
            return;
        }

        self::sendToUser(
            $livreur,
            $title,
            $body,
            [
                'type' => 'order_status_changed',
                'commande_id' => (string) $commande->id,
                'livraison_id' => (string) ($commande->livraison?->id ?? ''),
                'status_type' => $statusType,
                'new_status' => $newStatus,
                'audience' => 'livreur',
            ]
        );
    }

    /**
     * Compteurs pour l’admin (diagnostic « 0 destinataires »).
     *
     * @return array{employe_total: int, employe_fcm: int, livreur_total: int, livreur_fcm: int, all_fcm: int}
     */
    public static function campaignRecipientStats(): array
    {
        $hasFcm = fn ($q) => (clone $q)->whereNotNull('fcm_token')->where('fcm_token', '!=', '');

        $qEmp = User::query()->where('role', 'employe');
        $qLiv = User::query()->where('role', 'livreur');

        return [
            'employe_total' => (clone $qEmp)->count(),
            'employe_fcm' => $hasFcm($qEmp)->count(),
            'livreur_total' => (clone $qLiv)->count(),
            'livreur_fcm' => $hasFcm($qLiv)->count(),
            'all_fcm' => User::query()
                ->whereIn('role', ['employe', 'livreur'])
                ->whereNotNull('fcm_token')
                ->where('fcm_token', '!=', '')
                ->count(),
        ];
    }

    /**
     * Campagne push (promo, fidélisation, message) : envoi à tous les comptes app ayant un jeton FCM.
     * Pas de filtre is_active : un compte « désactivé » peut encore avoir l’app installée ; l’important est le jeton.
     *
     * @param  array<int, string>  $roles  Sous-ensemble de employe, livreur (vide = les deux)
     * @return array{targets: int, sent: int, failed: int}
     */
    public static function broadcastCampaign(array $roles, string $title, string $body, string $kind = 'message'): array
    {
        $allowedRoles = ['employe', 'livreur'];
        $roles = array_values(array_intersect($roles, $allowedRoles));
        if ($roles === []) {
            $roles = $allowedRoles;
        }

        if (! self::isConfigured()) {
            return ['targets' => 0, 'sent' => 0, 'failed' => 0];
        }

        $data = [
            'type' => 'campaign',
            'kind' => $kind,
        ];

        $query = User::query()
            ->whereIn('role', $roles)
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '');

        $targets = (clone $query)->count();
        $sent = 0;
        $failed = 0;

        $query->orderBy('id')->chunk(50, function ($users) use ($title, $body, $data, &$sent, &$failed) {
            foreach ($users as $user) {
                if (self::sendToUser($user, $title, $body, $data)) {
                    $sent++;
                } else {
                    $failed++;
                }
            }
            usleep(50_000);
        });

        return [
            'targets' => $targets,
            'sent' => $sent,
            'failed' => $failed,
        ];
    }

    /** À appeler depuis un back-office lorsque le livreur change d’entrepôt (warehouse_id). */
    public static function notifyLivreurWarehouseChanged(User $livreur, string $warehouseLabel = ''): void
    {
        $msg = $warehouseLabel !== ''
            ? 'Vous êtes rattaché à l’entrepôt : '.$warehouseLabel.'.'
            : 'Votre entrepôt d’affectation a été mis à jour.';

        self::sendToUser(
            $livreur->fresh(),
            'Entrepôt mis à jour',
            $msg,
            ['type' => 'warehouse_changed']
        );
    }

    private static function credentialsAbsolutePath(): ?string
    {
        $configured = config('services.firebase.credentials');
        if (! is_string($configured) || $configured === '') {
            return null;
        }

        $full = (str_starts_with($configured, DIRECTORY_SEPARATOR)
            || preg_match('#^[A-Za-z]:[\\\\/]#', $configured))
            ? $configured
            : base_path($configured);

        return is_readable($full) ? $full : null;
    }

    private static function resolveProjectId(): ?string
    {
        $fromEnv = config('services.firebase.project_id');
        if (is_string($fromEnv) && $fromEnv !== '') {
            return $fromEnv;
        }

        $path = self::credentialsAbsolutePath();
        if (! $path) {
            return null;
        }

        $json = json_decode((string) file_get_contents($path), true);

        return is_array($json) && ! empty($json['project_id']) ? (string) $json['project_id'] : null;
    }
}

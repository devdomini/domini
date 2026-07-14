<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OrangeSmsService
{
    // Identifiants API Orange
    protected $clientId = 'XmvK8mAW1FoFaaK0IHlsbmWt8nceqs5C';
    protected $clientSecret = 'pMN8qnvQz9m5G632ukLrMfvzTEW9Kh4AM8JiqKJrRGdI';
    protected $from = '2250700000000'; // Numéro Orange d'expédition
    protected $senderName; // Nom d'expéditeur

    public function __construct()
    {
        // Utiliser FITURING car c'est le senderName validé par Orange
        $this->senderName = env('ORANGE_SENDER_NAME', 'FITURING');
    }

    /**
     * Récupérer le token d'authentification OAuth
     */
    public function getToken()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ])->asForm()->post('https://api.orange.com/oauth/v3/token', [
            'grant_type' => 'client_credentials',
        ]);

        return $response->json()['access_token'] ?? null;
    }

    /**
     * Générer un code de vérification à 6 chiffres
     */
    public function generateVerificationCode()
    {
        return sprintf('%06d', mt_rand(0, 999999));
    }

    /**
     * Normaliser le numéro de téléphone au format international
     */
    private function normalizePhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques sauf le +
        $phone = preg_replace('/[^0-9+]/', '', trim($phone));
        
        // Si le numéro commence par 0 (format local ivoirien)
        if (strpos($phone, '0') === 0 && strlen($phone) == 10) {
            // Convertir 0XXXXXXXXX en 2250XXXXXXXXX
            $phone = '225' . substr($phone, 1);
        }
        
        // Supprimer le + si présent (l'API attend le numéro sans +)
        $phone = str_replace('+', '', $phone);
        
        return $phone;
    }

    /**
     * Envoyer un SMS via l'API Orange
     */
    public function sendSms($to, $message)
    {
        // Normaliser le numéro de téléphone
        $to = $this->normalizePhoneNumber($to);

        $token = $this->getToken();

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Token non récupéré'
            ];
        }

        $url = 'https://api.orange.com/smsmessaging/v1/outbound/tel%3A%2B' . $this->from . '/requests';

        $response = Http::withToken($token)
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post($url, [
                'outboundSMSMessageRequest' => [
                    'address' => "tel:+$to",
                    'senderAddress' => "tel:+$this->from",
                    'senderName' => $this->senderName,
                    'outboundSMSTextMessage' => [
                        'message' => $message,
                    ],
                ]
            ]);

        $responseData = $response->json();

        // Log the response for debugging
        \Illuminate\Support\Facades\Log::info('Orange SMS API Response', [
            'status' => $response->status(),
            'body' => $responseData,
            'to' => $to,
            'message' => $message
        ]);

        // Check if the response indicates success (HTTP 201 Created)
        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'SMS envoyé avec succès',
                'data' => $responseData
            ];
        } else {
            return [
                'success' => false,
                'message' => $responseData['message'] ?? 'Erreur lors de l\'envoi du SMS',
                'error' => $responseData
            ];
        }
    }

}

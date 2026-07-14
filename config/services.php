<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    | Clé API Google Maps (JavaScript) — à restreindre par référent HTTP dans la console Google Cloud.
    | .env : GOOGLE_MAPS_API_KEY=votre_cle
    */
    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    /*
    | FCM HTTP v1 — fichier JSON « compte de service » (Console Firebase > Paramètres du projet >
    | Comptes de service > Générer une nouvelle clé privée). Ce n’est pas google-services.json.
    | .env : FIREBASE_CREDENTIALS=storage/app/firebase-service-account.json
    | Optionnel : FIREBASE_PROJECT_ID=dominifood-12c49 (sinon lu depuis le JSON).
    */
    'firebase' => [
        'credentials' => env('FIREBASE_CREDENTIALS'),
        'project_id' => env('FIREBASE_PROJECT_ID'),
    ],

    'orange' => [
        'client_id' => env('ORANGE_CLIENT_ID', 'XmvK8mAW1FoFaaK0IHlsbmWt8nceqs5C'),
        'client_secret' => env('ORANGE_CLIENT_SECRET', 'pMN8qnvQz9m5G632ukLrMfvzTEW9Kh4AM8JiqKJrRGdI'),
        'authorization_header' => env('ORANGE_AUTH_HEADER', 'Basic WG12SzhtQVcxRm9GYWFLMElIbHNibVd0OG5jZXFzNUM6cE1OOHFudlF6OW01RzYzMnVrTHJNZnZ6VEVXOUtoNEFNOEppcUtKclJHZEk='),
        'sender_name' => env('ORANGE_SENDER_NAME', 'SMS 487507'),
        'sender_number' => env('ORANGE_SENDER_NUMBER', '00000000'),
    ],

];

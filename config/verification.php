<?php

return [

    'code_ttl_minutes' => (int) env('VERIFY_CODE_TTL_MINUTES', 10),

    /*
    | Canal par défaut à l'inscription (employe = client, livreur, commercial).
    | Tous les rôles mobiles peuvent basculer vers sms ou email via le paramètre API `channel`.
    */
    'channels' => [
        'employe' => env('VERIFY_CHANNEL_EMPLOYE', 'sms'),
        'livreur' => env('VERIFY_CHANNEL_LIVREUR', 'sms'),
        'commercial' => env('VERIFY_CHANNEL_COMMERCIAL', 'email'),
        'default' => env('VERIFY_CHANNEL_DEFAULT', 'sms'),
    ],

    'allowed_channels' => ['sms', 'email'],

];

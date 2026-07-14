<?php

namespace App\Services;

use Pusher\Pusher;
use Pusher\PusherException;

class PusherService
{
    private static ?Pusher $client = null;

    public static function enabled(): bool
    {
        return (bool) (env('PUSHER_APP_KEY') && env('PUSHER_APP_SECRET') && env('PUSHER_APP_ID'));
    }

    public static function client(): Pusher
    {
        if (self::$client) return self::$client;

        $options = [
            'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
            'useTLS' => true,
        ];

        self::$client = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        return self::$client;
    }

    /**
     * @param string $channel
     * @param string $event
     * @param array<string,mixed> $payload
     */
    public static function trigger(string $channel, string $event, array $payload): void
    {
        if (!self::enabled()) return;
        try {
            self::client()->trigger($channel, $event, $payload);
        } catch (PusherException $e) {
            // On évite de casser le flux applicatif si Pusher est KO.
            // Vous pouvez logger ici si besoin.
        } catch (\Throwable $e) {
        }
    }
}


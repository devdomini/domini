<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoService
{
    /**
     * @return array{lat: float, lng: float}|null
     */
    public static function geocodeAddress(string $query, ?string $apiKey = null): ?array
    {
        $apiKey = $apiKey ?? config('services.google_maps.api_key');
        $query = trim($query);
        if (! $apiKey || $query === '') {
            return null;
        }

        try {
            $response = Http::timeout(8)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $query,
                'region' => 'ci',
                'key' => $apiKey,
            ]);
        } catch (\Throwable) {
            return null;
        }

        if (! $response->ok()) {
            return null;
        }

        $data = $response->json();
        if (($data['status'] ?? '') !== 'OK' || empty($data['results'][0]['geometry']['location'])) {
            return null;
        }

        $loc = $data['results'][0]['geometry']['location'];

        return [
            'lat' => (float) $loc['lat'],
            'lng' => (float) $loc['lng'],
        ];
    }

    public static function haversineMeters(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthM = 6371000.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;

        return $earthM * (2 * atan2(sqrt($a), sqrt(1 - $a)));
    }
}

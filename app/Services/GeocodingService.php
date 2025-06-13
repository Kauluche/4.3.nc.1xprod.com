<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    protected $baseUrl = 'https://api-adresse.data.gouv.fr/search/';

    /**
     * Convertit une adresse en coordonnées géographiques
     *
     * @param string $address
     * @param string $postalCode
     * @param string $city
     * @return array
     */
    public function geocodeAddress(string $address, string $postalCode, string $city): array
    {
        try {
            $query = urlencode("{$address}, {$postalCode} {$city}");
            $response = Http::get("{$this->baseUrl}?q={$query}&limit=1");

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['features'])) {
                    $coordinates = $data['features'][0]['geometry']['coordinates'];
                    return [
                        'success' => true,
                        'latitude' => $coordinates[1],
                        'longitude' => $coordinates[0]
                    ];
                }
            }

            Log::warning('Échec de la géolocalisation', [
                'address' => $address,
                'postal_code' => $postalCode,
                'city' => $city,
                'response' => $response->json() ?? 'Pas de réponse'
            ]);

            return [
                'success' => false,
                'message' => 'Impossible de géolocaliser l\'adresse'
            ];

        } catch (\Exception $e) {
            Log::error('Erreur lors de la géolocalisation', [
                'error' => $e->getMessage(),
                'address' => $address,
                'postal_code' => $postalCode,
                'city' => $city
            ]);

            return [
                'success' => false,
                'message' => 'Erreur lors de la géolocalisation'
            ];
        }
    }
} 
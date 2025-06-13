<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('GEO_API_URL', 'https://api-adresse.data.gouv.fr');
    }

    /**
     * Suggérer des villes basées sur une requête
     * 
     * @param string $query
     * @return array
     */
    public function suggestCities(string $query): array
    {
        try {
            $response = Http::get("{$this->baseUrl}/search", [
                'q' => $query,
                'type' => 'municipality',
                'limit' => 10
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $cities = [];
                
                foreach ($data['features'] ?? [] as $feature) {
                    $properties = $feature['properties'] ?? [];
                    $cities[] = [
                        'name' => $properties['city'] ?? '',
                        'postal_code' => $properties['postcode'] ?? '',
                        'label' => $properties['label'] ?? '',
                    ];
                }
                
                return [
                    'success' => true,
                    'data' => $cities
                ];
            }
            
            Log::warning('GeoAPI returned an error', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return [
                'success' => false,
                'message' => 'Impossible de récupérer les villes depuis l\'API'
            ];
        } catch (\Exception $e) {
            Log::error('Error while fetching cities from GeoAPI', [
                'error' => $e->getMessage(),
                'query' => $query
            ]);
            
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la recherche des villes'
            ];
        }
    }

    /**
     * Récupérer les coordonnées géographiques d'une adresse
     * 
     * @param string $address
     * @param string $postalCode
     * @param string $city
     * @return array
     */
    public function getCoordinates(string $address, string $postalCode, string $city): array
    {
        try {
            $query = trim("$address $postalCode $city");
            $response = Http::get("{$this->baseUrl}/search", [
                'q' => $query,
                'limit' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['features'])) {
                    $coordinates = $data['features'][0]['geometry']['coordinates'] ?? null;
                    
                    if ($coordinates) {
                        return [
                            'success' => true,
                            'data' => [
                                'longitude' => $coordinates[0],
                                'latitude' => $coordinates[1]
                            ]
                        ];
                    }
                }
                
                return [
                    'success' => false,
                    'message' => 'Coordonnées introuvables pour cette adresse'
                ];
            }
            
            Log::warning('GeoAPI returned an error for coordinates', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);
            
            return [
                'success' => false,
                'message' => 'Impossible de récupérer les coordonnées depuis l\'API'
            ];
        } catch (\Exception $e) {
            Log::error('Error while fetching coordinates from GeoAPI', [
                'error' => $e->getMessage(),
                'address' => $query ?? ''
            ]);
            
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la récupération des coordonnées'
            ];
        }
    }
} 
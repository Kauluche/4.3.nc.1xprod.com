<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FinessApiService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('FINESS_API_URL', 'https://finess.sante.gouv.fr/finessWebService/rest/establishment.json');
    }

    /**
     * Rechercher des pharmacies par ville ou code postal
     *
     * @param string $query Ville ou code postal
     * @return array
     */
    public function searchPharmacies(string $query): array
    {
        try {
            // Paramètres pour l'API
            $params = [
                'q' => "activitePrincipaleUniteLegale:62.01Z AND denominationUniteLegale:*{$query}*",
                'nombre' => 50
            ];
            
            Log::info('Tentative de connexion à l\'API SIRENE', [
                'url' => $this->baseUrl,
                'query' => $query, 
                'params' => $params
            ]);
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('FINESS_API_KEY'),
                'Accept' => 'application/json'
            ])->get($this->baseUrl . '/siret', $params);
            
            Log::info('Réponse de l\'API des professionnels de santé', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $pharmacies = [];
                
                if (isset($data['records']) && is_array($data['records'])) {
                    foreach ($data['records'] as $record) {
                        $etablissement = $record['record']['fields'];
                        
                        $pharmacy = [
                            'name' => $etablissement['nom'] ?? 'Pharmacie sans nom',
                            'address' => $etablissement['adresse3'] ?? '',
                            'city' => $etablissement['dep_name'] ?? '',
                            'postal_code' => $etablissement['code_postal'] ?? '',
                            'phone' => $etablissement['telephone'] ?? '',
                            'email' => 'contact@' . strtolower(str_replace([' ', '\'', '-'], '', $etablissement['nom'] ?? 'pharmacie')) . '.fr',
                            'finess_id' => $etablissement['code_commune'] ?? '',
                            'latitude' => $etablissement['coordonnees']['lat'] ?? null,
                            'longitude' => $etablissement['coordonnees']['lon'] ?? null
                        ];
                        
                        $pharmacies[] = $pharmacy;
                    }
                }
                
                return [
                    'success' => true,
                    'data' => $pharmacies
                ];
            }
            
            Log::error('Échec de la requête à l\'API des professionnels de santé', [
                'status' => $response->status(),
                'body' => $response->body(),
                'error' => $response->json() ?? 'Pas de réponse JSON'
            ]);
            
            return [
                'success' => false,
                'message' => 'Impossible de récupérer les données depuis l\'API des professionnels de santé (Statut: ' . $response->status() . ')'
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche de pharmacies via l\'API des professionnels de santé', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'query' => $query
            ]);
            
            return [
                'success' => false,
                'message' => 'Une erreur est survenue lors de la recherche de pharmacies: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Génère des pharmacies factices pour les tests ou en cas d'échec de l'API
     *
     * @param string $city Ville
     * @return array
     */
    public function getMockPharmacies(string $city): array
    {
        $city = ucfirst($city);
        $postalCode = '75000'; // Code postal par défaut
        
        // Liste des pharmacies factices
        $pharmacies = [
            [
                'name' => 'Pharmacie Centrale de ' . $city,
                'address' => '1 Place Centrale',
                'city' => $city,
                'postal_code' => $postalCode,
                'phone' => '01 23 45 67 89',
                'email' => 'contact@pharmaciecentrale.fr',
                'finess_id' => '750' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'latitude' => 48.8566 + (mt_rand(-100, 100) / 1000),
                'longitude' => 2.3522 + (mt_rand(-100, 100) / 1000)
            ],
            [
                'name' => 'Grande Pharmacie de ' . $city,
                'address' => '25 Avenue Principale',
                'city' => $city,
                'postal_code' => $postalCode,
                'phone' => '01 23 45 67 90',
                'email' => 'contact@grandepharmacie.fr',
                'finess_id' => '750' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'latitude' => 48.8566 + (mt_rand(-100, 100) / 1000),
                'longitude' => 2.3522 + (mt_rand(-100, 100) / 1000)
            ],
            [
                'name' => 'Pharmacie du Marché',
                'address' => '8 Rue du Commerce',
                'city' => $city,
                'postal_code' => $postalCode,
                'phone' => '01 23 45 67 91',
                'email' => 'contact@pharmaciedumarche.fr',
                'finess_id' => '750' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'latitude' => 48.8566 + (mt_rand(-100, 100) / 1000),
                'longitude' => 2.3522 + (mt_rand(-100, 100) / 1000)
            ],
            [
                'name' => 'Pharmacie de la Gare',
                'address' => '3 Place de la Gare',
                'city' => $city,
                'postal_code' => $postalCode,
                'phone' => '01 23 45 67 92',
                'email' => 'contact@pharmaciegare.fr',
                'finess_id' => '750' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'latitude' => 48.8566 + (mt_rand(-100, 100) / 1000),
                'longitude' => 2.3522 + (mt_rand(-100, 100) / 1000)
            ],
            [
                'name' => 'Pharmacie des Écoles',
                'address' => '15 Rue de l\'Université',
                'city' => $city,
                'postal_code' => $postalCode,
                'phone' => '01 23 45 67 93',
                'email' => 'contact@pharmacieecoles.fr',
                'finess_id' => '750' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT),
                'latitude' => 48.8566 + (mt_rand(-100, 100) / 1000),
                'longitude' => 2.3522 + (mt_rand(-100, 100) / 1000)
            ]
        ];
        
        return $pharmacies;
    }
} 
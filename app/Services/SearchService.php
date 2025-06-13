<?php

namespace App\Services;

use App\Models\Pharmacy;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SearchService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = 'https://data.iledefrance.fr/api/explore/v2.1/catalog/datasets/carte-des-pharmacies-dile-de-france/records';
    }

    protected function formatPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Si le numéro commence par 33, le remplacer par 0
        if (strpos($phone, '33') === 0) {
            $phone = '0' . substr($phone, 2);
        }
        
        // S'assurer que le numéro commence par 0 et a 10 chiffres
        if (strlen($phone) === 9 && strpos($phone, '0') !== 0) {
            $phone = '0' . $phone;
        }
        
        // Formater le numéro avec des espaces
        if (strlen($phone) === 10) {
            return substr($phone, 0, 2) . ' ' . 
                   substr($phone, 2, 2) . ' ' . 
                   substr($phone, 4, 2) . ' ' . 
                   substr($phone, 6, 2) . ' ' . 
                   substr($phone, 8, 2);
        }
        
        return $phone;
    }

    /**
     * Recherche les pharmacies par département
     *
     * @param string $department
     * @return \Illuminate\Support\Collection
     */
    public function searchByDepartment(string $department)
    {
        Log::info('Recherche de pharmacies par département', ['department' => $department]);

        try {
            $response = Http::get($this->apiUrl, [
                'where' => "departement = {$department}",
                'limit' => 100
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $pharmacies = collect($data['results'])->map(function ($pharmacy) {
                    return [
                        'name' => $pharmacy['rs'],
                        'address' => $pharmacy['numvoie'] . ' ' . $pharmacy['typvoie'] . ' ' . $pharmacy['voie'],
                        'city' => $pharmacy['commune'],
                        'postal_code' => $pharmacy['cp'],
                        'phone' => $this->formatPhoneNumber($pharmacy['telephone']),
                        'latitude' => $pharmacy['lat'],
                        'longitude' => $pharmacy['lng']
                    ];
                });

                Log::info('Résultats trouvés', [
                    'count' => $pharmacies->count()
                ]);

                return $pharmacies;
            }

            Log::error('Erreur lors de l\'appel à l\'API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return collect();

        } catch (\Exception $e) {
            Log::error('Exception lors de la recherche de pharmacies', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return collect();
        }
    }
} 
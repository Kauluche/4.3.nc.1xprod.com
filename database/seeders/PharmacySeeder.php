<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Zone;

class PharmacySeeder extends Seeder
{
    public function run(): void
    {
        $pharmacies = [
            [
                'name' => 'Pharmacie du Centre',
                'address' => '123 rue de Rivoli',
                'city' => 'Paris',
                'postal_code' => '75001',
                'phone' => '01 23 45 67 89',
                'email' => 'contact@pharmacie-centre.fr',
            ],
            [
                'name' => 'Pharmacie Saint-Martin',
                'address' => '456 rue du Temple',
                'city' => 'Paris',
                'postal_code' => '75003',
                'phone' => '01 98 76 54 32',
                'email' => 'contact@pharmacie-saint-martin.fr',
            ],
            [
                'name' => 'Pharmacie des Lilas',
                'address' => '789 rue des Lilas',
                'city' => 'Paris',
                'postal_code' => '75019',
                'phone' => '01 45 67 89 01',
                'email' => 'contact@pharmacie-lilas.fr',
            ],
        ];

        $commercials = User::where('role', 'commercial')->get();
        $commercialCount = $commercials->count();

        if ($commercialCount === 0) {
            throw new \Exception('Aucun commercial trouvé. Veuillez exécuter UserSeeder d\'abord.');
        }

        // Vérifier que les commerciaux ont une zone assignée
        foreach ($commercials as $commercial) {
            if (!$commercial->zone_id) {
                // Assigner une zone aléatoire si aucune n'est assignée
                $zone = Zone::inRandomOrder()->first();
                if ($zone) {
                    $commercial->update(['zone_id' => $zone->id]);
                } else {
                    throw new \Exception('Aucune zone trouvée. Veuillez exécuter ZoneSeeder d\'abord.');
                }
            }
        }

        foreach ($pharmacies as $index => $pharmacy) {
            $commercial = $commercials[$index % $commercialCount];
            Pharmacy::create([
                'name' => $pharmacy['name'],
                'address' => $pharmacy['address'],
                'city' => $pharmacy['city'],
                'postal_code' => $pharmacy['postal_code'],
                'phone' => $pharmacy['phone'],
                'email' => $pharmacy['email'],
                'commercial_id' => $commercial->id,
                'zone_id' => $commercial->zone_id,
            ]);
        }
    }
}
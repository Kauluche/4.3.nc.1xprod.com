<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Zone;
use App\Models\Pharmacy;
use App\Models\Order;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Création des zones
        $zones = [
            ['name' => 'Paris Nord', 'description' => 'Zone couvrant le nord de Paris'],
            ['name' => 'Paris Sud', 'description' => 'Zone couvrant le sud de Paris'],
            ['name' => 'Lyon Centre', 'description' => 'Zone couvrant le centre de Lyon'],
            ['name' => 'Lyon Est', 'description' => 'Zone couvrant l\'est de Lyon'],
            ['name' => 'Marseille Nord', 'description' => 'Zone couvrant le nord de Marseille'],
            ['name' => 'Marseille Sud', 'description' => 'Zone couvrant le sud de Marseille'],
            ['name' => 'Bordeaux Centre', 'description' => 'Zone couvrant le centre de Bordeaux'],
            ['name' => 'Bordeaux Ouest', 'description' => 'Zone couvrant l\'ouest de Bordeaux'],
            ['name' => 'Toulouse Est', 'description' => 'Zone couvrant l\'est de Toulouse'],
            ['name' => 'Toulouse Ouest', 'description' => 'Zone couvrant l\'ouest de Toulouse'],
            ['name' => 'Nantes Centre', 'description' => 'Zone couvrant le centre de Nantes'],
            ['name' => 'Nantes Sud', 'description' => 'Zone couvrant le sud de Nantes'],
        ];

        foreach ($zones as $zone) {
            Zone::create($zone);
        }

        // Création des administrateurs
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@naturacorp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Support',
            'email' => 'support@naturacorp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'first_name' => 'Admin',
            'last_name' => 'Technique',
            'email' => 'tech@naturacorp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Liste des commerciaux avec leurs noms
        $commercials = [
            ['first_name' => 'Jean', 'last_name' => 'Dupont'],
            ['first_name' => 'Marie', 'last_name' => 'Martin'],
            ['first_name' => 'Pierre', 'last_name' => 'Bernard'],
            ['first_name' => 'Sophie', 'last_name' => 'Petit'],
            ['first_name' => 'Lucas', 'last_name' => 'Robert'],
            ['first_name' => 'Emma', 'last_name' => 'Richard'],
            ['first_name' => 'Thomas', 'last_name' => 'Dubois'],
            ['first_name' => 'Julie', 'last_name' => 'Moreau'],
            ['first_name' => 'Antoine', 'last_name' => 'Laurent'],
            ['first_name' => 'Clara', 'last_name' => 'Simon'],
            ['first_name' => 'Hugo', 'last_name' => 'Michel'],
            ['first_name' => 'Léa', 'last_name' => 'Leroy'],
        ];

        // Création des commerciaux avec leurs zones assignées
        foreach ($commercials as $i => $commercial) {
            $user = User::create([
                'first_name' => $commercial['first_name'],
                'last_name' => $commercial['last_name'],
                'email' => strtolower($commercial['first_name'] . '.' . $commercial['last_name'] . '@naturacorp.com'),
                'password' => Hash::make('password'),
                'role' => 'commercial',
                'zone_id' => $i + 1,
            ]);

            // Mise à jour de la zone avec l'ID du commercial
            Zone::where('id', $i + 1)->update(['commercial_id' => $user->id]);
        }

        // Création des pharmacies pour chaque commercial
        $commercials = User::where('role', 'commercial')->get();
        foreach ($commercials as $commercial) {
            for ($i = 1; $i <= 5; $i++) {
                Pharmacy::create([
                    'name' => "Pharmacie {$commercial->last_name} {$i}",
                    'address' => "Adresse {$i}",
                    'city' => "Ville {$i}",
                    'postal_code' => "7500{$i}",
                    'phone' => "012345678{$i}",
                    'email' => "pharmacie{$i}@example.com",
                    'commercial_id' => $commercial->id,
                    'zone_id' => $commercial->zone_id,
                ]);
            }
        }

        // Création des commandes pour chaque pharmacie
        Pharmacy::all()->each(function ($pharmacy) {
            $orderCount = rand(2, 5);
            for ($i = 1; $i <= $orderCount; $i++) {
                Order::create([
                    'pharmacy_id' => $pharmacy->id,
                    'commercial_id' => $pharmacy->commercial_id,
                    'status' => rand(0, 1) ? 'pending' : 'completed',
                    'total' => rand(100, 1000),
                    'discount' => rand(0, 100),
                    'notes' => "Note pour la commande {$i}",
                ]);
            }
        });

        // Création des documents pour chaque pharmacie
        Pharmacy::all()->each(function ($pharmacy) {
            $documentCount = rand(1, 3);
            for ($i = 1; $i <= $documentCount; $i++) {
                Document::create([
                    'pharmacy_id' => $pharmacy->id,
                    'commercial_id' => $pharmacy->commercial_id,
                    'title' => "Document {$i}",
                    'description' => "Description du document {$i}",
                    'type' => rand(0, 1) ? 'devis' : 'facture',
                    'file_path' => "documents/{$pharmacy->id}/document{$i}.pdf",
                ]);
            }
        });
    }
}
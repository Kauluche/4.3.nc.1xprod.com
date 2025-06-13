<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\User;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run()
    {
        // Récupérer tous les commerciaux
        $commercials = User::where('role', 'commercial')->get();
        
        // Pour chaque commercial
        foreach ($commercials as $commercial) {
            // Récupérer ses pharmacies
            $pharmacies = $commercial->pharmacies;
            
            // Pour chaque pharmacie, créer 5-10 commandes
            foreach ($pharmacies as $pharmacy) {
                $numberOfOrders = rand(5, 10);
                
                for ($i = 0; $i < $numberOfOrders; $i++) {
                    // Générer une date aléatoire dans les 6 derniers mois
                    $randomDate = Carbon::now()->subMonths(rand(0, 6))->addDays(rand(0, 30));
                    
                    // Générer un prix aléatoire entre 100 et 5000 euros
                    $price = rand(100, 5000);
                    
                    // Générer un statut aléatoire
                    $status = rand(0, 100);
                    if ($status < 70) { // 70% des commandes sont complétées
                        $status = 'completed';
                    } elseif ($status < 90) { // 20% sont en attente
                        $status = 'pending';
                    } else { // 10% sont annulées
                        $status = 'cancelled';
                    }
                    
                    Order::create([
                        'pharmacy_id' => $pharmacy->id,
                        'commercial_id' => $commercial->id,
                        'status' => $status,
                        'price' => $price,
                        'total' => $price, // Pour l'instant, le total est égal au prix
                        'created_at' => $randomDate,
                        'updated_at' => $randomDate,
                    ]);
                }
            }
        }
    }
} 
<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $types = [
            'order_status' => 'Le statut de votre commande a été mis à jour',
            'new_document' => 'Un nouveau document a été ajouté',
            'pharmacy_status' => 'Le statut d\'une pharmacie a été modifié',
            'zone_update' => 'Les informations de votre zone ont été mises à jour',
            'goal_achievement' => 'Félicitations ! Vous avez atteint votre objectif mensuel',
        ];

        foreach ($users as $user) {
            // Créer 5-10 notifications par utilisateur
            $notificationCount = rand(5, 10);

            for ($i = 0; $i < $notificationCount; $i++) {
                $type = array_rand($types);
                $isRead = rand(0, 1);
                $createdAt = now()->subDays(rand(1, 30));

                DB::table('notifications')->insert([
                    'id' => uniqid('', true),
                    'type' => $type,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $user->id,
                    'data' => json_encode([
                        'message' => $types[$type],
                        'type' => $type,
                    ]),
                    'read_at' => $isRead ? $createdAt->addHours(rand(1, 24)) : null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->addHours(rand(1, 24)),
                ]);
            }
        }
    }
} 
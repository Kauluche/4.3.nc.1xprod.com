<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un admin
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'System',
            'email' => 'admin@naturacorp.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '0612345678',
            'hire_date' => now(),
            'monthly_goal' => 50000,
        ]);

        // Créer des commerciaux
        $commercials = [
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'email' => 'jean.dupont@naturacorp.com',
                'phone' => '0612345679',
                'monthly_goal' => 30000,
            ],
            [
                'first_name' => 'Marie',
                'last_name' => 'Martin',
                'email' => 'marie.martin@naturacorp.com',
                'phone' => '0612345680',
                'monthly_goal' => 35000,
            ],
            [
                'first_name' => 'Pierre',
                'last_name' => 'Durand',
                'email' => 'pierre.durand@naturacorp.com',
                'phone' => '0612345681',
                'monthly_goal' => 40000,
            ],
        ];

        foreach ($commercials as $commercial) {
            User::create([
                'first_name' => $commercial['first_name'],
                'last_name' => $commercial['last_name'],
                'email' => $commercial['email'],
                'password' => Hash::make('password'),
                'role' => 'commercial',
                'phone' => $commercial['phone'],
                'hire_date' => now(),
                'monthly_goal' => $commercial['monthly_goal'],
            ]);
        }
    }
} 
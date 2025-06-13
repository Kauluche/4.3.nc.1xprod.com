<?php

namespace Database\Seeders;

use App\Models\Zone;
use App\Models\User;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Zone Nord',
                'description' => 'Région Nord de la France',
            ],
            [
                'name' => 'Zone Sud',
                'description' => 'Région Sud de la France',
            ],
            [
                'name' => 'Zone Est',
                'description' => 'Région Est de la France',
            ],
            [
                'name' => 'Zone Ouest',
                'description' => 'Région Ouest de la France',
            ],
        ];

        foreach ($zones as $zone) {
            Zone::create($zone);
        }
    }
}

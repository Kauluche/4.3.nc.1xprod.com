<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use App\Models\Pharmacy;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $commercials = User::where('role', 'commercial')->get();
        $pharmacies = Pharmacy::all();

        foreach ($commercials as $commercial) {
            // Créer des documents pour chaque commercial
            Document::create([
                'title' => 'Contrat de partenariat',
                'description' => 'Contrat de partenariat commercial',
                'type' => 'contract',
                'file_path' => 'documents/contracts/partnership.pdf',
                'commercial_id' => $commercial->id,
                'pharmacy_id' => $pharmacies->random()->id,
            ]);

            Document::create([
                'title' => 'Fiche produit',
                'description' => 'Fiche technique du produit',
                'type' => 'product',
                'file_path' => 'documents/products/technical.pdf',
                'commercial_id' => $commercial->id,
                'pharmacy_id' => $pharmacies->random()->id,
            ]);
        }
    }
} 
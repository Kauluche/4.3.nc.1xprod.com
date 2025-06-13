<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    public function index()
    {
        $values = [
            [
                'icon' => 'leaf',
                'title' => 'Naturel',
                'description' => 'Nous nous engageons à utiliser uniquement des ingrédients naturels de la plus haute qualité.'
            ],
            [
                'icon' => 'flask',
                'title' => 'Innovation',
                'description' => 'Notre équipe de recherche développe constamment de nouvelles solutions pour votre bien-être.'
            ],
            [
                'icon' => 'certificate',
                'title' => 'Qualité',
                'description' => 'Tous nos produits sont fabriqués selon les normes les plus strictes et sont certifiés.'
            ],
            [
                'icon' => 'heart',
                'title' => 'Engagement',
                'description' => 'Nous nous engageons pour votre santé et le respect de l\'environnement.'
            ]
        ];

        $certifications = [
            'ISO 9001:2015',
            'GMP (Good Manufacturing Practice)',
            'HACCP',
            'Agriculture Biologique'
        ];

        return view('enterprise', compact('values', 'certifications'));
    }
} 
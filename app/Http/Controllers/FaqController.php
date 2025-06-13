<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = [
            [
                'question' => 'Qu\'est-ce que MushBlue ?',
                'answer' => 'MushBlue est un complément alimentaire naturel à base d\'extrait de champignon, spécialement conçu pour renforcer votre système immunitaire et améliorer votre bien-être quotidien.'
            ],
            [
                'question' => 'Comment utiliser MushBlue ?',
                'answer' => 'Il est recommandé de prendre 1 à 2 gélules par jour avec un grand verre d\'eau, de préférence pendant les repas.'
            ],
            [
                'question' => 'MushBlue est-il naturel ?',
                'answer' => 'Oui, MushBlue est composé d\'ingrédients 100% naturels, principalement d\'extraits de champignons médicinaux sélectionnés pour leurs propriétés bénéfiques.'
            ],
            [
                'question' => 'Y a-t-il des effets secondaires ?',
                'answer' => 'MushBlue est un produit naturel et ne présente généralement pas d\'effets secondaires. Cependant, comme pour tout complément alimentaire, il est conseillé de consulter votre médecin en cas de doute.'
            ],
            [
                'question' => 'Où sont fabriqués vos produits ?',
                'answer' => 'Nos produits sont fabriqués en France dans des installations certifiées, respectant les normes les plus strictes de qualité et de sécurité.'
            ],
            [
                'question' => 'Comment conserver MushBlue ?',
                'answer' => 'MushBlue doit être conservé dans un endroit frais et sec, à l\'abri de la lumière. Veillez à bien refermer le flacon après chaque utilisation.'
            ]
        ];

        return view('faq', compact('faqs'));
    }
} 
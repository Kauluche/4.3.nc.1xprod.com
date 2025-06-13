<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $articles = [
            [
                'id' => 1,
                'title' => 'Les bienfaits des champignons médicinaux',
                'excerpt' => 'Découvrez comment les champignons médicinaux peuvent améliorer votre santé et votre bien-être au quotidien.',
                'content' => 'Les champignons médicinaux sont utilisés depuis des millénaires dans la médecine traditionnelle chinoise. Ils contiennent des composés bioactifs qui renforcent le système immunitaire, améliorent la digestion et réduisent le stress. Parmi les plus connus, on trouve le Reishi, le Shiitake et le Maitake.',
                'image' => 'images/vitrine/blog/champignons.jpg',
                'date' => '2024-03-15',
                'author' => 'Dr. Sophie Martin',
                'category' => 'Santé'
            ],
            [
                'id' => 2,
                'title' => 'L\'importance des compléments alimentaires naturels',
                'excerpt' => 'Pourquoi les compléments alimentaires naturels sont-ils essentiels dans notre mode de vie moderne ?',
                'content' => 'Dans notre société actuelle, il est souvent difficile de maintenir une alimentation équilibrée. Les compléments alimentaires naturels peuvent nous aider à combler les carences nutritionnelles et à maintenir un bon équilibre de notre organisme.',
                'image' => 'images/vitrine/blog/complements.jpg',
                'date' => '2024-03-10',
                'author' => 'Dr. Jean Dupont',
                'category' => 'Nutrition'
            ],
            [
                'id' => 3,
                'title' => 'Les secrets de la médecine traditionnelle',
                'excerpt' => 'Explorez les trésors de la médecine traditionnelle et leur application dans le monde moderne.',
                'content' => 'La médecine traditionnelle regorge de connaissances précieuses sur l\'utilisation des plantes et des champignons. Ces savoirs ancestraux, combinés aux avancées scientifiques modernes, ouvrent de nouvelles perspectives pour la santé et le bien-être.',
                'image' => 'images/vitrine/blog/medecine.jpg',
                'date' => '2024-03-05',
                'author' => 'Dr. Marie Laurent',
                'category' => 'Médecine'
            ]
        ];

        return view('blog.index', compact('articles'));
    }

    public function show($id)
    {
        $articles = [
            1 => [
                'id' => 1,
                'title' => 'Les bienfaits des champignons médicinaux',
                'content' => 'Les champignons médicinaux sont utilisés depuis des millénaires dans la médecine traditionnelle chinoise. Ils contiennent des composés bioactifs qui renforcent le système immunitaire, améliorent la digestion et réduisent le stress. Parmi les plus connus, on trouve le Reishi, le Shiitake et le Maitake.

Le Reishi, également appelé "champignon de l\'immortalité", est particulièrement réputé pour ses propriétés adaptogènes. Il aide l\'organisme à s\'adapter au stress et renforce les défenses naturelles.

Le Shiitake, quant à lui, est riche en lentinane, un composé qui stimule le système immunitaire. Il est également une excellente source de vitamines B et de minéraux essentiels.

Le Maitake, surnommé "le roi des champignons", est particulièrement efficace pour réguler la glycémie et soutenir le système immunitaire.

Pour profiter pleinement des bienfaits de ces champignons, il est recommandé de les consommer sous forme de compléments alimentaires de haute qualité, comme ceux proposés par NaturaCorp.',
                'image' => 'images/vitrine/blog/champignons.jpg',
                'date' => '2024-03-15',
                'author' => 'Dr. Sophie Martin',
                'category' => 'Santé'
            ],
            2 => [
                'id' => 2,
                'title' => 'L\'importance des compléments alimentaires naturels',
                'content' => 'Dans notre société actuelle, il est souvent difficile de maintenir une alimentation équilibrée. Les compléments alimentaires naturels peuvent nous aider à combler les carences nutritionnelles et à maintenir un bon équilibre de notre organisme.

Les raisons pour lesquelles les compléments alimentaires sont devenus essentiels sont multiples :

1. La diminution de la qualité nutritionnelle des aliments
2. Le stress et la pollution qui augmentent nos besoins en nutriments
3. Les modes de vie modernes qui ne permettent pas toujours une alimentation optimale

Chez NaturaCorp, nous sélectionnons rigoureusement les ingrédients de nos compléments alimentaires pour garantir leur qualité et leur efficacité. Nos produits sont fabriqués en France selon les normes les plus strictes.',
                'image' => 'images/vitrine/blog/complements.jpg',
                'date' => '2024-03-10',
                'author' => 'Dr. Jean Dupont',
                'category' => 'Nutrition'
            ],
            3 => [
                'id' => 3,
                'title' => 'Les secrets de la médecine traditionnelle',
                'content' => 'La médecine traditionnelle regorge de connaissances précieuses sur l\'utilisation des plantes et des champignons. Ces savoirs ancestraux, combinés aux avancées scientifiques modernes, ouvrent de nouvelles perspectives pour la santé et le bien-être.

Les principes fondamentaux de la médecine traditionnelle incluent :

- L\'approche holistique de la santé
- L\'utilisation de remèdes naturels
- La prévention plutôt que le traitement
- L\'équilibre entre le corps et l\'esprit

Chez NaturaCorp, nous nous inspirons de ces principes pour développer des produits qui respectent l\'équilibre naturel de l\'organisme tout en bénéficiant des avancées de la science moderne.',
                'image' => 'images/vitrine/blog/medecine.jpg',
                'date' => '2024-03-05',
                'author' => 'Dr. Marie Laurent',
                'category' => 'Médecine'
            ]
        ];

        if (!isset($articles[$id])) {
            abort(404);
        }

        return view('blog.show', ['article' => $articles[$id]]);
    }
} 
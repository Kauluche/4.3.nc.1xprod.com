<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil du site vitrine.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Instancier le BlogController pour récupérer les articles
        $blogController = new BlogController();
        $articles = $blogController->index()->getData()['articles'];
        
        // Limiter à 3 articles pour la page d'accueil
        $latestArticles = array_slice($articles, 0, 3);
        
        return view('home', compact('latestArticles'));
    }
} 
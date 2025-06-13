<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PharmacyApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Routes pour les pharmacies (accessibles uniquement aux commerciaux authentifiés)
Route::middleware(['auth:sanctum', 'role:commercial'])->group(function () {
    // Rechercher des pharmacies à proximité
    Route::post('/pharmacies/search', [PharmacyApiController::class, 'searchPharmacies']);
    
    // Ajouter une pharmacie comme prospect
    Route::post('/pharmacies/add-prospect', [PharmacyApiController::class, 'addPharmacyProspect']);
    
    // Autocomplétion des villes
    Route::get('/cities/suggest', [PharmacyApiController::class, 'suggestCities']);
});

// Route de test sans authentification pour déboguer l'API des villes
// À SUPPRIMER EN PRODUCTION
Route::get('/test/cities', [PharmacyApiController::class, 'testCitiesSuggestion']); 
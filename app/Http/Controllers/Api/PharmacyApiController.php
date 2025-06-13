<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pharmacy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PharmacyApiController extends Controller
{
    public function getCommercialPharmacies()
    {
        Log::info('Récupération des pharmacies pour le commercial', ['user_id' => Auth::id()]);
        
        $pharmacies = Pharmacy::where('commercial_id', Auth::id())
            ->select('id', 'name', 'address', 'city', 'postal_code', 'latitude', 'longitude', 'email', 'phone')
            ->get();
            
        Log::info('Pharmacies trouvées', ['count' => $pharmacies->count()]);
        
        return response()->json($pharmacies);
    }
} 
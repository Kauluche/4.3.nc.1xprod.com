<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\Zone;
use App\Models\Notification;
use App\Models\User;
use App\Services\Api\FinessApiService;
use App\Services\Api\GeoApiService;
use App\Services\GeocodingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class PharmacyController extends Controller
{
    protected $finessApiService;
    protected $geoApiService;
    protected $geocodingService;
    protected $searchService;

    public function __construct(
        FinessApiService $finessApiService, 
        GeoApiService $geoApiService, 
        GeocodingService $geocodingService,
        \App\Services\SearchService $searchService
    ) {
        $this->finessApiService = $finessApiService;
        $this->geoApiService = $geoApiService;
        $this->geocodingService = $geocodingService;
        $this->searchService = $searchService;
    }
    
    public function index(Request $request)
    {
        $query = Pharmacy::query();
        
        // Si l'utilisateur est un commercial, filtrer par sa zone
        if (auth()->user()->role === 'commercial') {
            $query->where('zone_id', auth()->user()->zone_id);
        }
        
        // Recherche par nom, ville ou code postal
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('postal_code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Charger les relations nécessaires
        $query->with(['zone', 'commercial']);
        
        // Pagination
        $pharmacies = $query->paginate(10)->withQueryString();
        
        return view('commercial.pharmacies.index', compact('pharmacies'));
    }

    public function create()
    {
        // Si l'utilisateur est un commercial, on ne montre que sa zone
        if (auth()->user()->role === 'commercial') {
            $zones = Zone::where('id', auth()->user()->zone_id)->get();
        } else {
            $zones = Zone::all();
        }
        return view('commercial.pharmacies.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        // Vérifier si la pharmacie existe déjà
        $existingPharmacy = Pharmacy::where('name', $validated['name'])
            ->where('postal_code', $validated['postal_code'])
            ->first();

        if ($existingPharmacy) {
            return back()->with('error', 'Cette pharmacie existe déjà dans la base de données.');
        }

        // Ajouter les informations manquantes
        $validated['status'] = 'prospect';
        $validated['zone_id'] = auth()->user()->zone_id;
        $validated['commercial_id'] = auth()->id();
        $validated['email'] = ''; // Email vide par défaut
        $validated['monthly_goal'] = 0; // Objectif mensuel à 0 par défaut

        // Créer la pharmacie
        $pharmacy = Pharmacy::create($validated);

        return redirect()->route('pharmacies.index')
            ->with('success', 'La pharmacie a été ajoutée en tant que prospect.');
    }

    public function edit(Pharmacy $pharmacy)
    {
        // Vérifier que le commercial ne peut modifier que les pharmacies de sa zone
        if (auth()->user()->role === 'commercial' && $pharmacy->zone_id != auth()->user()->zone_id) {
            abort(403, 'Vous ne pouvez modifier que les pharmacies de votre zone.');
        }

        // Si l'utilisateur est un commercial, on ne montre que sa zone
        if (auth()->user()->role === 'commercial') {
            $zones = Zone::where('id', auth()->user()->zone_id)->get();
        } else {
            $zones = Zone::all();
        }
        return view('commercial.pharmacies.edit', compact('pharmacy', 'zones'));
    }

    public function show(Pharmacy $pharmacy)
    {
        // Vérifier que le commercial ne peut voir que les pharmacies de sa zone
        if (auth()->user()->role === 'commercial' && $pharmacy->zone_id != auth()->user()->zone_id) {
            abort(403, 'Vous ne pouvez voir que les pharmacies de votre zone.');
        }

        return view('commercial.pharmacies.show', compact('pharmacy'));
    }

    public function update(Request $request, Pharmacy $pharmacy)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'status' => 'required|in:client,prospect',
            'zone_id' => 'required|exists:zones,id',
            'monthly_goal' => 'required|numeric|min:0',
        ]);

        // Vérifier si l'adresse a changé
        if ($pharmacy->address !== $validated['address'] || 
            $pharmacy->postal_code !== $validated['postal_code'] || 
            $pharmacy->city !== $validated['city']) {
            
            // Géocodage de la nouvelle adresse
            $geocodingResult = $this->geocodingService->geocodeAddress(
                $validated['address'],
                $validated['postal_code'],
                $validated['city']
            );

            if ($geocodingResult['success']) {
                $validated['latitude'] = $geocodingResult['latitude'];
                $validated['longitude'] = $geocodingResult['longitude'];
            } else {
                Log::warning('Impossible de géolocaliser la pharmacie mise à jour', [
                    'id' => $pharmacy->id,
                    'name' => $validated['name']
                ]);
            }
        }

        // Vérifier que le commercial ne peut modifier que les pharmacies de sa zone
        if (auth()->user()->role === 'commercial' && $validated['zone_id'] != auth()->user()->zone_id) {
            return back()->withErrors(['zone_id' => 'Vous ne pouvez modifier une pharmacie que dans votre zone.']);
        }

        $pharmacy->update($validated);

        return redirect()->route('pharmacies.index')->with('success', 'Pharmacie mise à jour avec succès.');
    }

    public function destroy(Pharmacy $pharmacy)
    {
        if (auth()->user()->role === 'commercial') {
            // Trouver l'admin
            $admin = User::where('role', 'admin')->first();
            if ($admin) {
                // Créer la notification pour l'admin
                $notificationData = [
                    'pharmacy_id' => $pharmacy->id,
                    'pharmacy_name' => $pharmacy->name,
                    'commercial_id' => auth()->id(),
                    'commercial_first_name' => auth()->user()->first_name,
                    'commercial_last_name' => auth()->user()->last_name,
                    'message' => 'Le commercial ' . auth()->user()->first_name . ' ' . auth()->user()->last_name . ' demande la suppression de la pharmacie ' . $pharmacy->name
                ];

                $notification = new Notification([
                    'id' => Str::uuid(),
                    'type' => 'pharmacy_deletion_request',
                    'data' => $notificationData,
                    'notifiable_id' => $admin->id,
                    'notifiable_type' => User::class
                ]);
                
                $notification->save();
            }

            return redirect()->route('pharmacies.index')
                ->with('success', 'Votre demande de suppression a été envoyée à l\'administrateur.');
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        $pharmacy->delete();
        return redirect()->route('pharmacies.index')->with('success', 'Pharmacie supprimée avec succès.');
    }

    /**
     * Affiche la page de recherche de pharmacies
     */
    public function search(Request $request)
    {
        \Log::info('PharmacyController@search called', [
            'request' => $request->all(),
            'user' => auth()->user(),
            'route' => $request->route(),
            'url' => $request->url()
        ]);

        // Si pas de département fourni, afficher simplement le formulaire
        if (!$request->has('department')) {
            return view('commercial.pharmacies.search');
        }

        try {
            $request->validate([
                'department' => 'required|string|size:2'
            ]);

            $pharmacies = $this->searchService->searchByDepartment($request->department);
            
            \Log::info('Search results', [
                'department' => $request->department,
                'count' => $pharmacies->count()
            ]);

            return view('commercial.pharmacies.search', [
                'pharmacies' => $pharmacies,
                'department' => $request->department
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in PharmacyController@search', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la recherche : ' . $e->getMessage());
        }
    }
    
    /**
     * Recherche des pharmacies par département
     */
    public function find(Request $request)
    {
        $request->validate([
            'department' => 'required|string|size:2,3',
        ]);
        
        $department = $request->input('department');
        $departments = [
            '01' => 'Ain',
            // ... liste complète des départements ...
        ];
        $departmentName = $departments[$department] ?? 'Inconnu';
        
        try {
            $response = Http::get('https://data.sante.gouv.fr/api/records/1.0/search/', [
                'dataset' => 'finess-etablissements-actifs',
                'q' => 'pharmacie',
                'refine.libelle_departement' => $departmentName,
                'rows' => 100,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $pharmacies = [];
                
                foreach ($data['records'] as $record) {
                    $fields = $record['fields'];
                    $pharmacies[] = [
                        'name' => $fields['rs'] ?? 'Pharmacie',
                        'address' => $fields['adresse'] ?? '',
                        'city' => $fields['commune'] ?? '',
                        'postal_code' => $fields['code_postal'] ?? '',
                        'phone' => $fields['telephone'] ?? '',
                    ];
                }
                
                return view('commercial.pharmacies.search', compact('pharmacies', 'departmentName', 'departments'));
            } else {
                return back()->with('error', 'Erreur lors de la recherche des pharmacies.');
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la recherche des pharmacies', [
                'error' => $e->getMessage(),
                'department' => $department
            ]);
            return back()->with('error', 'Une erreur est survenue lors de la recherche des pharmacies.');
        }
    }
    
    /**
     * Ajoute une pharmacie en tant que prospect
     */
    public function addProspect(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        // Vérifier si la pharmacie existe déjà
        $existingPharmacy = Pharmacy::where('name', $validated['name'])
            ->where('postal_code', $validated['postal_code'])
            ->first();

        if ($existingPharmacy) {
            return back()->with('error', 'Cette pharmacie existe déjà dans la base de données.');
        }

        // Géocodage de l'adresse
        $geocodingResult = $this->geocodingService->geocodeAddress(
            $validated['address'],
            $validated['postal_code'],
            $validated['city']
        );

        if ($geocodingResult['success']) {
            $validated['latitude'] = $geocodingResult['latitude'];
            $validated['longitude'] = $geocodingResult['longitude'];
        }

        // Ajouter les informations manquantes
        $validated['status'] = 'prospect';
        $validated['zone_id'] = auth()->user()->zone_id;
        $validated['commercial_id'] = auth()->id();
        $validated['monthly_goal'] = 0; // Objectif mensuel à 0 par défaut

        // Formater le numéro de téléphone
        if (isset($validated['phone'])) {
            $validated['phone'] = $this->formatPhoneNumber($validated['phone']);
        }

        // Créer la pharmacie
        $pharmacy = Pharmacy::create($validated);

        return redirect()->route('pharmacies.index')
            ->with('success', 'La pharmacie a été ajoutée en tant que prospect.');
    }

    protected function formatPhoneNumber($phone)
    {
        // Supprimer tous les caractères non numériques
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Si le numéro commence par 0, le remplacer par +33
        if (strpos($phone, '0') === 0) {
            $phone = '33' . substr($phone, 1);
        }
        
        // Formater le numéro avec des espaces
        return implode(' ', str_split($phone, 2));
    }
}

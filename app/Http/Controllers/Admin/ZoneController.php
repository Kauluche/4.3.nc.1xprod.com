<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;

class ZoneController extends Controller
{
    public function index()
    {
        \Log::info('Accessing ZoneController@index');
        $zones = Zone::with(['commercial' => function($query) {
            $query->where('role', 'commercial');
        }])->get();
        
        $commercialsWithoutZone = User::where('role', 'commercial')
            ->whereNull('zone_id')
            ->get();
            
        $zonesWithoutCommercial = Zone::whereNull('commercial_id')->get();

        $commercials = User::where('role', 'commercial')->get();
        
        \Log::info('Zones retrieved:', ['count' => $zones->count()]);
        return view('admin.zones.index', compact('zones', 'commercialsWithoutZone', 'zonesWithoutCommercial', 'commercials'));
    }

    public function create()
    {
        $commercials = User::where('role', 'commercial')->get();
        return view('admin.zones.create', compact('commercials'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'commercial_id' => 'nullable|exists:users,id',
        ]);

        // Si un commercial est assigné
        if ($validated['commercial_id']) {
            // On vérifie si le commercial est déjà assigné à une autre zone
            $existingZone = Zone::where('commercial_id', $validated['commercial_id'])->first();
            if ($existingZone) {
                return back()->withErrors(['commercial_id' => 'Ce commercial est déjà assigné à une autre zone.']);
            }

            // On vérifie que l'utilisateur est bien un commercial
            $commercial = User::find($validated['commercial_id']);
            if (!$commercial || $commercial->role !== 'commercial') {
                return back()->withErrors(['commercial_id' => 'L\'utilisateur sélectionné n\'est pas un commercial.']);
            }
            
            // On crée la zone
            $zone = Zone::create($validated);
            
            // On met à jour le commercial avec l'ID de la zone
            $commercial->zone_id = $zone->id;
            $commercial->save();
        } else {
            // On crée la zone sans commercial
            Zone::create($validated);
        }

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone créée avec succès.');
    }

    public function show(Zone $zone)
    {
        return view('admin.zones.show', compact('zone'));
    }

    public function edit(Zone $zone)
    {
        $commercials = User::where('role', 'commercial')->get();
        return view('admin.zones.edit', compact('zone', 'commercials'));
    }

    public function update(Request $request, Zone $zone)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'commercial_id' => 'nullable|exists:users,id',
        ]);

        // Si la zone avait déjà un commercial assigné
        if ($zone->commercial_id) {
            // On retire la zone de l'ancien commercial
            $oldCommercial = User::find($zone->commercial_id);
            if ($oldCommercial) {
                $oldCommercial->zone_id = null;
                $oldCommercial->save();
            }
        }

        // Si un nouveau commercial est assigné
        if ($validated['commercial_id']) {
            // On vérifie si le commercial est déjà assigné à une autre zone
            $existingZone = Zone::where('commercial_id', $validated['commercial_id'])
                               ->where('id', '!=', $zone->id)
                               ->first();
            
            if ($existingZone) {
                return back()->withErrors(['commercial_id' => 'Ce commercial est déjà assigné à une autre zone.']);
            }

            // On vérifie que l'utilisateur est bien un commercial
            $commercial = User::find($validated['commercial_id']);
            if (!$commercial || $commercial->role !== 'commercial') {
                return back()->withErrors(['commercial_id' => 'L\'utilisateur sélectionné n\'est pas un commercial.']);
            }

            // On met à jour la zone du nouveau commercial
            $commercial->zone_id = $zone->id;
            $commercial->save();
        }

        $zone->update($validated);

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone mise à jour avec succès.');
    }

    public function destroy(Zone $zone)
    {
        // On retire la zone de tous les commerciaux
        User::where('zone_id', $zone->id)->update(['zone_id' => null]);
        
        $zone->delete();

        return redirect()->route('admin.zones.index')
            ->with('success', 'Zone supprimée avec succès.');
    }

    public function swap(Request $request)
    {
        $request->validate([
            'commercial1_id' => 'required|exists:users,id',
            'commercial2_id' => 'required|exists:users,id|different:commercial1_id',
        ]);

        $commercial1 = User::findOrFail($request->commercial1_id);
        $commercial2 = User::findOrFail($request->commercial2_id);

        // Vérifier que les deux utilisateurs sont des commerciaux
        if ($commercial1->role !== 'commercial' || $commercial2->role !== 'commercial') {
            return back()->with('error', 'Les deux utilisateurs doivent être des commerciaux.');
        }

        // Vérifier que le deuxième commercial a bien une zone
        if (!$commercial2->zone_id) {
            return back()->with('error', 'Le deuxième commercial doit avoir une zone assignée.');
        }

        // Récupérer les zones actuelles
        $zone1 = null;
        if ($commercial1->zone_id) {
            $zone1 = Zone::find($commercial1->zone_id);
        }
        
        $zone2 = Zone::find($commercial2->zone_id);
        
        // Mettre à jour les zones pour les commerciaux
        if ($zone1) {
            // Commercial 1 avait une zone, on l'assigne au commercial 2
            $zone1->commercial_id = $commercial2->id;
            $zone1->save();
            
            // On met à jour le commercial 2
            $commercial2->zone_id = $zone1->id;
            $commercial2->save();
        } else {
            // Commercial 1 n'avait pas de zone, on retire la zone du commercial 2
            $commercial2->zone_id = null;
            $commercial2->save();
        }
        
        // On assigne la zone du commercial 2 au commercial 1
        $zone2->commercial_id = $commercial1->id;
        $zone2->save();
        
        $commercial1->zone_id = $zone2->id;
        $commercial1->save();

        return redirect()->route('admin.zones.index')
            ->with('success', 'Les commerciaux ont été échangés avec succès.');
    }
} 
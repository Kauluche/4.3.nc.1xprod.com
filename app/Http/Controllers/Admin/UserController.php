<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('zone');

        // Recherche par nom, prénom ou email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtre par rôle
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filtre par zone
        if ($request->filled('zone_id')) {
            $query->where('zone_id', $request->zone_id);
        }

        // Tri
        $sortField = $request->get('sort', 'first_name');
        $sortDirection = $request->get('direction', 'asc');
        
        if (in_array($sortField, ['first_name', 'email'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('first_name', 'asc');
        }

        $users = $query->get();
        $zones = Zone::all();

        return view('admin.users.index', compact('users', 'zones'));
    }

    public function create()
    {
        $zones = Zone::all();
        return view('admin.users.create', compact('zones'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,commercial',
            'zone_id' => 'nullable|exists:zones,id',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|in:M,F,O',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'hire_date' => 'nullable|date',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'two_factor_enabled' => 'boolean',
        ]);

        // Vérification préalable de la zone si l'utilisateur est un commercial
        if ($validated['role'] === 'commercial' && $validated['zone_id']) {
            $existingCommercial = User::where('zone_id', $validated['zone_id'])
                                    ->where('role', 'commercial')
                                    ->first();
            
            if ($existingCommercial) {
                return back()->withErrors(['zone_id' => 'Cette zone est déjà assignée à un autre commercial.'])
                            ->withInput();
            }
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'zone_id' => $validated['zone_id'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'city' => $validated['city'] ?? null,
            'postal_code' => $validated['postal_code'] ?? null,
            'hire_date' => $validated['hire_date'] ?? null,
            'department' => $validated['department'] ?? null,
            'position' => $validated['position'] ?? null,
            'two_factor_enabled' => $validated['two_factor_enabled'] ?? false,
        ]);

        // Mise à jour de la zone après la création de l'utilisateur
        if ($user->role === 'commercial' && $user->zone_id) {
            Zone::where('id', $user->zone_id)->update(['commercial_id' => $user->id]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    public function edit(User $user)
    {
        $zones = Zone::all();
        return view('admin.users.edit', compact('user', 'zones'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,commercial',
            'zone_id' => 'nullable|exists:zones,id',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Si l'utilisateur est un admin
        if ($validated['role'] === 'admin') {
            // Si l'utilisateur était un commercial, on retire sa zone
            if ($user->zone) {
                $user->zone->update(['commercial_id' => null]);
            }
            unset($validated['zone_id']);
        } 
        // Si l'utilisateur est un commercial
        else if ($validated['role'] === 'commercial') {
            // Si une zone est assignée
            if ($validated['zone_id']) {
                // On vérifie si la zone est déjà assignée à un autre commercial
                $existingCommercial = User::where('zone_id', $validated['zone_id'])
                                        ->where('role', 'commercial')
                                        ->where('id', '!=', $user->id)
                                        ->first();
                
                if ($existingCommercial) {
                    return back()->withErrors(['zone_id' => 'Cette zone est déjà assignée à un autre commercial.']);
                }

                // On met à jour la zone avec l'ID du commercial
                Zone::where('id', $validated['zone_id'])->update(['commercial_id' => $user->id]);
            } else {
                // Si on retire la zone, on met à jour l'ancienne zone
                if ($user->zone) {
                    $user->zone->update(['commercial_id' => null]);
                }
            }
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Utilisateur mis à jour avec succès.');
    }

    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        // Si l'utilisateur est un admin, on ne le supprime pas
        if ($user->isAdmin()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Impossible de supprimer un administrateur.');
        }

        // On supprime d'abord les relations
        if ($user->isCommercial()) {
            // On met à jour la zone associée
            if ($user->zone) {
                $user->zone->update(['commercial_id' => null]);
            }

            // On met à jour les pharmacies associées avec un commercial par défaut (admin)
            $adminUser = User::where('role', 'admin')->first();
            if ($adminUser) {
                $user->pharmacies()->update(['commercial_id' => $adminUser->id]);
            }

            // On supprime les commandes des pharmacies du commercial
            $user->pharmacies()->each(function ($pharmacy) {
                $pharmacy->orders()->delete();
            });
        }

        // On supprime les documents et les logs d'audit
        $user->documents()->delete();
        $user->auditLogs()->delete();

        // Puis on supprime l'utilisateur
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
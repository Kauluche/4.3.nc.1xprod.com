<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Pharmacy;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::whereIn('pharmacy_id', auth()->user()->pharmacies()->pluck('id'))
            ->with(['pharmacy', 'items'])
            ->latest();

        // Recherche par numéro de commande ou nom de pharmacie
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhereHas('pharmacy', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par date
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->paginate(10)->withQueryString();

        return view('commercial.orders.index', compact('orders'));
    }

    public function create()
    {
        $pharmacies = auth()->user()->pharmacies()->orderBy('name')->get();
        return view('commercial.orders.create', compact('pharmacies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.product_reference' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier que la pharmacie appartient au commercial
        if (!auth()->user()->pharmacies()->where('id', $validated['pharmacy_id'])->exists()) {
            return back()->withErrors(['pharmacy_id' => 'Vous ne pouvez créer une commande que pour vos pharmacies.']);
        }

        // Créer la commande
        $order = Order::create([
            'pharmacy_id' => $validated['pharmacy_id'],
            'commercial_id' => auth()->id(),
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Créer les items de la commande
        foreach ($validated['items'] as $item) {
            // Calculer le total pour cet élément
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $discountPercentage = $item['discount_percentage'] ?? 0;
            
            // Calcul du total avec réduction
            $total = $quantity * $unitPrice * (1 - ($discountPercentage / 100));

            $order->items()->create([
                'product_name' => $item['product_name'],
                'product_reference' => $item['product_reference'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_percentage' => $discountPercentage,
                'notes' => $item['notes'],
                'total' => $total, // Ajouter le total calculé
            ]);
        }

        // Calculer le total de la commande
        $order->calculateTotals();

        return redirect()->route('commercial.orders.index')->with('success', 'Commande créée avec succès.');
    }

    public function show(Order $order)
    {
        // Vérifier que la commande appartient à une pharmacie du commercial
        if (!auth()->user()->pharmacies()->where('id', $order->pharmacy_id)->exists()) {
            abort(403);
        }

        $order->load(['pharmacy', 'items']);
        return view('commercial.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        // Vérifier que la commande appartient à une pharmacie du commercial
        if (!auth()->user()->pharmacies()->where('id', $order->pharmacy_id)->exists()) {
            abort(403);
        }

        $pharmacies = auth()->user()->pharmacies()->orderBy('name')->get();
        $order->load('items');
        return view('commercial.orders.edit', compact('order', 'pharmacies'));
    }

    public function update(Request $request, Order $order)
    {
        // Vérifier que la commande appartient à une pharmacie du commercial
        if (!auth()->user()->pharmacies()->where('id', $order->pharmacy_id)->exists()) {
            abort(403);
        }

        $validated = $request->validate([
            'pharmacy_id' => 'required|exists:pharmacies,id',
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.product_reference' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_percentage' => 'nullable|numeric|min:0|max:100',
            'items.*.notes' => 'nullable|string|max:1000',
        ]);

        // Vérifier que la pharmacie appartient au commercial
        if (!auth()->user()->pharmacies()->where('id', $validated['pharmacy_id'])->exists()) {
            return back()->withErrors(['pharmacy_id' => 'Vous ne pouvez modifier une commande que pour vos pharmacies.']);
        }

        // Mettre à jour la commande
        $order->update([
            'pharmacy_id' => $validated['pharmacy_id'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // Supprimer les anciens items
        $order->items()->delete();

        // Créer les nouveaux items
        foreach ($validated['items'] as $item) {
            // Calculer le total pour cet élément
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $discountPercentage = $item['discount_percentage'] ?? 0;
            
            // Calcul du total avec réduction
            $total = $quantity * $unitPrice * (1 - ($discountPercentage / 100));
            
            $order->items()->create([
                'product_name' => $item['product_name'],
                'product_reference' => $item['product_reference'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_percentage' => $discountPercentage,
                'notes' => $item['notes'],
                'total' => $total, // Ajouter le total calculé
            ]);
        }

        // Calculer le total de la commande
        $order->calculateTotals();

        return redirect()->route('commercial.orders.index')->with('success', 'Commande mise à jour avec succès.');
    }

    public function destroy(Order $order)
    {
        // Vérifier que la commande appartient à une pharmacie du commercial
        if (!auth()->user()->pharmacies()->where('id', $order->pharmacy_id)->exists()) {
            abort(403);
        }
        
        // Supprimer la commande
        $order->items()->delete();
        $order->delete();
        
        return redirect()->route('commercial.orders.index')->with('success', 'Commande supprimée avec succès.');
    }
} 
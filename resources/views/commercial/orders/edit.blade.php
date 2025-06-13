@extends('layouts.app')

@section('title', 'Modifier la commande')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Modifier la commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
        <a href="{{ route('orders.show', $order) }}" class="btn-secondary">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour
        </a>
    </div>

    <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations de la commande -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations de la commande</h2>
                <div class="space-y-4">
                    <div>
                        <label for="pharmacy_id" class="block text-sm font-medium text-gray-700">Pharmacie</label>
                        <select name="pharmacy_id" id="pharmacy_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($pharmacies as $pharmacy)
                                <option value="{{ $pharmacy->id }}" {{ $order->pharmacy_id == $pharmacy->id ? 'selected' : '' }}>
                                    {{ $pharmacy->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Terminée</option>
                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $order->notes }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Résumé financier -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Résumé financier</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Montant HT</span>
                        <span class="text-sm font-medium text-gray-900" id="total-amount">{{ number_format($order->total, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Réduction</span>
                        <span class="text-sm font-medium text-gray-900" id="total-discount">{{ number_format($order->discount, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between">
                            <span class="text-base font-medium text-gray-900">Total TTC</span>
                            <span class="text-base font-medium text-gray-900" id="total-ttc">{{ number_format($order->total - $order->discount, 2, ',', ' ') }} €</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des produits -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Produits de la commande</h2>
                <button type="button" id="add-item" class="btn-primary">
                    <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Ajouter un produit
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Référence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Réduction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="items-container">
                        @foreach($order->items as $item)
                            <tr class="item-row" data-item-id="{{ $item->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" name="items[{{ $item->id }}][product_name]" value="{{ $item->product_name }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" name="items[{{ $item->id }}][product_reference]" value="{{ $item->product_reference }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="items[{{ $item->id }}][quantity]" value="{{ $item->quantity }}" min="1" class="quantity-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-20">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="items[{{ $item->id }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" class="price-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-32">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="number" name="items[{{ $item->id }}][discount_percentage]" value="{{ $item->discount_percentage }}" min="0" max="100" class="discount-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-20">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="item-total">{{ number_format($item->total, 2, ',', ' ') }} €</span>
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text" name="items[{{ $item->id }}][notes]" value="{{ $item->notes }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button type="button" class="remove-item text-red-600 hover:text-red-900">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    let itemCounter = {{ $order->items->count() }};

    // Fonction pour calculer le total d'un item
    function calculateItemTotal(row) {
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
        
        const total = quantity * price * (1 - discount / 100);
        row.querySelector('.item-total').textContent = total.toFixed(2).replace('.', ',') + ' €';
        updateTotals();
    }

    // Fonction pour mettre à jour les totaux globaux
    function updateTotals() {
        let totalAmount = 0;
        let totalDiscount = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const discount = parseFloat(row.querySelector('.discount-input').value) || 0;
            
            const itemTotal = quantity * price;
            const itemDiscount = itemTotal * (discount / 100);
            
            totalAmount += itemTotal;
            totalDiscount += itemDiscount;
        });

        document.getElementById('total-amount').textContent = totalAmount.toFixed(2).replace('.', ',') + ' €';
        document.getElementById('total-discount').textContent = totalDiscount.toFixed(2).replace('.', ',') + ' €';
        document.getElementById('total-ttc').textContent = (totalAmount - totalDiscount).toFixed(2).replace('.', ',') + ' €';
    }

    // Ajouter un nouvel item
    addButton.addEventListener('click', function() {
        itemCounter++;
        const newRow = document.createElement('tr');
        newRow.className = 'item-row';
        newRow.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="items[new_${itemCounter}][product_name]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="text" name="items[new_${itemCounter}][product_reference]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[new_${itemCounter}][quantity]" value="1" min="1" class="quantity-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-20">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[new_${itemCounter}][unit_price]" value="0" step="0.01" class="price-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-32">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" name="items[new_${itemCounter}][discount_percentage]" value="0" min="0" max="100" class="discount-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-20">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="item-total">0,00 €</span>
            </td>
            <td class="px-6 py-4">
                <input type="text" name="items[new_${itemCounter}][notes]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <button type="button" class="remove-item text-red-600 hover:text-red-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </button>
            </td>
        `;

        container.appendChild(newRow);
        setupRowEventListeners(newRow);
    });

    // Supprimer un item
    container.addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            e.target.closest('.item-row').remove();
            updateTotals();
        }
    });

    // Mettre en place les écouteurs d'événements pour une ligne
    function setupRowEventListeners(row) {
        row.querySelectorAll('.quantity-input, .price-input, .discount-input').forEach(input => {
            input.addEventListener('input', () => calculateItemTotal(row));
        });
    }

    // Mettre en place les écouteurs d'événements pour toutes les lignes existantes
    document.querySelectorAll('.item-row').forEach(setupRowEventListeners);
});
</script>
@endpush
@endsection 
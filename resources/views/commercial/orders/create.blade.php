@extends('layouts.app')

@section('title', 'Nouvelle commande')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Nouvelle commande</h1>
        <a href="{{ route('commercial.orders.index') }}" class="btn-secondary">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Retour
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('commercial.orders.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informations de la commande -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations de la commande</h2>
                <div class="space-y-4">
                    <div>
                        <label for="pharmacy_id" class="block text-sm font-medium text-gray-700">Pharmacie</label>
                        <select name="pharmacy_id" id="pharmacy_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">Sélectionnez une pharmacie</option>
                            @foreach($pharmacies as $pharmacy)
                                <option value="{{ $pharmacy->id }}" {{ old('pharmacy_id') == $pharmacy->id ? 'selected' : '' }}>
                                    {{ $pharmacy->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Statut</label>
                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Résumé financier -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Résumé financier</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Montant HT</span>
                        <span class="text-sm font-medium text-gray-900" id="total-amount">15,00 € × <span id="quantity-display">1</span> = <span id="subtotal-display">15,00 €</span></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-500">Réduction</span>
                        <span class="text-sm font-medium text-gray-900" id="total-discount">0,00 €</span>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between">
                            <span class="text-base font-medium text-gray-900">Total TTC</span>
                            <span class="text-base font-medium text-gray-900" id="total-ttc">15,00 €</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produit Mush Blue -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Produit de la commande</h2>
            </div>

            <div class="p-6">
                <div class="mb-6 bg-blue-50 p-4 rounded-lg">
                    <div class="font-semibold text-blue-800 text-lg mb-2">Mush Blue</div>
                    <div class="text-sm text-gray-150 mb-2">
                        <strong>Référence:</strong> MB-001
                    </div>
                    <div class="text-sm text-gray-150 mb-4">
                        <strong>Prix unitaire:</strong> 15,00 € HT
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantité</label>
                            <input type="number" id="quantity" name="items[1][quantity]" value="1" min="1" class="quantity-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full" required>
                            
                            <!-- Champs cachés pour le produit -->
                            <input type="hidden" name="items[1][product_name]" value="Mush Blue">
                            <input type="hidden" name="items[1][product_reference]" value="MB-001">
                            <input type="hidden" name="items[1][unit_price]" value="15">
                        </div>
                        
                        <div>
                            <label for="discount" class="block text-sm font-medium text-gray-700 mb-1">Réduction (%)</label>
                            <input type="number" id="discount" name="items[1][discount_percentage]" value="0" min="0" max="100" class="discount-input rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full" required>
                        </div>
                        
                        <div>
                            <label for="item_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                            <input type="text" id="item_notes" name="items[1][notes]" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Créer la commande
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const discountInput = document.getElementById('discount');
    const quantityDisplay = document.getElementById('quantity-display');
    const subtotalDisplay = document.getElementById('subtotal-display');
    const totalDiscountDisplay = document.getElementById('total-discount');
    const totalTtcDisplay = document.getElementById('total-ttc');
    
    // Prix unitaire fixe
    const unitPrice = 15;
    
    // Fonction pour calculer le total
    function updateTotals() {
        const quantity = parseInt(quantityInput.value) || 0;
        const discount = parseFloat(discountInput.value) || 0;
        
        // Calculer le sous-total
        const subtotal = quantity * unitPrice;
        
        // Calculer la réduction
        const discountAmount = subtotal * (discount / 100);
        
        // Calculer le total
        const total = subtotal - discountAmount;
        
        // Mettre à jour les affichages
        quantityDisplay.textContent = quantity;
        subtotalDisplay.textContent = subtotal.toFixed(2).replace('.', ',') + ' €';
        totalDiscountDisplay.textContent = discountAmount.toFixed(2).replace('.', ',') + ' €';
        totalTtcDisplay.textContent = total.toFixed(2).replace('.', ',') + ' €';
    }
    
    // Écouter les changements de quantité et de réduction
    quantityInput.addEventListener('input', updateTotals);
    discountInput.addEventListener('input', updateTotals);
    
    // Initialiser l'affichage
    updateTotals();
});
</script>
@endpush
@endsection 
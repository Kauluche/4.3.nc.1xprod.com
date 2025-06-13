@extends('layouts.app')

@section('title', 'Détails de la commande')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Commande #{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</h1>
        <div class="flex space-x-4">
            <a href="{{ route('commercial.orders.edit', $order) }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Modifier
            </a>
            <a href="{{ route('commercial.orders.index') }}" class="btn-secondary">
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Informations de la commande -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Informations de la commande</h2>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-500">Pharmacie</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->pharmacy->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Statut</label>
                    <p class="mt-1 text-sm text-gray-900">
                        @switch($order->status)
                            @case('pending')
                                <span class="px-2 py-1 text-xs font-semibold text-yellow-800 bg-yellow-100 rounded-full">En attente</span>
                                @break
                            @case('completed')
                                <span class="px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">Terminée</span>
                                @break
                            @case('cancelled')
                                <span class="px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">Annulée</span>
                                @break
                        @endswitch
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Date de création</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-500">Notes</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $order->notes ?: 'Aucune note' }}</p>
                </div>
            </div>
        </div>

        <!-- Résumé financier -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Résumé financier</h2>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Montant HT</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($order->total, 2, ',', ' ') }} €</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-500">Réduction</span>
                    <span class="text-sm font-medium text-gray-900">{{ number_format($order->discount, 2, ',', ' ') }} €</span>
                </div>
                <div class="border-t pt-4">
                    <div class="flex justify-between">
                        <span class="text-base font-medium text-gray-900">Total TTC</span>
                        <span class="text-base font-medium text-gray-900">{{ number_format($order->total - $order->discount, 2, ',', ' ') }} €</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des produits -->
    <div class="mt-6 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-800">Produits de la commande</h2>
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
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $item->product_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->product_reference }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($item->unit_price, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->discount_percentage }}%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($item->total, 2, ',', ' ') }} €
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $item->notes ?: '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Documents associés -->
<div class="mt-6 bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Documents associés</h2>
        <div class="flex space-x-4">
            <a href="{{ route('crm.documents.generate-invoice', $order) }}" 
               class="btn btn-primary" 
               style="background-color: #4F46E5; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; display: inline-flex; align-items: center; text-decoration: none;">
                <i class="fas fa-file-invoice mr-2"></i> Générer la facture
            </a>
            <a href="{{ route('crm.documents.generate-delivery-note', $order) }}" 
               class="btn btn-primary" 
               style="background-color: #4F46E5; color: white; padding: 0.5rem 1rem; border-radius: 0.375rem; display: inline-flex; align-items: center; text-decoration: none;">
                <i class="fas fa-truck mr-2"></i> Générer le bon de livraison
            </a>
        </div>
    </div>
</div>
@endsection 
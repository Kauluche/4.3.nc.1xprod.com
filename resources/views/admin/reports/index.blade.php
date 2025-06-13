@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold">Rapports</h1>
                    </div>

                    <!-- Statistiques générales -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900">Total Commandes</h3>
                            <p class="text-3xl font-bold text-blue-600">{{ $stats['total_orders'] }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900">Total Pharmacies</h3>
                            <p class="text-3xl font-bold text-green-600">{{ $stats['total_pharmacies'] }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900">Total Commerciaux</h3>
                            <p class="text-3xl font-bold text-purple-600">{{ $stats['total_commercials'] }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900">Total Zones</h3>
                            <p class="text-3xl font-bold text-orange-600">{{ $stats['total_zones'] }}</p>
                        </div>
                    </div>

                    <!-- Pharmacies par zone -->
                    <div class="mb-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Pharmacies par zone</h2>
                            <a href="{{ route('admin.export.pharmacies-by-zone') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Exporter toutes les zones (CSV)
                            </a>
                        </div>
                        
                        <!-- Statistiques des zones -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Zone qui rapporte le plus -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Zone qui rapporte le plus</h3>
                                @if($stats['top_zones']['revenue'])
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['top_zones']['revenue']['name'] }}</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $stats['top_zones']['revenue']['formatted_value'] }}</p>
                                @else
                                    <p class="text-lg font-bold text-gray-900">Aucune donnée</p>
                                @endif
                            </div>
                            
                            <!-- Zone qui commande le plus -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Zone qui commande le plus</h3>
                                @if($stats['top_zones']['orders'])
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['top_zones']['orders']['name'] }}</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $stats['top_zones']['orders']['formatted_value'] }} commandes</p>
                                @else
                                    <p class="text-lg font-bold text-gray-900">Aucune donnée</p>
                                @endif
                            </div>
                            
                            <!-- Zone avec le plus de pharmacies -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Zone avec le plus de pharmacies</h3>
                                @if($stats['top_zones']['pharmacies'])
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['top_zones']['pharmacies']['name'] }}</p>
                                    <p class="text-2xl font-bold text-orange-600">{{ $stats['top_zones']['pharmacies']['formatted_value'] }} pharmacies</p>
                                @else
                                    <p class="text-lg font-bold text-gray-900">Aucune donnée</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zone</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de pharmacies</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Export</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stats['pharmacies_by_zone'] as $zone)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $zone->name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $zone->pharmacies_count }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.export.pharmacies-by-zone.show', $zone->id) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                    <svg class="inline-block h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                                    </svg>
                                                    CSV
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Performance des commerciaux -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-xl font-semibold text-gray-900">Performance des commerciaux</h2>
                            <a href="{{ route('admin.export.commercials-performance') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Exporter tous les commerciaux (CSV)
                            </a>
                        </div>
                        
                        <!-- Statistiques des commerciaux -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <!-- Commercial qui rapporte le plus -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Commercial qui rapporte le plus</h3>
                                @if($stats['top_commercials']['revenue'])
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['top_commercials']['revenue']['name'] }}</p>
                                    <p class="text-2xl font-bold text-green-600">{{ $stats['top_commercials']['revenue']['formatted_value'] }}</p>
                                @else
                                    <p class="text-lg font-bold text-gray-900">Aucune donnée</p>
                                @endif
                            </div>
                            
                            <!-- Commercial avec le plus de commandes -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Commercial avec le plus de commandes</h3>
                                @if($stats['top_commercials']['orders'])
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['top_commercials']['orders']['name'] }}</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $stats['top_commercials']['orders']['formatted_value'] }} commandes</p>
                                @else
                                    <p class="text-lg font-bold text-gray-900">Aucune donnée</p>
                                @endif
                            </div>
                            
                            <!-- Commercial avec le plus de clients -->
                            <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-500 mb-1">Commercial avec le plus de clients</h3>
                                @if($stats['top_commercials']['clients'])
                                    <p class="text-lg font-bold text-gray-900">{{ $stats['top_commercials']['clients']['name'] }}</p>
                                    <p class="text-2xl font-bold text-orange-600">{{ $stats['top_commercials']['clients']['formatted_value'] }} clients</p>
                                @else
                                    <p class="text-lg font-bold text-gray-900">Aucune donnée</p>
                                @endif
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commercial</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre de pharmacies</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Export</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($stats['commercial_performance'] as $commercial)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $commercial->first_name }} {{ $commercial->last_name }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $commercial->pharmacies_count }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('admin.export.commercials-performance.show', $commercial->id) }}" class="inline-flex items-center px-3 py-1 border border-gray-300 text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                    <svg class="inline-block h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                                    </svg>
                                                    CSV
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des boutons d'export
        const exportButtons = document.querySelectorAll('.export-button');
        exportButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Ajouter une classe pour indiquer que le bouton est en cours de chargement
                button.classList.add('loading');
                // Simuler un délai de chargement
                setTimeout(function() {
                    // Supprimer la classe de chargement
                    button.classList.remove('loading');
                }, 2000);
            });
        });
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Liste des zones')

@section('content')
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="px-4 py-6 sm:px-0">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Liste des zones</h1>
                <a href="{{ route('admin.zones.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Ajouter une zone
                </a>
            </div>

            @if (session('success'))
                <div class="bg-green-500 text-white p-4 rounded-lg mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Résumé des assignations -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Zones sans commercial</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $zonesWithoutCommercial->count() }}</p>
                    @if($zonesWithoutCommercial->count() > 0)
                        <ul class="mt-2 text-sm text-gray-600">
                            @foreach($zonesWithoutCommercial as $zone)
                                <li>{{ $zone->name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Commerciaux sans zone</h3>
                    <p class="text-3xl font-bold text-yellow-600">{{ $commercialsWithoutZone->count() }}</p>
                    @if($commercialsWithoutZone->count() > 0)
                        <ul class="mt-2 text-sm text-gray-600">
                            @foreach($commercialsWithoutZone as $commercial)
                                <li>{{ $commercial->first_name }} {{ $commercial->last_name }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Total des zones</h3>
                    <p class="text-3xl font-bold text-blue-600">{{ $zones->count() }}</p>
                </div>
            </div>

            @if($zones->isEmpty())
                <p class="text-gray-700">Aucune zone pour le moment.</p>
            @else
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commercial</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($zones as $zone)
                                <tr class="{{ $zone->commercial ? 'bg-white' : 'bg-red-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $zone->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ $zone->description }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($zone->commercial)
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-600 font-semibold">
                                                            {{ substr($zone->commercial->first_name, 0, 1) }}{{ substr($zone->commercial->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $zone->commercial->first_name }} {{ $zone->commercial->last_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">{{ $zone->commercial->email }}</div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Non assigné
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <button onclick="openSwapModal({{ $zone->id }}, {{ $zone->commercial ? $zone->commercial->id : 'null' }}, '{{ $zone->commercial ? $zone->commercial->first_name . ' ' . $zone->commercial->last_name : 'Sans commercial' }}', '{{ $zone->name }}')" class="text-indigo-600 hover:text-indigo-900" title="Échanger">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                            </button>
                                            <a href="{{ route('admin.zones.edit', $zone) }}" class="text-blue-600 hover:text-blue-900" title="Modifier">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.zones.destroy', $zone) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette zone ?')">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal d'échange de zones -->
    <div id="swapModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Échanger les commerciaux</h3>
                <form id="swapForm" method="POST" action="{{ route('admin.zones.swap') }}">
                    @csrf
                    <input type="hidden" name="commercial1_id" id="commercial1_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Premier commercial</label>
                        <div id="commercial1_info" class="mt-2 p-3 bg-gray-50 rounded-md"></div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Deuxième commercial</label>
                        <select name="commercial2_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Sélectionner un commercial</option>
                            @foreach($commercials as $commercial)
                                @if($commercial->zone_id)
                                <option value="{{ $commercial->id }}" data-zone="{{ $commercial->zone_id }}">
                                    {{ $commercial->first_name }} {{ $commercial->last_name }}
                                    @if($commercial->zone)
                                        ({{ $commercial->zone->name }})
                                    @endif
                                </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeSwapModal()" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                            Annuler
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                            Échanger
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openSwapModal(zoneId, commercialId, commercialName, zoneName) {
            document.getElementById('commercial1_id').value = commercialId;
            document.getElementById('commercial1_info').innerHTML = `
                <div class="font-medium">${commercialName}</div>
                <div class="text-sm text-gray-500">${zoneName}</div>
            `;
            document.getElementById('swapModal').classList.remove('hidden');
        }

        function closeSwapModal() {
            document.getElementById('swapModal').classList.add('hidden');
        }

        // Fermer le modal si on clique en dehors
        window.onclick = function(event) {
            const modal = document.getElementById('swapModal');
            if (event.target == modal) {
                closeSwapModal();
            }
        }
    </script>
    @endpush
@endsection 
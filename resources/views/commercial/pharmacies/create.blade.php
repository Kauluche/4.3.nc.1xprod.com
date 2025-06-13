@extends('layouts.app')

@section('title', 'Nouvelle pharmacie')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Nouvelle pharmacie</h1>
            <a href="{{ route('pharmacies.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('pharmacies.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de base -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-medium text-gray-900">Informations de base</h2>
                        
                        <div>
                            <label for="name" class="form-label">Nom de la pharmacie</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-input" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}" class="form-input" required>
                            <div id="address-suggestions" class="hidden absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg"></div>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}" class="form-input" required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="form-label">Code postal</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}" class="form-input" required>
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Informations de contact -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-medium text-gray-900">Informations de contact</h2>
                        
                        <div>
                            <label for="phone" class="form-label">Téléphone</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="form-input" required>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-input" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="form-label">Statut</label>
                            <select name="status" id="status" class="form-input" required>
                                <option value="">Sélectionnez un statut</option>
                                <option value="client" {{ old('status') == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="prospect" {{ old('status') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="zone_id" class="form-label">Zone</label>
                            <select name="zone_id" id="zone_id" class="form-input" required>
                                <option value="">Sélectionnez une zone</option>
                                @foreach($zones as $zone)
                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                        {{ $zone->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('zone_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="monthly_goal" class="form-label">Objectif mensuel (€)</label>
                            <input type="number" name="monthly_goal" id="monthly_goal" value="{{ old('monthly_goal') }}" class="form-input" step="0.01" min="0" required>
                            @error('monthly_goal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    #address-suggestions {
        max-height: 200px;
        overflow-y: auto;
    }
    .suggestion-item {
        padding: 8px 12px;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .suggestion-item:hover {
        background-color: #f3f4f6;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addressInput = document.getElementById('address');
    const cityInput = document.getElementById('city');
    const postalCodeInput = document.getElementById('postal_code');
    const suggestionsDiv = document.getElementById('address-suggestions');
    let timeoutId;

    addressInput.addEventListener('input', function(e) {
        clearTimeout(timeoutId);
        const query = e.target.value;

        if (query.length < 3) {
            suggestionsDiv.classList.add('hidden');
            return;
        }

        timeoutId = setTimeout(() => {
            fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`)
                .then(response => response.json())
                .then(data => {
                    suggestionsDiv.innerHTML = '';
                    if (data.features && data.features.length > 0) {
                        data.features.forEach(feature => {
                            const div = document.createElement('div');
                            div.className = 'suggestion-item';
                            div.textContent = feature.properties.label;
                            div.addEventListener('click', () => {
                                addressInput.value = feature.properties.name;
                                cityInput.value = feature.properties.city;
                                postalCodeInput.value = feature.properties.postcode;
                                suggestionsDiv.classList.add('hidden');
                            });
                            suggestionsDiv.appendChild(div);
                        });
                        suggestionsDiv.classList.remove('hidden');
                    } else {
                        suggestionsDiv.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche d\'adresse:', error);
                    suggestionsDiv.classList.add('hidden');
                });
        }, 300);
    });

    // Fermer les suggestions quand on clique en dehors
    document.addEventListener('click', function(e) {
        if (!addressInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.classList.add('hidden');
        }
    });
});
</script>
@endpush
@endsection
@extends('layouts.app')

@section('title', 'Modifier la pharmacie')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Modifier la pharmacie</h1>
            <a href="{{ route('pharmacies.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Retour
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <form action="{{ route('pharmacies.update', $pharmacy) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Informations de base -->
                    <div class="space-y-4">
                        <h2 class="text-lg font-medium text-gray-900">Informations de base</h2>
                        
                        <div>
                            <label for="name" class="form-label">Nom de la pharmacie</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $pharmacy->name) }}" class="form-input" required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="form-label">Adresse</label>
                            <input type="text" name="address" id="address" value="{{ old('address', $pharmacy->address) }}" class="form-input" required>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="city" class="form-label">Ville</label>
                            <input type="text" name="city" id="city" value="{{ old('city', $pharmacy->city) }}" class="form-input" required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postal_code" class="form-label">Code postal</label>
                            <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $pharmacy->postal_code) }}" class="form-input" required>
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
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $pharmacy->phone) }}" class="form-input" required>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $pharmacy->email) }}" class="form-input" required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="form-label">Statut</label>
                            <select name="status" id="status" class="form-input" required>
                                <option value="">Sélectionnez un statut</option>
                                <option value="client" {{ old('status', $pharmacy->status) == 'client' ? 'selected' : '' }}>Client</option>
                                <option value="prospect" {{ old('status', $pharmacy->status) == 'prospect' ? 'selected' : '' }}>Prospect</option>
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
                                    <option value="{{ $zone->id }}" {{ old('zone_id', $pharmacy->zone_id) == $zone->id ? 'selected' : '' }}>
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
                            <input type="number" name="monthly_goal" id="monthly_goal" value="{{ old('monthly_goal', $pharmacy->monthly_goal) }}" class="form-input" step="0.01" min="0" required>
                            @error('monthly_goal')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addressInput = document.getElementById('address');
    const cityInput = document.getElementById('city');
    const postalCodeInput = document.getElementById('postal_code');

    let timeout = null;
    let lastSearch = '';

    addressInput.addEventListener('input', function(e) {
        clearTimeout(timeout);
        
        const search = e.target.value;
        if (search.length < 3 || search === lastSearch) return;
        
        lastSearch = search;
        
        timeout = setTimeout(() => {
            fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(search)}&limit=5`)
                .then(response => response.json())
                .then(data => {
                    const suggestions = data.features.map(feature => ({
                        address: feature.properties.name,
                        city: feature.properties.city,
                        postalCode: feature.properties.postcode,
                        label: feature.properties.label
                    }));

                    // Créer la liste de suggestions
                    let suggestionsList = document.getElementById('address-suggestions');
                    if (!suggestionsList) {
                        suggestionsList = document.createElement('ul');
                        suggestionsList.id = 'address-suggestions';
                        suggestionsList.className = 'absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg mt-1';
                        addressInput.parentNode.appendChild(suggestionsList);
                    }

                    // Vider et remplir la liste
                    suggestionsList.innerHTML = '';
                    suggestions.forEach(suggestion => {
                        const li = document.createElement('li');
                        li.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                        li.textContent = suggestion.label;
                        
                        li.addEventListener('click', () => {
                            addressInput.value = suggestion.address;
                            cityInput.value = suggestion.city;
                            postalCodeInput.value = suggestion.postalCode;
                            suggestionsList.innerHTML = '';
                        });
                        
                        suggestionsList.appendChild(li);
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la recherche d\'adresse:', error);
                });
        }, 300);
    });

    // Fermer la liste des suggestions quand on clique ailleurs
    document.addEventListener('click', function(e) {
        const suggestionsList = document.getElementById('address-suggestions');
        if (suggestionsList && !addressInput.contains(e.target) && !suggestionsList.contains(e.target)) {
            suggestionsList.innerHTML = '';
        }
    });
});
</script>
@endpush
@endsection
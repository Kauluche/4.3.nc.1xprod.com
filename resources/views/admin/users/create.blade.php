@extends('layouts.app')

<!-- Vue: create.blade.php -->

@section('title', 'Créer un utilisateur')

@section('content')


    <div class="card">
        <div class="px-6 py-8 sm:p-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Créer un utilisateur</h1>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
                </a>
            </div>

            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf
                <div class="space-y-8">
                    <!-- Informations personnelles -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations personnelles</h2>
                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="first_name" class="form-label">Prénom</label>
                                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                                    class="form-input">
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="last_name" class="form-label">Nom</label>
                                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                                    class="form-input">
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="birth_date" class="form-label">Date de naissance</label>
                                <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}"
                                    class="form-input">
                                @error('birth_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="gender" class="form-label">Genre</label>
                                <select name="gender" id="gender" class="form-input">
                                    <option value="">Sélectionner un genre</option>
                                    <option value="M" {{ old('gender') == 'M' ? 'selected' : '' }}>Masculin</option>
                                    <option value="F" {{ old('gender') == 'F' ? 'selected' : '' }}>Féminin</option>
                                    <option value="O" {{ old('gender') == 'O' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Coordonnées -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Coordonnées</h2>
                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="email" class="form-label">Email professionnel</label>
                                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                    class="form-input">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                    class="form-input" placeholder="+33 6 12 34 56 78">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2 sm:col-span-2">
                                <label for="address" class="form-label">Adresse</label>
                                <textarea name="address" id="address" rows="2" class="form-input">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" name="city" id="city" value="{{ old('city') }}"
                                    class="form-input">
                                @error('city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="postal_code" class="form-label">Code postal</label>
                                <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                    class="form-input">
                                @error('postal_code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Informations professionnelles -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informations professionnelles</h2>
                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="role" class="form-label">Rôle</label>
                                <select name="role" id="role" required class="form-input">
                                    <option value="">Sélectionner un rôle</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                                    <option value="commercial" {{ old('role') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                </select>
                                @error('role')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="hire_date" class="form-label">Date d'embauche</label>
                                <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date') }}"
                                    class="form-input">
                                @error('hire_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="zone_field" style="display: none;" class="space-y-2">
                                <label for="zone_id" class="form-label">Zone</label>
                                <select name="zone_id" id="zone_id" class="form-input">
                                    <option value="">Sélectionner une zone</option>
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

                            <div class="space-y-2">
                                <label for="department" class="form-label">Département</label>
                                <input type="text" name="department" id="department" value="{{ old('department') }}"
                                    class="form-input">
                                @error('department')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="position" class="form-label">Poste</label>
                                <input type="text" name="position" id="position" value="{{ old('position') }}"
                                    class="form-input">
                                @error('position')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Sécurité -->
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Sécurité</h2>
                        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                            <div class="space-y-2">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" name="password" id="password" required
                                    class="form-input" autocomplete="new-password">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" required
                                    class="form-input" autocomplete="new-password">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="two_factor_enabled" class="form-label">Authentification à deux facteurs</label>
                                <div class="flex items-center">
                                    <input type="checkbox" name="two_factor_enabled" id="two_factor_enabled" value="1"
                                        {{ old('two_factor_enabled') ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <label for="two_factor_enabled" class="ml-2 block text-sm text-gray-900">Activer</label>
                                </div>
                                @error('two_factor_enabled')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn-primary">
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    console.log('=== Scripts de create.blade.php ===');
    console.log('Script de create.blade.php chargé');
    
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM chargé dans create.blade.php');
        
        // Vérifier les éléments
        const card = document.querySelector('.card');
        console.log('Card trouvée:', !!card);
        
        const inputs = document.querySelectorAll('.form-input');
        console.log('Inputs trouvés:', inputs.length);
        
        const buttons = document.querySelectorAll('.btn-primary, .btn-secondary');
        console.log('Boutons trouvés:', buttons.length);
        
        const labels = document.querySelectorAll('.form-label');
        console.log('Labels trouvés:', labels.length);
        
        const roleSelect = document.getElementById('role');
        const zoneField = document.getElementById('zone_field');
        
        function toggleZoneField() {
            if (roleSelect.value === 'commercial') {
                zoneField.style.display = 'block';
            } else {
                zoneField.style.display = 'none';
            }
        }

        roleSelect.addEventListener('change', toggleZoneField);
        toggleZoneField(); // Initial state
    });
</script>
@endpush 
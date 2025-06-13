@extends('layouts.app')

@section('title', 'Recherche de pharmacies')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Recherche de pharmacies</h1>
        <a href="{{ route('pharmacies.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Retour à la liste
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form action="{{ route('pharmacies.search') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="col-span-2">
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Rechercher des pharmacies par département
                    </label>
                    <div class="mt-1 flex rounded-md shadow-sm">
                        <div class="relative flex items-stretch flex-grow">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   class="form-input pl-10 block w-full rounded-md border-gray-300 @error('department') border-red-300 @enderror" 
                                   id="department" 
                                   name="department" 
                                   value="{{ old('department', request('department')) }}"
                                   placeholder="Ex: 75, 77, 78..."
                                   required>
                        </div>
                        <button type="submit" class="ml-3 btn-primary">
                            Rechercher
                        </button>
                    </div>
                    @error('department')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if(isset($pharmacies) && $pharmacies->count() > 0)
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    {{ $pharmacies->count() }} pharmacie(s) trouvée(s)
                    @if(request('department'))
                        dans le département {{ request('department') }}
                    @endif
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adresse</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ville</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code postal</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pharmacies as $pharmacy)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $pharmacy['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pharmacy['address'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pharmacy['city'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pharmacy['postal_code'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <a href="tel:{{ $pharmacy['phone'] }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $pharmacy['phone'] }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <form action="{{ route('pharmacies.store') }}" method="POST" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $pharmacy['name'] }}">
                                        <input type="hidden" name="address" value="{{ $pharmacy['address'] }}">
                                        <input type="hidden" name="city" value="{{ $pharmacy['city'] }}">
                                        <input type="hidden" name="postal_code" value="{{ $pharmacy['postal_code'] }}">
                                        <input type="hidden" name="phone" value="{{ $pharmacy['phone'] }}">
                                        <input type="hidden" name="latitude" value="{{ $pharmacy['latitude'] }}">
                                        <input type="hidden" name="longitude" value="{{ $pharmacy['longitude'] }}">
                                        <input type="hidden" name="email" value="{{ $pharmacy['email'] ?? '' }}">
                                        <button type="submit" style="width: 30px; height: 30px; border-radius: 50%; background-color: #4f46e5; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 14px;" title="Ajouter comme prospect">
                                            +
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @elseif(request()->has('department'))
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm">
                        Aucune pharmacie trouvée dans le département {{ request('department') }}.
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .btn-sm {
        @apply px-3 py-1 text-sm rounded-md;
    }
    .btn-success {
        @apply bg-green-600 text-white hover:bg-green-700 focus:ring-green-500;
    }
    .btn-primary {
        @apply inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
    }
    .btn-secondary {
        @apply inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500;
    }
    .btn-plus {
        @apply inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-600 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out;
    }
    .btn-plus:hover {
        @apply transform scale-110;
    }
    .btn-plus:active {
        @apply transform scale-95;
    }
    .btn-plus i {
        @apply text-sm;
    }
</style>
@endpush
@endsection 
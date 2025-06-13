@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('Informations du profil') }}
                </h2>
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('Mot de passe') }}
                </h2>
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    {{ __('Supprimer le compte') }}
                </h2>
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection

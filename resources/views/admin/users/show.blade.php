@extends('layouts.app')

@section('title', 'Détails de l\'utilisateur')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Détails de l'utilisateur</h2>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn-primary">
                        <i class="fas fa-edit mr-2"></i>Modifier
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn-secondary">
                        <i class="fas fa-arrow-left mr-2"></i>Retour
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Informations personnelles -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Informations personnelles</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nom complet</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date de naissance</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->birth_date ? $user->birth_date->format('d/m/Y') : 'Non renseignée' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Genre</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @switch($user->gender)
                                        @case('M')
                                            Masculin
                                            @break
                                        @case('F')
                                            Féminin
                                            @break
                                        @case('O')
                                            Autre
                                            @break
                                        @default
                                            Non renseigné
                                    @endswitch
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Informations de contact</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->phone ?: 'Non renseigné' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($user->address)
                                        {{ $user->address }}<br>
                                        {{ $user->postal_code }} {{ $user->city }}
                                    @else
                                        Non renseignée
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Informations professionnelles -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Informations professionnelles</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Rôle</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ ucfirst($user->role) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Zone</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->zone ? $user->zone->name : 'Non assignée' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Département</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->department ?: 'Non renseigné' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Poste</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->position ?: 'Non renseigné' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date d'embauche</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->hire_date ? $user->hire_date->format('d/m/Y') : 'Non renseignée' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Objectif mensuel</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ number_format($user->monthly_goal, 2) }} €</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Informations de sécurité -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-700">Informations de sécurité</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dl class="grid grid-cols-1 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Authentification à deux facteurs</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @if($user->two_factor_enabled)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Activée
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Désactivée
                                        </span>
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière connexion</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Jamais' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Compte créé le</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dernière mise à jour</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->updated_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
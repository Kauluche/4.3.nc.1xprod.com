<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Styles -->
</head>

@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold text-gray-900">Tableau de bord</h1>
                    @php
                        $unreadCount = \App\Models\Notification::where('notifiable_id', auth()->id())
                            ->where('notifiable_type', \App\Models\User::class)
                            ->whereNull('read_at')
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            {{ $unreadCount }} notification(s) non lue(s)
                        </span>
                    @endif
                </div>

                @if(auth()->user()->isAdmin() && isset($pharmacy_deletion_requests) && $pharmacy_deletion_requests->isNotEmpty())
                    <!-- Demandes de suppression de pharmacie -->
                    <div class="mt-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-medium text-red-600">
                                        Demandes de suppression de pharmacie en attente
                                    </h2>
                                    @php
                                        $unreadCount = \App\Models\Notification::where('notifiable_id', auth()->id())
                                            ->where('notifiable_type', \App\Models\User::class)
                                            ->whereNull('read_at')
                                            ->count();
                                    @endphp
                                   
                                </div>
                                <div class="space-y-4">
                                    @foreach($pharmacy_deletion_requests as $notification)
                                        <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg">
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $notification->data['message'] }}
                                                </p>
                                                @if(isset($notification->data['commercial_first_name']) && isset($notification->data['commercial_last_name']))
                                                    <p class="text-sm text-gray-500">
                                                        {{ $notification->data['commercial_first_name'] }} {{ $notification->data['commercial_last_name'] }}
                                                    </p>
                                                @endif
                                                <p class="text-sm text-gray-500">
                                                    {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="flex items-center space-x-4">
                                                <form action="{{ route('crm.admin.notifications.approve', $notification) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-md transition duration-150 ease-in-out" style="background-color: #059669;">
                                                        Approuver
                                                    </button>
                                                </form>
                                                <form action="{{ route('crm.admin.notifications.reject', $notification) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition duration-150 ease-in-out">
                                                        Rejeter
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-8"></div>
                @endif

                @if(auth()->user()->role === 'commercial')
                    <!-- Notifications de réponse aux demandes de suppression -->
                    @if($notifications->whereIn('type', ['pharmacy_deletion_approved', 'pharmacy_deletion_rejected'])->isNotEmpty())
                        <div class="mt-8">
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h2 class="text-lg font-medium text-gray-900">
                                            Réponses à vos demandes de suppression
                                        </h2>
                                        @php
                                            $unreadCount = \App\Models\Notification::where('notifiable_id', auth()->id())
                                                ->where('notifiable_type', \App\Models\User::class)
                                                ->whereNull('read_at')
                                                ->count();
                                        @endphp
                                        @if($unreadCount > 0)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                {{ $unreadCount }} nouvelle(s) notification(s)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="space-y-4">
                                        @foreach($notifications->whereIn('type', ['pharmacy_deletion_approved', 'pharmacy_deletion_rejected']) as $notification)
                                            <div class="flex items-center justify-between p-4 rounded-lg 
                                                {{ $notification->type === 'pharmacy_deletion_approved' ? 'bg-green-50' : 'bg-red-50' }}">
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">
                                                        {{ $notification->data['message'] }}
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                    </p>
                                                </div>
                                                <form action="{{ route('crm.notifications.markAsRead', $notification) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                                        Marquer comme lu
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Statistiques générales -->
                    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Total des pharmacies -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total des pharmacies</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $total_pharmacies }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Clients -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Clients</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $client_pharmacies }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Prospects -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Prospects</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $prospect_pharmacies }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Taux de conversion -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Taux de conversion</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $conversion_rate }}%</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        
                    <!-- Statistiques des commandes -->
                    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Total des commandes -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Total des commandes</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $total_orders }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Montant total -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Montant total</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ number_format($total_orders_amount, 2) }} €</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commandes du mois -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Commandes du mois</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ $monthly_orders }}</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Panier moyen -->
                        <div class="bg-white overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 truncate">Panier moyen</dt>
                                            <dd class="flex items-baseline">
                                                <div class="text-2xl font-semibold text-gray-900">{{ number_format($average_cart, 2) }} €</div>
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sélecteur de période pour les graphiques -->
                    <div class="mt-8 w-full">
                        <div class="bg-white overflow-hidden shadow rounded-lg w-full mb-6">
                            <div class="p-5">
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Sélectionner une période</h2>
                                <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Sélecteur de type de période -->
                                        <div>
                                            <label for="period_type" class="block text-sm font-medium text-gray-700 mb-1">Type de période</label>
                                            <select id="period_type" name="period_type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" onchange="toggleCustomDateFields()">
                                                <option value="last30days" {{ $period_type == 'last30days' ? 'selected' : '' }}>30 derniers jours</option>
                                                <option value="last3months" {{ $period_type == 'last3months' ? 'selected' : '' }}>3 derniers mois</option>
                                                <option value="last6months" {{ $period_type == 'last6months' ? 'selected' : '' }}>6 derniers mois</option>
                                                <option value="lastyear" {{ $period_type == 'lastyear' ? 'selected' : '' }}>12 derniers mois</option>
                                                <option value="custom" {{ $period_type == 'custom' ? 'selected' : '' }}>Période personnalisée</option>
                                            </select>
                                        </div>
                                        
                                        <!-- Bouton d'application du filtre -->
                                        <div class="flex items-end">
                                            <button type="submit" style="background-color: #047857; color: white; border: 2px solid #1f2937;" class="inline-flex items-center px-4 py-2 border-2 border-gray-800 text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <svg class="-ml-1 mr-2 h-5 w-5 text-yellow-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                </svg>
                                                Appliquer le filtre
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Champs de date personnalisée (affichés uniquement si "Période personnalisée" est sélectionné) -->
                                    <div id="custom_date_fields" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $period_type != 'custom' ? 'hidden' : '' }}">
                                        <div>
                                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                                            <input type="date" id="start_date" name="start_date" value="{{ $start_date ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                                            <input type="date" id="end_date" name="end_date" value="{{ $end_date ?? '' }}" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Graphiques des ventes et clients -->
                    <div class="mt-4 flex flex-col items-center">
                        <!-- Graphique des ventes -->
                        <div class="bg-white overflow-hidden shadow rounded-lg w-full mb-6">
                            <div class="p-5">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-medium text-gray-900">Ventes par période</h2>
                                    <a href="{{ route('export.sales') }}" style="background-color: #047857; color: white; border: 2px solid #1f2937;" class="inline-flex items-center px-3 py-2 border-2 border-gray-800 text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4 text-yellow-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                        Exporter CSV
                                    </a>
                                </div>
                                <div class="chart-container">
                                    <canvas id="salesChart" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Graphique des clients -->
                        <div class="bg-white overflow-hidden shadow rounded-lg w-full mb-6">
                            <div class="p-5">
                                <div class="flex justify-between items-center mb-4">
                                    <h2 class="text-lg font-medium text-gray-900">Clients rapportés par période</h2>
                                    <a href="{{ route('export.clients') }}" style="background-color: #047857; color: white; border: 2px solid #1f2937;" class="inline-flex items-center px-3 py-2 border-2 border-gray-800 text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <svg class="-ml-0.5 mr-2 h-4 w-4 text-yellow-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                        </svg>
                                        Exporter CSV
                                    </a>
                                </div>
                                <div class="chart-container">
                                    <canvas id="clientsChart" class="chart-canvas"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Carte des pharmacies -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Carte des pharmacies</h2>
                        </div>
                        <div class="max-w-4xl mx-auto">
                            <div class="bg-white shadow overflow-hidden sm:rounded-md" style="height: 600px;">
                                <div class="h-full rounded-lg overflow-hidden" id="pharmacies-map"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Pharmacies récentes -->
                    <div class="mt-8">
                        <div class="flex justify-between items-center mb-4">
                            <h2 class="text-lg font-medium text-gray-900">Pharmacies récentes</h2>
                            <a href="{{ route('pharmacies.index') }}" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Voir toutes mes pharmacies
                            </a>
                        </div>
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul class="divide-y divide-gray-200">
                                @forelse($recent_pharmacies as $pharmacy)
                                    <li>
                                        <a href="{{ route('pharmacies.show', $pharmacy) }}" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-sm font-medium text-indigo-600 truncate">
                                                        {{ $pharmacy->name }}
                                                    </div>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $pharmacy->status === 'client' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                            {{ ucfirst($pharmacy->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                            {{ $pharmacy->city }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <p>
                                                            Ajoutée le {{ $pharmacy->created_at->format('d/m/Y') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                                        Aucune pharmacie récente
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                    <!-- Commandes récentes -->
                    <div class="mt-8">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Commandes récentes</h2>
                        <div class="flex space-x-2">
                            <a href="{{ route('export.recent-sales') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-indigo-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="-ml-0.5 mr-2 h-4 w-4 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Exporter CSV
                            </a>
                            <a href="{{ route('commercial.orders.index') }}" class="btn-primary">
                                <i class="fas fa-plus mr-2"></i>Voir toutes mes commandes
                            </a>
                        </div>
                    </div>
                        <div class="bg-white shadow overflow-hidden sm:rounded-md">
                            <ul class="divide-y divide-gray-200">
                                @forelse($recent_orders as $order)
                                    <li>
                                        <a href="{{ route('orders.show', $order) }}" class="block hover:bg-gray-50">
                                            <div class="px-4 py-4 sm:px-6">
                                                <div class="flex items-center justify-between">
                                                    <div class="text-sm font-medium text-indigo-600 truncate">
                                                        Commande #{{ $order->id }}
                                                    </div>
                                                    <div class="ml-2 flex-shrink-0 flex">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                               ($order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="mt-2 sm:flex sm:justify-between">
                                                    <div class="sm:flex">
                                                        <p class="flex items-center text-sm text-gray-500">
                                                            <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                            </svg>
                                                            {{ $order->pharmacy->name }}
                                                        </p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-sm text-gray-500 sm:mt-0">
                                                        <svg class="flex-shrink-0 mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <p>
                                                            {{ number_format($order->total, 2) }} €
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                                        Aucune commande récente
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                        @if(auth()->user()->isAdmin())
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Total Pharmacies</h3>
                                    <p class="text-3xl font-bold text-blue-600">{{ $total_pharmacies }}</p>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Total Commandes</h3>
                                    <p class="text-3xl font-bold text-green-600">{{ $total_orders }}</p>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Total Documents</h3>
                                    <p class="text-3xl font-bold text-purple-600">{{ $total_documents }}</p>
                                </div>
                            </div>
                            <!-- Notifications de connexion des commerciaux -->
                            @if($notifications->where('type', 'user_connection')->isNotEmpty())
                                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6">
                                        <div class="flex justify-between items-center mb-4">
                                            <h2 class="text-lg font-medium text-gray-900">
                                                Activité des commerciaux
                                            </h2>
                                        </div>
                                        <div class="space-y-4">
                                            @foreach($notifications->where('type', 'user_connection') as $notification)
                                                <div class="flex items-center justify-between p-4 rounded-lg 
                                                    {{ $notification->data['connection_type'] === 'login' ? 'bg-green-50' : 'bg-red-50' }}">
                                                    <div class="flex-1">
                                                        <p class="text-sm font-medium text-gray-900">
                                                            {{ $notification->data['message'] }}
                                                        </p>
                                                        <p class="text-sm text-gray-500">
                                                            {{ Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                    <form action="{{ route('crm.notifications.markAsRead', $notification) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                                            Marquer comme lu
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Mes Pharmacies</h3>
                                    <p class="text-3xl font-bold text-blue-600">{{ $pharmacies }}</p>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Mes Commandes</h3>
                                    <p class="text-3xl font-bold text-green-600">{{ $orders }}</p>
                                </div>
                            </div>
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900">Mes Documents</h3>
                                    <p class="text-3xl font-bold text-purple-600">{{ $documents }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Commandes récentes -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Commandes récentes</h2>
                                @if($recent_orders->isEmpty())
                                    <p class="text-gray-500">Aucune commande récente</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pharmacie</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Montant</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($recent_orders as $order)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm font-medium text-gray-900">{{ $order->pharmacy->name }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900">{{ $order->created_at->format('d/m/Y') }}</div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="text-sm text-gray-900">{{ number_format($order->total, 2) }} €</div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Documents récents -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Documents récents</h2>
                                @if($recent_documents->isEmpty())
                                    <p class="text-gray-500">Aucun document récent</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($recent_documents as $document)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900">{{ $document->title }}</h3>
                                                    <p class="text-sm text-gray-500">{{ $document->created_at->format('d/m/Y') }}</p>
                                                </div>
                                                <a href="{{ route('documents.show', $document) }}" class="text-blue-600 hover:text-blue-900">
                                                    Voir
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Notifications -->
                    <div class="mt-8">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h2 class="text-xl font-semibold text-gray-900 mb-4">Notifications récentes</h2>
                                @if($notifications->isEmpty())
                                    <p class="text-gray-500">Aucune notification</p>
                                @else
                                    <div class="space-y-4">
                                        @foreach($notifications as $notification)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                                <div>
                                                    <h3 class="text-sm font-medium text-gray-900">{{ $notification->title }}</h3>
                                                    <p class="text-sm text-gray-500">{{ $notification->message }}</p>
                                                    <p class="text-xs text-gray-400">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                                @if(!$notification->read_at)
                                                    <form action="{{ route('crm.notifications.markAsRead', $notification) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-blue-600 hover:text-blue-900">
                                                            Marquer comme lu
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet">

<style>
    /* Style pour s'assurer que les graphiques prennent toute la largeur disponible */
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
    canvas.chart-canvas {
        width: 100% !important;
    }
</style>

<!-- Initialisation des graphiques -->
@if(auth()->user()->role === 'commercial')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation du graphique des ventes
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($sales_chart_data['labels'] ?? []) !!},
                datasets: [{
                    label: 'Ventes (€)',
                    data: {!! json_encode($sales_chart_data['data'] ?? []) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' €';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toLocaleString() + ' €';
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 20,
                            padding: 20
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 0,
                        bottom: 10
                    }
                }
            }
        });
        
        // Initialisation du graphique des clients
        const clientsCtx = document.getElementById('clientsChart').getContext('2d');
        const clientsChart = new Chart(clientsCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($clients_chart_data['labels'] ?? []) !!},
                datasets: [{
                    label: 'Nouveaux clients',
                    data: {!! json_encode($clients_chart_data['data'] ?? []) !!},
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 20,
                            padding: 20
                        }
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 0,
                        bottom: 10
                    }
                }
            }
        });
    });
</script>
@endif

<!-- Script pour ajuster la taille des graphiques -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fonction pour ajuster la taille des graphiques
        function resizeCharts() {
            const chartContainers = document.querySelectorAll('.chart-container');
            chartContainers.forEach(container => {
                const canvas = container.querySelector('canvas');
                if (canvas) {
                    canvas.style.width = '100%';
                    canvas.style.maxWidth = '100%';
                }
            });
        }
        
        // Attendre que Chart.js ait fini de rendre les graphiques
        setTimeout(resizeCharts, 100);
        window.addEventListener('resize', resizeCharts);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation de la carte
        console.log('Initialisation de la carte...');
        const map = L.map('pharmacies-map').setView([46.603354, 1.888334], 6);
        console.log('Carte initialisée:', map);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        console.log('Fond de carte ajouté');

        // Limites de la France
        const franceBounds = L.latLngBounds(
            L.latLng(41.0, -5.0), // Sud-Ouest
            L.latLng(51.5, 9.0)   // Nord-Est
        );
        map.setMaxBounds(franceBounds);
        map.setMinZoom(6);

        // Récupération des pharmacies via AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('Token CSRF:', csrfToken);

        // Appel direct à l'API des pharmacies
        fetch('api/commercial/pharmacies', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Réponse reçue:', response);
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(pharmacies => {
            console.log('Pharmacies reçues:', pharmacies);
            pharmacies.forEach(pharmacy => {
                if (pharmacy.latitude && pharmacy.longitude) {
                    const marker = L.marker([pharmacy.latitude, pharmacy.longitude])
                        .addTo(map)
                        .bindPopup(`
                            <strong>${pharmacy.name}</strong><br>
                            ${pharmacy.address}<br>
                            ${pharmacy.postal_code} ${pharmacy.city}
                        `);
                }
            });
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des pharmacies:', error);
            // Afficher un message d'erreur sur la carte
            const errorDiv = document.createElement('div');
            errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative';
            errorDiv.innerHTML = `
                <strong class="font-bold">Erreur !</strong>
                <span class="block sm:inline">Impossible de charger les pharmacies. Veuillez rafraîchir la page.</span>
            `;
            document.getElementById('pharmacies-map').appendChild(errorDiv);
        });
    });
</script>

<script>
    function toggleCustomDateFields() {
        const periodType = document.getElementById('period_type').value;
        const customDateFields = document.getElementById('custom_date_fields');
        
        if (periodType === 'custom') {
            customDateFields.classList.remove('hidden');
        } else {
            customDateFields.classList.add('hidden');
        }
    }
    
    // Initialiser l'état au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        toggleCustomDateFields();
    });
</script>
@endpush
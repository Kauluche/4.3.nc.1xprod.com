<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('dashboard') }}">
                                <img src="{{ asset('images/vitrine/logo.png') }}" alt="NaturaCorp" class="h-9 w-auto">
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('Tableau de bord') }}
                            </x-nav-link>

                            @if(auth()->user()->role === 'commercial')
                                <x-nav-link :href="route('pharmacies.index')" :active="request()->routeIs('pharmacies.*')">
                                    {{ __('Pharmacies') }}
                                </x-nav-link>
                                <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                                    {{ __('Commandes') }}
                                </x-nav-link>
                                <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                                    {{ __('Documents') }}
                                </x-nav-link>
                                <x-nav-link :href="route('crm.notifications.index')" :active="request()->routeIs('crm.notifications.*')">
                                    {{ __('Notifications') }}
                                    @php
                                        $unreadCount = \App\Models\Notification::where('notifiable_id', auth()->id())
                                            ->where('notifiable_type', \App\Models\User::class)
                                            ->whereNull('read_at')
                                            ->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </x-nav-link>
                            @else
                                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                    {{ __('Utilisateurs') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.zones.index')" :active="request()->routeIs('admin.zones.*')">
                                    {{ __('Zones') }}
                                </x-nav-link>
                                <x-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                                    {{ __('Rapports') }}
                                </x-nav-link>
                                <x-nav-link :href="route('crm.admin.notifications.index')" :active="request()->routeIs('crm.admin.notifications.*')">
                                    {{ __('Notifications') }}
                                    @php
                                        $unreadCount = \App\Models\Notification::where('notifiable_id', auth()->id())
                                            ->where('notifiable_type', \App\Models\User::class)
                                            ->whereNull('read_at')
                                            ->count();
                                    @endphp
                                    @if($unreadCount > 0)
                                        <span class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                            {{ $unreadCount }}
                                        </span>
                                    @endif
                                </x-nav-link>
                            @endif
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <div>{{ Auth::user()->full_name }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    {{ __('Profil') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Déconnexion') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Tableau de bord') }}
                    </x-responsive-nav-link>

                    @if(auth()->user()->role === 'commercial')
                        <x-responsive-nav-link :href="route('pharmacies.index')" :active="request()->routeIs('pharmacies.*')">
                            {{ __('Pharmacies') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                            {{ __('Commandes') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')">
                            {{ __('Documents') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('crm.notifications.index')" :active="request()->routeIs('crm.notifications.*')">
                            {{ __('Notifications') }}
                        </x-responsive-nav-link>
                    @else
                        <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Utilisateurs') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.zones.index')" :active="request()->routeIs('admin.zones.*')">
                            {{ __('Zones') }}
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('admin.reports.index')" :active="request()->routeIs('admin.reports.*')">
                            {{ __('Rapports') }}
                        </x-responsive-nav-link>
                    @endif
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->full_name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('Profil') }}
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-responsive-nav-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Déconnexion') }}
                            </x-responsive-nav-link>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Layout: admin.blade.php -->
    <title>{{ config('app.name', 'Laravel') }} - Administration</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        console.log('=== Début du chargement ===');
        console.log('Layout admin chargé');
        console.log('Vue actuelle:', '{{ request()->route()->getName() }}');
        
        // Vérifier si le CSS est chargé
        const styleSheets = document.styleSheets;
        console.log('Nombre de feuilles de style:', styleSheets.length);
        for (let i = 0; i < styleSheets.length; i++) {
            console.log(`Feuille de style ${i}:`, styleSheets[i].href);
        }

        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== DOM chargé ===');
            console.log('DOM chargé dans admin.blade.php');
            
            // Vérifier si les styles sont appliqués
            const card = document.querySelector('.card');
            if (card) {
                console.log('Classe .card trouvée');
                const computedStyle = window.getComputedStyle(card);
                console.log('Styles de .card:', {
                    backgroundColor: computedStyle.backgroundColor,
                    boxShadow: computedStyle.boxShadow,
                    borderRadius: computedStyle.borderRadius,
                    border: computedStyle.border,
                    padding: computedStyle.padding
                });
            } else {
                console.log('Classe .card non trouvée');
            }

            // Vérifier les inputs
            const inputs = document.querySelectorAll('.form-input');
            console.log('Nombre d\'inputs trouvés:', inputs.length);
            if (inputs.length > 0) {
                const computedStyle = window.getComputedStyle(inputs[0]);
                console.log('Styles du premier input:', {
                    width: computedStyle.width,
                    borderColor: computedStyle.borderColor,
                    borderRadius: computedStyle.borderRadius,
                    border: computedStyle.border,
                    padding: computedStyle.padding
                });
            }

            // Vérifier les boutons
            const primaryButtons = document.querySelectorAll('.btn-primary');
            const secondaryButtons = document.querySelectorAll('.btn-secondary');
            console.log('Nombre de boutons trouvés:', {
                primary: primaryButtons.length,
                secondary: secondaryButtons.length
            });

            if (primaryButtons.length > 0) {
                const computedStyle = window.getComputedStyle(primaryButtons[0]);
                console.log('Styles du premier bouton primary:', {
                    backgroundColor: computedStyle.backgroundColor,
                    color: computedStyle.color,
                    border: computedStyle.border,
                    padding: computedStyle.padding
                });
            }

            // Vérifier les labels
            const labels = document.querySelectorAll('.form-label');
            console.log('Nombre de labels trouvés:', labels.length);
            if (labels.length > 0) {
                const computedStyle = window.getComputedStyle(labels[0]);
                console.log('Styles du premier label:', {
                    color: computedStyle.color,
                    fontSize: computedStyle.fontSize,
                    fontWeight: computedStyle.fontWeight,
                    marginBottom: computedStyle.marginBottom
                });
            }

            // Vérifier le contenu de la page
            console.log('=== Contenu de la page ===');
            console.log('Titre de la page:', document.title);
            console.log('URL actuelle:', window.location.href);
            console.log('Route actuelle:', '{{ request()->route()->getName() }}');
        });
    </script>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <a href="{{ route('admin.dashboard') }}" class="text-xl font-bold text-gray-800">
                                {{ config('app.name', 'Laravel') }}
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.dashboard') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Tableau de bord
                            </a>
                            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.users.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Utilisateurs
                            </a>
                            <a href="{{ route('admin.zones.index') }}" class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('admin.zones.*') ? 'border-blue-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Zones
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="ml-3 relative">
                            <div class="flex items-center">
                                <span class="text-gray-700 mr-4">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="text-gray-500 hover:text-gray-700">
                                        Déconnexion
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html> 
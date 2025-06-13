<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NaturaCorp - Solutions Pharmaceutiques</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a]">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav class="bg-white dark:bg-[#161615] shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <span class="text-2xl font-bold text-[#1b1b18] dark:text-white">NaturaCorp</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-[#1b1b18] dark:text-white hover:text-gray-600 dark:hover:text-gray-300">
                                Tableau de bord
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-[#1b1b18] dark:text-white hover:text-gray-600 dark:hover:text-gray-300">
                                Connexion
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="bg-[#1b1b18] text-white px-4 py-2 rounded-md hover:bg-gray-800">
                                    Inscription
                                </a>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="flex-1">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-[#1b1b18] dark:text-white mb-4">
                        Bienvenue sur NaturaCorp
                    </h1>
                    <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                        Votre partenaire de confiance pour la gestion pharmaceutique
                    </p>
                </div>

                <!-- Access Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
                    <!-- Commercial Access -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                        <h2 class="text-2xl font-semibold text-[#1b1b18] dark:text-white mb-4">
                            Espace Commercial
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Gérez vos commandes et suivez vos clients
                        </p>
                        <a href="{{ route('login') }}" class="inline-block bg-[#1b1b18] text-white px-4 py-2 rounded-md hover:bg-gray-800">
                            Accéder
                        </a>
                    </div>

                    <!-- Admin Access -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                        <h2 class="text-2xl font-semibold text-[#1b1b18] dark:text-white mb-4">
                            Administration
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Gestion complète de la plateforme
                        </p>
                        <a href="{{ route('login') }}" class="inline-block bg-[#1b1b18] text-white px-4 py-2 rounded-md hover:bg-gray-800">
                            Accéder
                        </a>
                    </div>

                    <!-- Public Access -->
                    <div class="bg-white dark:bg-[#161615] rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow">
                        <h2 class="text-2xl font-semibold text-[#1b1b18] dark:text-white mb-4">
                            Site Vitrine
                        </h2>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Découvrez nos services et produits
                        </p>
                        <a href="#" class="inline-block bg-[#1b1b18] text-white px-4 py-2 rounded-md hover:bg-gray-800">
                            Visiter
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="bg-white dark:bg-[#161615] border-t border-gray-200 dark:border-gray-700">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="text-center text-gray-600 dark:text-gray-300">
                    <p>&copy; {{ date('Y') }} NaturaCorp. Tous droits réservés.</p>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>

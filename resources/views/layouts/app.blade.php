<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ShopMe - Votre boutique en ligne')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <!-- Navigation Desktop -->
    <nav class="bg-white shadow-md sticky top-0 z-50 hidden md:block">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="text-2xl font-bold text-indigo-600">ShopMe</a>
                
                <div class="flex-1 max-w-xl mx-8">
                    <form action="{{ route('products.index') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Rechercher un produit..." 
                               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <button type="submit" class="absolute right-2 top-2 text-gray-400 hover:text-indigo-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center space-x-6">
                    @auth
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            @if(auth()->user()->cartItems()->count() > 0)
                                <span class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->cartItems()->count() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-indigo-600">
                            <i class="fas fa-box text-xl"></i>
                        </a>
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-indigo-600">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span>{{ Auth::user()->name }}</span>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition">
                                <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mon Profil</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Déconnexion</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Navigation Mobile -->
    <nav class="bg-white shadow-md sticky top-0 z-50 md:hidden" x-data="{ open: false }">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="text-xl font-bold text-indigo-600">ShopMe</a>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            @if(auth()->user()->cartItems()->count() > 0)
                                <span class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->cartItems()->count() }}
                                </span>
                            @endif
                        </a>
                    @endauth
                    <button @click="open = !open" class="text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            <div x-show="open" x-cloak class="pb-4">
                <form action="{{ route('products.index') }}" method="GET" class="mb-4">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Rechercher..." 
                           class="w-full px-4 py-2 border rounded-lg">
                </form>
                <div class="space-y-2">
                    @auth
                        <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Mon Profil</a>
                        <a href="{{ route('orders.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Mes Commandes</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full text-left block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Déconnexion</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 rounded">Connexion</a>
                        <a href="{{ route('register') }}" class="block px-4 py-2 bg-indigo-600 text-white rounded">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Messages Flash -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 mt-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Contenu principal -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">ShopMe</h3>
                    <p class="text-gray-400">Votre boutique en ligne préférée pour tous vos besoins.</p>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Liens rapides</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="{{ route('home') }}" class="hover:text-white">Accueil</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-white">Produits</a></li>
                        @auth
                            <li><a href="{{ route('cart.index') }}" class="hover:text-white">Panier</a></li>
                            <li><a href="{{ route('orders.index') }}" class="hover:text-white">Commandes</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-4">Contact</h4>
                    <p class="text-gray-400">Email: contact@shopme.com</p>
                    <p class="text-gray-400">Tél: +33 1 23 45 67 89</p>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} ShopMe. Tous droits réservés.</p>
            </div>
        </div>
    </footer>
</body>
</html>

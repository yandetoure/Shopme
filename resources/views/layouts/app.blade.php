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
<body class="bg-gray-50 pb-24 md:pb-0">
    @php
        $primaryNavLinks = [
            [
                'label' => 'Accueil',
                'route' => 'home',
                'route_is' => 'home',
            ],
            [
                'label' => 'Produits',
                'route' => 'products.index',
                'route_is' => 'products.*',
            ],
        ];

        if(auth()->check()) {
            $currentUser = auth()->user();

            if($currentUser->isSuperAdmin()) {
                $primaryNavLinks[] = [
                    'label' => 'Dashboard',
                    'route' => 'dashboard.superadmin',
                    'route_is' => 'dashboard.*',
                ];
                $primaryNavLinks[] = [
                    'label' => 'Admin',
                    'route' => 'admin.products.index',
                    'route_is' => 'admin.*',
                ];
            } elseif($currentUser->isAdmin()) {
                $primaryNavLinks[] = [
                    'label' => 'Dashboard',
                    'route' => 'dashboard.admin',
                    'route_is' => 'dashboard.*',
                ];
                $primaryNavLinks[] = [
                    'label' => 'Gestion',
                    'route' => 'admin.products.index',
                    'route_is' => 'admin.*',
                ];
            } elseif($currentUser->isVendeur()) {
                $primaryNavLinks[] = [
                    'label' => 'Dashboard',
                    'route' => 'dashboard.vendeur',
                    'route_is' => 'dashboard.*',
                ];
                $primaryNavLinks[] = [
                    'label' => 'Mes commandes',
                    'route' => 'orders.index',
                    'route_is' => 'orders.*',
                ];
            } elseif($currentUser->isClient()) {
                $primaryNavLinks[] = [
                    'label' => 'Favoris',
                    'route' => 'favorites.index',
                    'route_is' => 'favorites.*',
                ];
                $primaryNavLinks[] = [
                    'label' => 'Commandes',
                    'route' => 'orders.index',
                    'route_is' => 'orders.*',
                ];
            }
        } else {
            $primaryNavLinks[] = [
                'label' => 'Connexion',
                'route' => 'login',
                'route_is' => 'login',
            ];
        }
    @endphp

    <!-- Navigation Desktop -->
    <nav class="bg-white shadow-md sticky top-0 z-50 hidden md:block">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="ShopMe" class="h-10">
                </a>
                
                <!-- Navigation principale -->
                <div class="flex items-center space-x-6">
                    @foreach($primaryNavLinks as $link)
                        <a href="{{ route($link['route']) }}" class="text-gray-700 hover:text-orange-500 font-medium {{ request()->routeIs($link['route_is']) ? 'text-orange-500 border-b-2 border-orange-500 pb-1' : '' }}">
                            {{ $link['label'] }}
                        </a>
                    @endforeach
                    
                    <!-- Dropdown Catégories -->
                    @if(isset($navCategories) && $navCategories->count() > 0)
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-orange-500 font-medium flex items-center {{ request()->routeIs('category.*') ? 'text-orange-500 border-b-2 border-orange-500 pb-1' : '' }}">
                            Catégories
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-[960px] bg-white rounded-lg shadow-xl border border-gray-100 py-4 px-4 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 max-h-[75vh] overflow-y-auto">
                            <div class="grid grid-cols-6 gap-x-4 gap-y-3">
                                @foreach($navCategories as $category)
                                    <div class="border-r border-gray-100 last:border-r-0 pr-3 last:pr-0">
                                        @if($category->children->count() > 0)
                                            <!-- Catégorie avec sous-catégories -->
                                            <a href="{{ route('category.show', $category->slug) }}" class="text-xs font-semibold text-gray-900 hover:text-orange-600 block mb-2 pb-1 border-b border-gray-100">
                                                {{ $category->name }}
                                            </a>
                                            <div class="space-y-1">
                                                @foreach($category->children->take(6) as $child)
                                                    <a href="{{ route('category.subcategory', [$category->slug, $child->slug]) }}" class="block text-xs text-gray-600 hover:text-orange-600 hover:pl-1 transition-all py-0.5 truncate" title="{{ $child->name }}">
                                                        {{ $child->name }}
                                                    </a>
                                                @endforeach
                                                @if($category->children->count() > 6)
                                                    <a href="{{ route('category.show', $category->slug) }}" class="block text-xs text-orange-600 font-medium hover:text-orange-700 mt-1">
                                                        +{{ $category->children->count() - 6 }} autres...
                                                    </a>
                                                @endif
                                            </div>
                                        @else
                                            <!-- Catégorie sans sous-catégories -->
                                            <a href="{{ route('category.show', $category->slug) }}" class="block text-xs font-semibold text-gray-900 hover:text-orange-600 py-1">
                                                {{ $category->name }}
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                
                <div class="flex-1 max-w-md mx-4">
                    <form action="{{ route('products.index') }}" method="GET" class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Rechercher..." 
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <button type="submit" class="absolute right-2 top-1.5 text-gray-400 hover:text-orange-500 text-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>

                <div class="flex items-center space-x-6">
                    @auth
                        <a href="{{ route('favorites.index') }}" class="relative text-gray-700 hover:text-orange-500">
                            <i class="fas fa-heart text-xl"></i>
                            @if(auth()->user()->favorites()->count() > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->favorites()->count() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 hover:text-orange-500">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            @if(auth()->user()->cartItems()->count() > 0)
                                <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->cartItems()->count() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('orders.index') }}" class="text-gray-700 hover:text-orange-500">
                            <i class="fas fa-box text-xl"></i>
                        </a>
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700 hover:text-orange-500">
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
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-orange-500">Connexion</a>
                        <a href="{{ route('register') }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">Inscription</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Navigation Mobile -->
    <nav class="bg-white shadow-md sticky top-0 z-50 md:hidden" x-data="{ open: false }">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/logo.png') }}" alt="ShopMe" class="h-8">
                </a>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('favorites.index') }}" class="relative text-gray-700 mr-2">
                            <i class="fas fa-heart text-xl"></i>
                            @if(auth()->user()->favorites()->count() > 0)
                                <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                                    {{ auth()->user()->favorites()->count() }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('cart.index') }}" class="relative text-gray-700 mr-2">
                            <i class="fas fa-shopping-cart text-xl"></i>
                            @if(auth()->user()->cartItems()->count() > 0)
                                <span class="absolute -top-2 -right-2 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
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
            <div 
                x-show="open" 
                x-cloak
                class="fixed inset-0 z-50 md:hidden"
                aria-hidden="true"
            >
                <div 
                    class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                    x-transition:enter="transition-opacity duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition-opacity duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="open = false"
                ></div>
                <div 
                    class="absolute inset-y-0 right-0 w-full max-w-sm bg-white shadow-2xl flex flex-col"
                    x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                >
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <span class="text-base font-semibold text-gray-900">Navigation</span>
                        <button @click="open = false" class="p-2 rounded-full border border-gray-200 text-gray-500 hover:text-gray-800 hover:border-gray-300">
                            <i class="fas fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-5 space-y-6 bg-gray-50">
                        <form action="{{ route('products.index') }}" method="GET" class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Rechercher un produit ou une marque" 
                                   class="w-full pl-11 pr-3 py-2.5 text-sm bg-white border border-gray-200 rounded-lg focus:border-orange-400 focus:ring-1 focus:ring-orange-400 shadow-sm">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                        </form>

                        <!-- Catégories mobile -->
                        @if(isset($navCategories) && $navCategories->count() > 0)
                            <div class="bg-white rounded-2xl p-4 shadow-sm">
                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Catégories populaires</p>
                                        <p class="text-xs text-gray-500">Choisissez une catégorie pour explorer</p>
                                    </div>
                                    <a href="{{ route('products.index') }}" class="text-xs font-semibold text-orange-500 hover:text-orange-600">Tout voir</a>
                                </div>
                                <div class="grid grid-cols-2 gap-2 max-h-[48vh] overflow-y-auto pr-1">
                                    @foreach($navCategories as $category)
                                        <a href="{{ route('category.show', $category->slug) }}" class="flex items-center gap-2 px-3 py-2 rounded-xl border border-gray-100 bg-gray-50 text-xs font-medium text-gray-700 hover:bg-orange-50 hover:border-orange-200 transition">
                                            <span class="w-2 h-2 rounded-full bg-orange-400"></span>
                                            <span class="truncate">{{ $category->name }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="bg-white rounded-2xl p-4 shadow-sm space-y-3">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Espace client</p>
                            @auth
                                <a href="{{ route('orders.index') }}" class="flex items-center justify-between px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-gray-800 text-sm font-medium hover:border-orange-200 hover:bg-orange-50 transition">
                                    <span><i class="fas fa-box text-orange-500 mr-2"></i>Mes Commandes</span>
                                    <i class="fas fa-chevron-right text-xs text-gray-400"></i>
                                </a>
                            @else
                                <div class="space-y-2">
                                    <a href="{{ route('login') }}" class="block w-full text-center px-4 py-2.5 rounded-lg border border-gray-200 text-gray-700 text-sm font-medium hover:border-orange-400">Connexion</a>
                                    <a href="{{ route('register') }}" class="block w-full text-center px-4 py-2.5 rounded-lg bg-orange-500 text-white text-sm font-semibold hover:bg-orange-600">Inscription</a>
                                </div>
                            @endauth
                        </div>
                    </div>
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

    @include('partials.mobile-role-tabs')

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

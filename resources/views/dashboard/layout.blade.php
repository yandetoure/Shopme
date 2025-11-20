<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard - ShopMe')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex-shrink-0" x-data="{ open: false }">
            <div class="flex flex-col h-full">
                <!-- Logo -->
                <div class="p-3 border-b border-gray-700">
                    <a href="{{ route('home') }}" class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="ShopMe" class="h-6">
                        <span class="ml-2 font-semibold text-sm">ShopMe</span>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto p-3">
                    <div class="space-y-1">
                        @php
                            $userRole = Auth::user()->roles->first()->name ?? 'vendeur';
                            $dashboardRoute = match($userRole) {
                                'super_admin' => 'dashboard.superadmin',
                                'admin' => 'dashboard.admin',
                                'vendeur' => 'dashboard.vendeur',
                                default => 'dashboard.vendeur'
                            };
                        @endphp
                        <a href="{{ route($dashboardRoute) }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-chart-line w-4"></i>
                            <span class="ml-2">Dashboard</span>
                        </a>

                        @if(Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin'))
                        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700">
                            <i class="fas fa-home w-4"></i>
                            <span class="ml-2">Site Web</span>
                        </a>
                        @endif

                        @if(Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin'))
                        <div class="pt-3">
                            <p class="px-3 text-xs text-gray-400 uppercase tracking-wider">Gestion</p>
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-box w-4"></i>
                            <span class="ml-2">Produits</span>
                        </a>
                        <a href="{{ route('admin.categories.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-tags w-4"></i>
                            <span class="ml-2">Catégories</span>
                        </a>
                        @endif

                        @if(Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin'))
                        <div class="pt-3">
                            <p class="px-3 text-xs text-gray-400 uppercase tracking-wider">Commandes</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-shopping-cart w-4"></i>
                            <span class="ml-2">Toutes les commandes</span>
                        </a>
                        @endif

                        @if(Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('admin'))
                        <div class="pt-3">
                            <p class="px-3 text-xs text-gray-400 uppercase tracking-wider">Configuration</p>
                        </div>
                        <a href="{{ route('admin.variables.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.variables.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-palette w-4"></i>
                            <span class="ml-2">Variables</span>
                        </a>
                        <a href="{{ route('admin.shipping-rates.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.shipping-rates.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-truck w-4"></i>
                            <span class="ml-2">Livraisons</span>
                        </a>
                        <a href="{{ route('admin.coupons.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.coupons.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-ticket-alt w-4"></i>
                            <span class="ml-2">Coupons</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.settings.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-cog w-4"></i>
                            <span class="ml-2">Paramètres</span>
                        </a>
                        @endif

                        @if(Auth::user()->hasRole('super_admin'))
                        <div class="pt-3">
                            <p class="px-3 text-xs text-gray-400 uppercase tracking-wider">Administration</p>
                        </div>
                        <a href="#" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700">
                            <i class="fas fa-users w-4"></i>
                            <span class="ml-2">Utilisateurs</span>
                        </a>
                        <a href="#" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700">
                            <i class="fas fa-user-shield w-4"></i>
                            <span class="ml-2">Rôles & Permissions</span>
                        </a>
                        @endif

                        @if(!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('super_admin'))
                        <div class="pt-3">
                            <p class="px-3 text-xs text-gray-400 uppercase tracking-wider">Mes Commandes</p>
                        </div>
                        <a href="{{ route('orders.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('orders.index') || request()->routeIs('orders.show') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-shopping-bag w-4"></i>
                            <span class="ml-2">Mes commandes</span>
                        </a>
                        @endif
                    </div>
                </nav>

                <!-- User Info -->
                <div class="p-3 border-t border-gray-700">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <div class="ml-2 flex-1">
                            <p class="text-xs font-medium">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', Auth::user()->roles->first()->name ?? 'client')) }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center px-3 py-2 bg-red-600 rounded-lg hover:bg-red-700 text-xs">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 py-3">
                    <h1 class="text-lg font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4">
                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>


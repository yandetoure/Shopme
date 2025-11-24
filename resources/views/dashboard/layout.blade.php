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
    @php
        $user = Auth::user();
        $userRole = $user?->roles->first()->name ?? 'vendeur';
        $dashboardRoute = match($userRole) {
            'super_admin' => 'dashboard.superadmin',
            'admin' => 'dashboard.admin',
            default => 'dashboard.vendeur'
        };
        $isSuperAdmin = $user?->hasRole('super_admin');
        $isAdmin = $user?->hasRole('admin');
        $isManager = $isSuperAdmin || $isAdmin;

        $mobileNavItems = [
            [
                'label' => 'Dashboard',
                'icon' => 'fa-chart-line',
                'route' => $dashboardRoute,
                'route_is' => 'dashboard.*',
            ],
        ];

        if ($isManager) {
            $mobileNavItems[] = [
                'label' => 'Produits',
                'icon' => 'fa-box',
                'route' => 'admin.products.index',
                'route_is' => 'admin.products.*',
            ];
            $mobileNavItems[] = [
                'label' => 'Commandes',
                'icon' => 'fa-shopping-cart',
                'route' => 'admin.orders.index',
                'route_is' => 'admin.orders.*',
            ];
            $mobileNavItems[] = [
                'label' => 'Paramètres',
                'icon' => 'fa-sliders-h',
                'drawer' => 'settings',
                'children' => [
                    [
                        'label' => 'Visiter site',
                        'icon' => 'fa-home',
                        'route' => 'home',
                        'route_is' => 'home',
                    ],
                    [
                        'label' => 'Catégories',
                        'icon' => 'fa-tags',
                        'route' => 'admin.categories.index',
                        'route_is' => 'admin.categories.*',
                    ],
                    [
                        'label' => 'Variables',
                        'icon' => 'fa-palette',
                        'route' => 'admin.variables.index',
                        'route_is' => 'admin.variables.*',
                    ],
                    [
                        'label' => 'Livraisons',
                        'icon' => 'fa-truck',
                        'route' => 'admin.shipping-rates.index',
                        'route_is' => 'admin.shipping-rates.*',
                    ],
                    [
                        'label' => 'Coupons',
                        'icon' => 'fa-ticket-alt',
                        'route' => 'admin.coupons.index',
                        'route_is' => 'admin.coupons.*',
                    ],
                    [
                        'label' => 'Paramètres généraux',
                        'icon' => 'fa-sliders-h',
                        'route' => 'admin.settings.index',
                        'route_is' => 'admin.settings.*',
                    ],
                ],
            ];
        } else {
            $mobileNavItems[] = [
                'label' => 'Commandes',
                'icon' => 'fa-shopping-bag',
                'route' => 'orders.index',
                'route_is' => 'orders.*',
            ];
            $mobileNavItems[] = [
                'label' => 'Boutique',
                'icon' => 'fa-store',
                'route' => 'home',
                'route_is' => 'home',
            ];
        }

        $mobileNavItems[] = [
            'label' => 'Profil',
            'icon' => 'fa-user',
            'route' => 'profile.index',
            'route_is' => 'profile.*',
        ];
    @endphp
    <div class="flex min-h-screen md:h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="hidden md:flex md:w-64 bg-gray-800 text-white flex-shrink-0" x-data="{ open: false }">
            <div class="flex flex-col h-full w-full">
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
                        <a href="{{ route($dashboardRoute) }}" 
                           class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('dashboard.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-chart-line w-4"></i>
                            <span class="ml-2">Dashboard</span>
                        </a>

                        @if($isManager)
                        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700">
                            <i class="fas fa-home w-4"></i>
                            <span class="ml-2">Site Web</span>
                        </a>
                        @endif

                        @if($isManager)
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

                        @if($isManager)
                        <div class="pt-3">
                            <p class="px-3 text-xs text-gray-400 uppercase tracking-wider">Commandes</p>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-700' : '' }}">
                            <i class="fas fa-shopping-cart w-4"></i>
                            <span class="ml-2">Toutes les commandes</span>
                        </a>
                        @endif

                        @if($isManager)
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

                        @if($isSuperAdmin)
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

                        @if(!$isManager)
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
                            <p class="text-xs font-medium">{{ $user?->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst(str_replace('_', ' ', $userRole ?? 'client')) }}</p>
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
            <main class="flex-1 overflow-y-auto p-4 pb-24 md:pb-4">
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

    @if(!empty($mobileNavItems))
        <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] md:hidden" x-data="{ activeDrawer: null }">
            <div class="grid grid-cols-{{ count($mobileNavItems) }}">
                @foreach($mobileNavItems as $item)
                    @if(isset($item['children']))
                        @php
                            $drawerKey = $item['drawer'] ?? \Illuminate\Support\Str::slug($item['label']);
                            $hasActiveChild = false;
                            foreach ($item['children'] as $child) {
                                $childActive = isset($child['route_is'])
                                    ? request()->routeIs($child['route_is'])
                                    : request()->routeIs($child['route']);
                                if ($childActive) {
                                    $hasActiveChild = true;
                                    break;
                                }
                            }
                        @endphp
                        <button type="button"
                                @click="activeDrawer = activeDrawer === '{{ $drawerKey }}' ? null : '{{ $drawerKey }}'"
                                class="flex flex-col items-center justify-center py-2 text-[11px] font-medium w-full {{ $hasActiveChild ? 'text-orange-600' : 'text-gray-500' }}">
                            <i class="fas {{ $item['icon'] }} text-base mb-1"></i>
                            <span>{{ $item['label'] }}</span>
                            <i class="fas fa-chevron-up text-[9px] {{ $hasActiveChild ? 'text-orange-500' : 'text-gray-400' }}"></i>
                        </button>
                    @else
                        @php
                            $isActive = isset($item['route_is'])
                                ? request()->routeIs($item['route_is'])
                                : request()->routeIs($item['route']);
                        @endphp
                        <a href="{{ route($item['route']) }}" class="flex flex-col items-center justify-center py-2 text-[11px] font-medium {{ $isActive ? 'text-orange-600' : 'text-gray-500' }}">
                            <i class="fas {{ $item['icon'] }} text-base mb-1"></i>
                            <span>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            @foreach($mobileNavItems as $item)
                @if(isset($item['children']))
                    @php
                        $drawerKey = $item['drawer'] ?? \Illuminate\Support\Str::slug($item['label']);
                    @endphp
                    <div class="bg-white border-t border-gray-200 shadow-inner" 
                         x-show="activeDrawer === '{{ $drawerKey }}'"
                         x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-full"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-full">
                        <div class="flex items-center justify-between px-4 py-2">
                            <p class="text-xs font-semibold text-gray-700">{{ $item['label'] }}</p>
                            <button type="button" class="text-gray-500 text-xs" @click="activeDrawer = null">
                                Fermer
                                <i class="fas fa-xmark ml-1"></i>
                            </button>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($item['children'] as $child)
                                @php
                                    $isActive = isset($child['route_is'])
                                        ? request()->routeIs($child['route_is'])
                                        : request()->routeIs($child['route']);
                                @endphp
                                <a href="{{ route($child['route']) }}" class="flex items-center justify-between px-4 py-3 text-sm {{ $isActive ? 'text-orange-600' : 'text-gray-700' }}">
                                    <div class="flex items-center space-x-3">
                                        <i class="fas {{ $child['icon'] }} text-base"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </div>
                                    <i class="fas fa-chevron-right text-xs"></i>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </nav>
    @endif
</body>
</html>


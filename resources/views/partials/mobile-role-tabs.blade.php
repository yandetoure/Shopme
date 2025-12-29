@php
    $mobileTabs = [];

    if (auth()->check()) {
        $user = auth()->user();
        $userRole = $user->roles->first()->name ?? 'client';
        
        // Déterminer la route du dashboard selon le rôle
        $dashboardRoute = match($userRole) {
            'super_admin' => 'dashboard.superadmin',
            'admin' => 'dashboard.admin',
            'vendeur' => 'dashboard.vendeur',
            default => null
        };

        // Si c'est un client (pas de dashboard), afficher Accueil, Produits, Catégories, Profil
        if (!$dashboardRoute) {
            $mobileTabs = [
                [
                    'label' => 'Accueil',
                    'icon' => 'fa-house',
                    'route' => 'home',
                    'route_is' => 'home',
                ],
                [
                    'label' => 'Produits',
                    'icon' => 'fa-store',
                    'route' => 'products.index',
                    'route_is' => 'products.*',
                ],
                [
                    'label' => 'Catégories',
                    'icon' => 'fa-tags',
                    'route' => 'categories.index',
                    'route_is' => 'categories.*|category.*',
                ],
                [
                    'label' => 'Profil',
                    'icon' => 'fa-user',
                    'route' => 'profile.index',
                    'route_is' => 'profile.*',
                ],
            ];
        } else {
            // Tabs pour les autres rôles (admin, vendeur, super_admin)
            $mobileTabs = [
                [
                    'label' => 'Accueil',
                    'icon' => 'fa-house',
                    'route' => 'home',
                    'route_is' => 'home',
                ],
                [
                    'label' => 'Produits',
                    'icon' => 'fa-store',
                    'route' => 'products.index',
                    'route_is' => 'products.*',
                ],
                [
                    'label' => 'Profil',
                    'icon' => 'fa-user',
                    'route' => 'profile.index',
                    'route_is' => 'profile.*',
                ],
                [
                    'label' => 'Dashboard',
                    'icon' => 'fa-chart-line',
                    'route' => $dashboardRoute,
                    'route_is' => 'dashboard.*',
                ],
            ];
        }
    } else {
        // Tabs pour utilisateurs non connectés : Accueil, Produits, Catégories, Profil
        $mobileTabs = [
            [
                'label' => 'Accueil',
                'icon' => 'fa-house',
                'route' => 'home',
                'route_is' => 'home',
            ],
            [
                'label' => 'Produits',
                'icon' => 'fa-store',
                'route' => 'products.index',
                'route_is' => 'products.*',
            ],
            [
                'label' => 'Catégories',
                'icon' => 'fa-tags',
                'route' => 'categories.index',
                'route_is' => 'categories.*|category.*',
            ],
            [
                'label' => 'Profil',
                'icon' => 'fa-user',
                'route' => 'login',
                'route_is' => 'login',
            ],
        ];
    }
@endphp

@if(!empty($mobileTabs))
    <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-100 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] md:hidden z-[100]" style="z-index: 100;">
        <div class="grid grid-cols-{{ count($mobileTabs) }} divide-x divide-gray-100">
            @foreach($mobileTabs as $tab)
                @php
                    $isActive = isset($tab['route_is'])
                        ? request()->routeIs($tab['route_is'])
                        : request()->routeIs($tab['route']);
                @endphp
                <a href="{{ route($tab['route']) }}" class="relative flex flex-col items-center justify-center py-2 text-xs font-medium {{ $isActive ? 'text-orange-600' : 'text-gray-500' }}">
                    <i class="fas {{ $tab['icon'] }} text-lg mb-1"></i>
                    <span>{{ $tab['label'] }}</span>
                    @if(!empty($tab['badge']))
                        <span class="absolute top-1 right-4 bg-orange-500 text-white text-[10px] font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">
                            {{ $tab['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </nav>
@endif


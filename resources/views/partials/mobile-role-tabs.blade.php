@if(auth()->check())
    @php
        $user = auth()->user();
        $mobileTabs = [];

        if ($user->isClient()) {
            $cartCount = $user->cartItems()->count();
            $favoriteCount = $user->favorites()->count();

            $mobileTabs = [
                [
                    'label' => 'Accueil',
                    'icon' => 'fa-house',
                    'route' => 'home',
                    'route_is' => 'home',
                ],
                [
                    'label' => 'Boutique',
                    'icon' => 'fa-store',
                    'route' => 'products.index',
                    'route_is' => 'products.*',
                ],
                [
                    'label' => 'Favoris',
                    'icon' => 'fa-heart',
                    'route' => 'favorites.index',
                    'route_is' => 'favorites.*',
                    'badge' => $favoriteCount > 0 ? $favoriteCount : null,
                ],
                [
                    'label' => 'Panier',
                    'icon' => 'fa-shopping-cart',
                    'route' => 'cart.index',
                    'route_is' => 'cart.*',
                    'badge' => $cartCount > 0 ? $cartCount : null,
                ],
                [
                    'label' => 'Profil',
                    'icon' => 'fa-user',
                    'route' => 'profile.index',
                    'route_is' => 'profile.*',
                ],
            ];
        }
    @endphp

    @if(!empty($mobileTabs))
        <nav class="fixed bottom-0 inset-x-0 bg-white border-t border-gray-100 shadow-[0_-4px_12px_rgba(0,0,0,0.05)] md:hidden z-50">
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
@endif


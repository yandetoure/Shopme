@extends('layouts.app')

@section('title', 'Produits - ShopMe')

@section('content')
@php
    $minPrice = \App\Models\Product::min('price') ?? 0;
    $maxPrice = \App\Models\Product::max('price') ?? 1000000;
    $currentMinPrice = request('price_min', $minPrice);
    $currentMaxPrice = request('price_max', $maxPrice);
@endphp

<div class="container mx-auto px-4 py-4 md:py-8" x-data="{ 
    activeTab: 'products',
    showFilters: false,
    priceMin: {{ $currentMinPrice }},
    priceMax: {{ $currentMaxPrice }},
    minPrice: {{ $minPrice }},
    maxPrice: {{ $maxPrice }}
}">
    <!-- Barre de recherche mobile -->
    <div class="md:hidden mb-4">
        <form method="GET" action="{{ route('products.index') }}" class="relative">
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Rechercher un produit..." 
                   class="w-full pl-11 pr-3 py-2.5 text-sm bg-white border border-gray-200 rounded-lg focus:border-orange-400 focus:ring-1 focus:ring-orange-400 shadow-sm">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            @if(request()->hasAny(['category', 'sort', 'price_min', 'price_max', 'on_sale', 'in_stock', 'featured']))
                @if(request('category'))<input type="hidden" name="category" value="{{ request('category') }}">@endif
                @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
                @if(request('price_min'))<input type="hidden" name="price_min" value="{{ request('price_min') }}">@endif
                @if(request('price_max'))<input type="hidden" name="price_max" value="{{ request('price_max') }}">@endif
                @if(request('on_sale'))<input type="hidden" name="on_sale" value="1">@endif
                @if(request('in_stock'))<input type="hidden" name="in_stock" value="1">@endif
                @if(request('featured'))<input type="hidden" name="featured" value="1">@endif
            @endif
        </form>
    </div>

    <!-- Onglets mobile -->
    <div class="md:hidden mb-4 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex border-b border-gray-200">
            <button @click="activeTab = 'products'" 
                    :class="activeTab === 'products' ? 'border-b-2 border-orange-500 text-orange-600 font-semibold' : 'text-gray-600'"
                    class="flex-1 px-4 py-3 text-sm text-center transition-colors">
                <i class="fas fa-box mr-2"></i>Produits
            </button>
            <button @click="activeTab = 'filters'" 
                    :class="activeTab === 'filters' ? 'border-b-2 border-orange-500 text-orange-600 font-semibold' : 'text-gray-600'"
                    class="flex-1 px-4 py-3 text-sm text-center transition-colors relative">
                <i class="fas fa-filter mr-2"></i>Filtres
                @if(request()->hasAny(['category', 'sort', 'price_min', 'price_max', 'on_sale', 'in_stock', 'featured']))
                    <span class="absolute top-1 right-1 w-2 h-2 bg-orange-500 rounded-full"></span>
                @endif
        </button>
        </div>
    </div>

    <div class="flex flex-col md:flex-row gap-8">
        <!-- Filtres -->
        <aside class="w-full md:w-64 bg-white rounded-lg shadow-md p-4 h-fit sticky top-20"
               :class="{
                   'hidden md:block': activeTab === 'products',
                   'block': activeTab === 'filters'
               }">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-base">Filtres</h3>
                <button type="button"
                        class="text-xs text-gray-500 md:hidden"
                        @click="activeTab = 'products'">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form method="GET" action="{{ route('products.index') }}" id="filters-form">
                <!-- Recherche (desktop uniquement) -->
                <div class="mb-4 hidden md:block">
                    <label class="block text-xs font-medium mb-1.5">Rechercher</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nom du produit..." 
                           class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                </div>

                <!-- Catégorie -->
                @if(isset($categories) && $categories->count() > 0)
                <div class="mb-4">
                    <label class="block text-xs font-medium mb-1.5">Catégorie</label>
                    <select name="category" class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Filtre par prix -->
                <div class="mb-4">
                    <label class="block text-xs font-medium mb-2">Prix (FCFA)</label>
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <input type="number" name="price_min" 
                                   x-model="priceMin"
                                   :min="minPrice" 
                                   :max="maxPrice"
                                   placeholder="Min" 
                                   class="flex-1 px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <span class="text-gray-400">-</span>
                            <input type="number" name="price_max" 
                                   x-model="priceMax"
                                   :min="minPrice" 
                                   :max="maxPrice"
                                   placeholder="Max" 
                                   class="flex-1 px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        </div>
                        <div class="text-xs text-gray-500">
                            <span x-text="new Intl.NumberFormat('fr-FR').format(priceMin)"></span> - 
                            <span x-text="new Intl.NumberFormat('fr-FR').format(priceMax)"></span> FCFA
                        </div>
                    </div>
                </div>

                <!-- Options de filtrage -->
                <div class="mb-4 space-y-2">
                    <label class="block text-xs font-medium mb-2">Options</label>
                    
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <span class="text-sm text-gray-700">En promotion</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <span class="text-sm text-gray-700">En stock</span>
                    </label>
                    
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }}
                               class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <span class="text-sm text-gray-700">Produits vedettes</span>
                    </label>
                </div>

                <!-- Trier par -->
                <div class="mb-4">
                    <label class="block text-xs font-medium mb-1.5">Trier par</label>
                    <select name="sort" class="w-full px-3 py-2 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Plus populaires</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <button type="submit" class="w-full bg-orange-500 text-white px-3 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium transition">
                        <i class="fas fa-filter mr-2"></i>Appliquer les filtres
                </button>
                    <a href="{{ route('products.index') }}" class="block text-center text-xs text-gray-600 hover:text-orange-600 transition">
                        <i class="fas fa-redo mr-1"></i>Réinitialiser
                </a>
                </div>
            </form>
        </aside>

        <!-- Liste des produits -->
        <div class="flex-1"
             :class="{
                 'hidden md:block': activeTab === 'filters',
                 'block': activeTab === 'products'
             }">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl md:text-2xl font-bold">Produits</h1>
                @if(request()->hasAny(['search', 'category', 'sort', 'price_min', 'price_max', 'on_sale', 'in_stock', 'featured']))
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-600">Filtres actifs:</span>
                        @if(request('category'))
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">
                                Catégorie
                                <a href="{{ route('products.index', array_merge(request()->except('category'), ['page' => 1])) }}" class="ml-1">×</a>
                            </span>
                        @endif
                        @if(request('price_min') || request('price_max'))
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">
                                Prix
                                <a href="{{ route('products.index', array_merge(request()->except(['price_min', 'price_max']), ['page' => 1])) }}" class="ml-1">×</a>
                            </span>
                        @endif
                        @if(request('on_sale'))
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">
                                Promotion
                                <a href="{{ route('products.index', array_merge(request()->except('on_sale'), ['page' => 1])) }}" class="ml-1">×</a>
                            </span>
                        @endif
                        @if(request('in_stock'))
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">
                                En stock
                                <a href="{{ route('products.index', array_merge(request()->except('in_stock'), ['page' => 1])) }}" class="ml-1">×</a>
                            </span>
                        @endif
                        @if(request('featured'))
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded text-xs">
                                Vedette
                                <a href="{{ route('products.index', array_merge(request()->except('featured'), ['page' => 1])) }}" class="ml-1">×</a>
                            </span>
                        @endif
                    </div>
                @endif
            </div>
            
            @if($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-600 mb-2">Aucun produit trouvé.</p>
                    @if(request()->hasAny(['search', 'category', 'sort', 'price_min', 'price_max', 'on_sale', 'in_stock', 'featured']))
                        <a href="{{ route('products.index') }}" class="mt-4 inline-block text-orange-600 hover:text-orange-700 text-sm font-medium">
                            Réinitialiser les filtres
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

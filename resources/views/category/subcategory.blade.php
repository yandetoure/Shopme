@extends('layouts.app')

@section('title', ($category->parent ? $category->parent->name . ' - ' : '') . $category->name . ' - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <nav class="mb-4 text-xs text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-orange-600">Accueil</a>
        @if($category->parent)
            <span class="mx-1">/</span>
            <a href="{{ route('category.show', $category->parent->slug) }}" class="hover:text-orange-600">{{ $category->parent->name }}</a>
        @endif
        <span class="mx-1">/</span>
        <span class="text-gray-800 font-medium">{{ $category->name }}</span>
    </nav>

    <!-- Header de la sous-catégorie -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex items-center justify-between mb-3">
            @if($category->parent)
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs text-gray-500">Catégorie :</span>
                    <a href="{{ route('category.show', $category->parent->slug) }}" class="text-xs text-orange-600 hover:underline font-medium">
                        {{ $category->parent->name }}
                    </a>
                </div>
            @endif
        </div>
        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $category->name }}</h1>
        @if($category->description)
            <p class="text-sm text-gray-600">{{ $category->description }}</p>
        @endif
        @if($products->total() > 0)
            <p class="text-xs text-gray-500 mt-2">{{ $products->total() }} produit(s) disponible(s)</p>
        @endif
    </div>

    <!-- Retour à la catégorie parente -->
    @if($category->parent)
        <div class="mb-4">
            <a href="{{ route('category.show', $category->parent->slug) }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-orange-600">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Retour à {{ $category->parent->name }}</span>
            </a>
        </div>
    @endif

    <!-- Sous-catégories sœurs (si elles existent) -->
    @if($category->parent && $category->parent->children->count() > 1)
        <div class="mb-6">
            <h2 class="text-base font-semibold mb-3 text-gray-800">Autres sous-catégories</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($category->parent->children as $sibling)
                    @if($sibling->id !== $category->id)
                        <a href="{{ route('category.subcategory', [$category->parent->slug, $sibling->slug]) }}" 
                           class="px-3 py-1.5 text-xs bg-white border border-gray-200 rounded-lg hover:border-orange-500 hover:text-orange-600 hover:bg-orange-50 transition {{ $sibling->id === $category->id ? 'border-orange-500 text-orange-600 bg-orange-50' : '' }}">
                            {{ $sibling->name }}
                        </a>
                    @else
                        <span class="px-3 py-1.5 text-xs bg-orange-500 text-white border border-orange-500 rounded-lg font-medium">
                            {{ $sibling->name }}
                        </span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Produits -->
    <div>
        @if($products->count() > 0)
            <!-- Options de tri et filtre -->
            <div class="bg-white rounded-lg shadow-md p-3 mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-600">Trier par :</span>
                    <select class="text-xs border rounded px-2 py-1 focus:ring-2 focus:ring-orange-500" onchange="location.href=this.value">
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_low']) }}" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'price_high']) }}" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="{{ request()->fullUrlWithQuery(['sort' => 'popular']) }}" {{ request('sort') == 'popular' ? 'selected' : '' }}>Plus populaires</option>
                    </select>
                </div>
                <div class="text-xs text-gray-600">
                    Affichage de {{ $products->firstItem() }} à {{ $products->lastItem() }} sur {{ $products->total() }} résultats
                </div>
            </div>

            <!-- Grille de produits -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-box-open text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Aucun produit disponible</h3>
                <p class="text-sm text-gray-600 mb-4">Il n'y a pas encore de produits dans cette sous-catégorie.</p>
                @if($category->parent)
                    <a href="{{ route('category.show', $category->parent->slug) }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                        Voir la catégorie {{ $category->parent->name }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection


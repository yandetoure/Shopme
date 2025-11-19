@extends('layouts.app')

@section('title', 'Produits - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Filtres -->
        <aside class="w-full md:w-64 bg-white rounded-lg shadow-md p-6 h-fit">
            <h3 class="font-bold text-lg mb-4">Filtres</h3>
            
            <form method="GET" action="{{ route('products.index') }}">
                <!-- Recherche -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Rechercher</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Nom du produit..." 
                           class="w-full px-3 py-2 border rounded-lg">
                </div>

                <!-- Catégorie -->
                @if(isset($categories) && $categories->count() > 0)
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Catégorie</label>
                    <select name="category" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">Toutes</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <!-- Trier par -->
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Trier par</label>
                    <select name="sort" class="w-full px-3 py-2 border rounded-lg">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Plus récents</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Prix croissant</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Prix décroissant</option>
                        <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Plus populaires</option>
                    </select>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                    Appliquer
                </button>
                <a href="{{ route('products.index') }}" class="block text-center mt-2 text-gray-600 hover:text-indigo-600">
                    Réinitialiser
                </a>
            </form>
        </aside>

        <!-- Liste des produits -->
        <div class="flex-1">
            <h1 class="text-3xl font-bold mb-6">Produits</h1>
            
            @if($products->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        @include('partials.product-card', ['product' => $product])
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                    <p class="text-gray-600">Aucun produit trouvé.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

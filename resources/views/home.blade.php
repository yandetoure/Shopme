@extends('layouts.app')

@section('title', 'Accueil - ShopMe')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-orange-500 to-orange-600 text-white py-20">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Bienvenue sur ShopMe</h1>
            <p class="text-lg md:text-xl mb-6">Découvrez notre sélection de produits de qualité</p>
            <a href="{{ route('products.index') }}" class="bg-white text-orange-600 px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                Découvrir les produits
            </a>
        </div>
    </div>
</section>

<!-- Catégories -->
@if(isset($categories) && $categories->count() > 0)
<section class="py-12 container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">Catégories</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="group">
                <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-orange-100 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:bg-orange-500 transition">
                        <i class="fas fa-tag text-orange-600 group-hover:text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-orange-600 text-sm">{{ $category->name }}</h3>
                    @if($category->children->count() > 0)
                        <p class="text-xs text-gray-500 mt-1">{{ $category->children->count() }} sous-catégories</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

<!-- Produits en vedette -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section class="py-12 bg-white container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">Produits en vedette</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($featuredProducts as $product)
            @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
        @endforeach
    </div>
</section>
@endif

<!-- Produits en promotion -->
@if(isset($onSaleProducts) && $onSaleProducts->count() > 0)
<section class="py-12 container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">Promotions</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($onSaleProducts as $product)
            @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
        @endforeach
    </div>
</section>
@endif

<!-- Nouveaux produits -->
@if(isset($latestProducts) && $latestProducts->count() > 0)
<section class="py-12 bg-white container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">Nouveautés</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($latestProducts as $product)
            @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
        @endforeach
    </div>
</section>
@endif
@endsection

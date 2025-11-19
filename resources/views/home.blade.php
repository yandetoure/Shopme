@extends('layouts.app')

@section('title', 'Accueil - ShopMe')

@section('content')
<!-- Hero Section -->
<section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-20">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Bienvenue sur ShopMe</h1>
            <p class="text-xl md:text-2xl mb-8">Découvrez notre sélection de produits de qualité</p>
            <a href="{{ route('products.index') }}" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                Découvrir les produits
            </a>
        </div>
    </div>
</section>

<!-- Catégories -->
@if(isset($categories) && $categories->count() > 0)
<section class="py-12 container mx-auto px-4">
    <h2 class="text-3xl font-bold mb-8">Catégories</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="group">
                <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:bg-indigo-600 transition">
                        <i class="fas fa-tag text-indigo-600 group-hover:text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600">{{ $category->name }}</h3>
                    @if($category->children->count() > 0)
                        <p class="text-sm text-gray-500 mt-2">{{ $category->children->count() }} sous-catégories</p>
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
    <h2 class="text-3xl font-bold mb-8">Produits en vedette</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($featuredProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

<!-- Produits en promotion -->
@if(isset($onSaleProducts) && $onSaleProducts->count() > 0)
<section class="py-12 container mx-auto px-4">
    <h2 class="text-3xl font-bold mb-8">Promotions</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($onSaleProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif

<!-- Nouveaux produits -->
@if(isset($latestProducts) && $latestProducts->count() > 0)
<section class="py-12 bg-white container mx-auto px-4">
    <h2 class="text-3xl font-bold mb-8">Nouveautés</h2>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        @foreach($latestProducts as $product)
            @include('partials.product-card', ['product' => $product])
        @endforeach
    </div>
</section>
@endif
@endsection

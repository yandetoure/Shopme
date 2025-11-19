@extends('layouts.app')

@section('title', $category->name . ' - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-indigo-600">Accueil</a>
        @if($category->parent)
            <span class="mx-2">/</span>
            <a href="{{ route('category.show', $category->parent->slug) }}" class="hover:text-indigo-600">{{ $category->parent->name }}</a>
        @endif
        <span class="mx-2">/</span>
        <span class="text-gray-800">{{ $category->name }}</span>
    </nav>

    <h1 class="text-2xl font-bold mb-4">{{ $category->name }}</h1>

    @if($category->description)
        <p class="text-gray-700 mb-8">{{ $category->description }}</p>
    @endif

    <!-- Sous-catégories -->
    @if($category->children->count() > 0)
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Sous-catégories</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($category->children as $child)
                    <a href="{{ route('category.subcategory', [$category->slug, $child->slug]) }}" 
                       class="bg-white rounded-lg shadow-md p-4 text-center hover:shadow-xl transition">
                        <h3 class="font-semibold text-gray-800 hover:text-indigo-600">{{ $child->name }}</h3>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Produits -->
    <div>
        <h2 class="text-lg font-semibold mb-3">Produits</h2>
        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
                <p class="text-gray-600">Aucun produit dans cette catégorie pour le moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection

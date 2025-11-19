@extends('layouts.app')

@section('title', $category->name . ' - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-orange-600">Accueil</a>
        @if($category->parent)
            <span class="mx-2">/</span>
            <a href="{{ route('category.show', $category->parent->slug) }}" class="hover:text-orange-600">{{ $category->parent->name }}</a>
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
        <div class="mb-6 bg-white rounded-lg shadow-md p-4">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Sous-catégories</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3">
                @foreach($category->children as $child)
                    <a href="{{ route('category.subcategory', [$category->slug, $child->slug]) }}" 
                       class="bg-gray-50 rounded-lg p-3 text-center hover:bg-orange-50 hover:shadow-md transition border border-transparent hover:border-orange-200 group">
                        <div class="w-12 h-12 bg-orange-100 rounded-full mx-auto mb-2 flex items-center justify-center group-hover:bg-orange-500 transition">
                            <i class="fas fa-tag text-orange-600 group-hover:text-white text-sm"></i>
                        </div>
                        <h3 class="font-medium text-gray-800 group-hover:text-orange-600 text-xs leading-tight">{{ $child->name }}</h3>
                        @php
                            $childProductCount = \App\Models\Product::whereHas('categories', function($q) use ($child) {
                                $q->where('categories.id', $child->id);
                            })->active()->count();
                        @endphp
                        @if($childProductCount > 0)
                            <p class="text-xs text-gray-500 mt-1">{{ $childProductCount }} produit(s)</p>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Produits -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Produits</h2>
            @if($products->total() > 0)
                <span class="text-xs text-gray-500">{{ $products->total() }} produit(s)</span>
            @endif
        </div>
        
        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($products as $product)
                    @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-box-open text-gray-400 text-5xl mb-4"></i>
                <p class="text-sm text-gray-600">Aucun produit dans cette catégorie pour le moment.</p>
            </div>
        @endif
    </div>
</div>
@endsection

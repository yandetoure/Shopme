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
        <div class="mb-4 bg-white rounded-lg shadow-md p-3">
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold text-gray-600 whitespace-nowrap">Sous-catégories:</span>
                @foreach($category->children as $child)
                    @php
                        $childProductCount = \App\Models\Product::whereHas('categories', function($q) use ($child) {
                            $q->where('categories.id', $child->id);
                        })->active()->count();
                    @endphp
                    <a href="{{ route('category.subcategory', [$category->slug, $child->slug]) }}" 
                       class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs bg-gray-50 hover:bg-orange-50 hover:text-orange-600 border border-transparent hover:border-orange-200 transition">
                        <span>{{ $child->name }}</span>
                        @if($childProductCount > 0)
                            <span class="text-xs text-gray-500">({{ $childProductCount }})</span>
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

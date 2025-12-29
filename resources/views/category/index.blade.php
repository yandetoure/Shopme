@extends('layouts.app')

@section('title', 'Catégories - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="mb-6 text-sm text-gray-600">
        <a href="{{ route('home') }}" class="hover:text-orange-600">Accueil</a>
        <span class="mx-2">/</span>
        <span class="text-gray-800">Catégories</span>
    </nav>

    <h1 class="text-2xl font-bold mb-6">Toutes les catégories</h1>

    @if($categories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $category)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <a href="{{ route('category.show', $category->slug) }}" class="block">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-tag text-orange-600 text-2xl"></i>
                            </div>
                            <div class="flex-1">
                                <h2 class="text-lg font-bold text-gray-800">{{ $category->name }}</h2>
                                @if($category->description)
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $category->description }}</p>
                                @endif
                            </div>
                        </div>
                    </a>

                    @if($category->children->count() > 0)
                        <div class="border-t border-gray-100 pt-4 mt-4">
                            <p class="text-xs font-semibold text-gray-600 mb-2">Sous-catégories:</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($category->children as $child)
                                    @php
                                        $childProductCount = \App\Models\Product::whereHas('categories', function($q) use ($child) {
                                            $q->where('categories.id', $child->id);
                                        })->active()->count();
                                    @endphp
                                    <a href="{{ route('category.subcategory', [$category->slug, $child->slug]) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs bg-gray-50 hover:bg-orange-50 hover:text-orange-600 border border-gray-100 hover:border-orange-200 transition">
                                        <span>{{ $child->name }}</span>
                                        @if($childProductCount > 0)
                                            <span class="text-xs text-gray-500">({{ $childProductCount }})</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg shadow-md">
            <i class="fas fa-tags text-gray-400 text-5xl mb-4"></i>
            <p class="text-gray-600">Aucune catégorie disponible pour le moment.</p>
        </div>
    @endif
</div>
@endsection


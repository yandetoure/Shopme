@extends('layouts.app')

@section('title', $product->name . ' - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Image du produit -->
        <div>
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/600x600?text=' . urlencode($product->name) }}" 
                 alt="{{ $product->name }}" 
                 class="w-full rounded-lg shadow-md">
        </div>

        <!-- Informations du produit -->
        <div>
            <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
            
            @if($product->category)
                <a href="{{ route('category.show', $product->category->slug) }}" class="text-indigo-600 hover:underline mb-4 block">
                    {{ $product->category->name }}
                </a>
            @endif

            <!-- Prix -->
            <div class="mb-6">
                @if($product->is_on_sale)
                    <div class="flex items-center gap-4">
                        <span class="text-3xl font-bold text-indigo-600">{{ number_format($product->sale_price, 2) }} €</span>
                        <span class="text-xl text-gray-400 line-through">{{ number_format($product->price, 2) }} €</span>
                        <span class="bg-red-500 text-white px-3 py-1 rounded text-sm font-semibold">
                            -{{ $product->discount_percentage }}%
                        </span>
                    </div>
                @else
                    <span class="text-3xl font-bold text-indigo-600">{{ number_format($product->price, 2) }} €</span>
                @endif
            </div>

            <!-- Description -->
            @if($product->short_description)
                <p class="text-gray-700 mb-6">{{ $product->short_description }}</p>
            @endif

            <!-- Stock -->
            <div class="mb-6">
                @if($product->in_stock)
                    <span class="text-green-600 font-semibold">✓ En stock ({{ $product->stock_quantity }} disponibles)</span>
                @else
                    <span class="text-red-600 font-semibold">✗ Rupture de stock</span>
                @endif
            </div>

            <!-- Formulaire d'ajout au panier -->
            @auth
                @if($product->in_stock && $product->status === 'active')
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="flex items-center gap-4 mb-4">
                            <label for="quantity" class="font-semibold">Quantité:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock_quantity }}" 
                                   class="w-20 px-3 py-2 border rounded-lg">
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold">
                            <i class="fas fa-shopping-cart mr-2"></i>Ajouter au panier
                        </button>
                    </form>
                @endif
            @else
                <div class="mb-6">
                    <a href="{{ route('login') }}" class="block w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold text-center">
                        Connectez-vous pour acheter
                    </a>
                </div>
            @endauth

            <!-- Description détaillée -->
            @if($product->description)
                <div class="border-t pt-6">
                    <h3 class="font-bold text-lg mb-4">Description</h3>
                    <p class="text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Produits similaires -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Produits similaires</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    @include('partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

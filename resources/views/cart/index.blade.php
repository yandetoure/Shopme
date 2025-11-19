@extends('layouts.app')

@section('title', 'Panier - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Mon Panier</h1>

    @if($cartItems->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Liste des articles -->
            <div class="md:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-lg shadow-md p-4 flex flex-col md:flex-row gap-4">
                        <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/150?text=' . urlencode($item->product->name) }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-full md:w-24 h-24 object-cover rounded">
                        
                        <div class="flex-1">
                            <a href="{{ route('products.show', $item->product->slug) }}" class="font-semibold text-gray-800 hover:text-indigo-600">
                                {{ $item->product->name }}
                            </a>
                            <p class="text-indigo-600 font-bold mt-2">{{ number_format($item->price, 0, ',', ' ') }} FCFA</p>
                            
                            <div class="mt-4 flex items-center gap-4">
                                <form action="{{ route('cart.update', $item->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock_quantity }}" 
                                           class="w-20 px-2 py-1 border rounded" onchange="this.form.submit()">
                                </form>
                                
                                <span class="font-semibold">{{ number_format($item->total, 0, ',', ' ') }} FCFA</span>
                                
                                <form action="{{ route('cart.remove', $item->id) }}" method="POST" class="ml-auto">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Résumé -->
            <div class="bg-white rounded-lg shadow-md p-6 h-fit sticky top-20">
                <h2 class="text-xl font-bold mb-4">Résumé</h2>
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span>Sous-total</span>
                        <span>{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Livraison</span>
                        <span>6 550 FCFA</span>
                    </div>
                    <div class="border-t pt-2 flex justify-between font-bold text-lg">
                        <span>Total</span>
                        <span class="text-indigo-600">{{ number_format($total + 6550, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
                
                <a href="{{ route('cart.checkout') }}" class="block w-full bg-indigo-600 text-white text-center px-6 py-3 rounded-lg hover:bg-indigo-700 font-semibold mb-2">
                    Passer la commande
                </a>
                
                <a href="{{ route('products.index') }}" class="block text-center text-gray-600 hover:text-indigo-600">
                    Continuer les achats
                </a>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-shopping-cart text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-600 mb-4">Votre panier est vide.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Découvrir les produits
            </a>
        </div>
    @endif
</div>
@endsection

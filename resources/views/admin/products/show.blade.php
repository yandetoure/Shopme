@extends('dashboard.layout')

@section('title', 'Détails du Produit - ShopMe')
@section('page-title', 'Détails du Produit')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
            <p class="text-gray-600">Détails du produit</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-2"></i> Modifier
            </a>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Image -->
            <div>
                @if($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                @else
                    <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-4xl"></i>
                    </div>
                @endif
            </div>

            <!-- Informations -->
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500">Nom</p>
                    <p class="text-lg font-semibold text-gray-800">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">SKU</p>
                    <p class="text-lg text-gray-800">{{ $product->sku ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Prix</p>
                    <p class="text-2xl font-bold text-gray-800">
                        @if($product->is_on_sale && $product->sale_price)
                            <span class="text-red-600">{{ number_format($product->sale_price, 2) }} €</span>
                            <span class="text-gray-400 line-through text-lg ml-2">{{ number_format($product->price, 2) }} €</span>
                        @else
                            {{ number_format($product->price, 2) }} €
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Stock</p>
                    <p class="text-lg text-gray-800">
                        <span class="px-3 py-1 rounded-full {{ $product->in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock_quantity }} unités
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Statut</p>
                    <p class="text-lg">
                        <span class="px-3 py-1 rounded-full {{ $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->status == 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Description et détails -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Description</h3>
            <p class="text-gray-600">{{ $product->description ?? 'Aucune description' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Informations</h3>
            <div class="space-y-2">
                <p><span class="font-semibold">Catégories:</span> 
                    @forelse($product->categories as $category)
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">{{ $category->name }}</span>
                    @empty
                        Aucune catégorie
                    @endforelse
                </p>
                <p><span class="font-semibold">Vues:</span> {{ $product->views }}</p>
                <p><span class="font-semibold">Ventes:</span> {{ $product->sales_count }}</p>
                <p><span class="font-semibold">Créé le:</span> {{ $product->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection


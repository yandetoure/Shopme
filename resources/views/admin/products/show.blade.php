@extends('dashboard.layout')

@section('title', 'Détails du Produit - ShopMe')
@section('page-title', 'Détails du Produit')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <!-- En-tête -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h2>
            <p class="text-sm text-gray-600">Détails du produit</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.products.edit', $product) }}" class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <a href="{{ route('admin.products.index') }}" class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div class="bg-white rounded-lg shadow p-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Images -->
            <div>
                @php
                    $allImages = [];
                    if ($product->image) {
                        $allImages[] = $product->image;
                    }
                    if ($product->images && is_array($product->images)) {
                        $allImages = array_merge($allImages, $product->images);
                    }
                @endphp
                
                @if(count($allImages) > 0)
                    <div x-data="{ currentImage: 0, images: {{ json_encode($allImages) }} }" class="space-y-4">
                        <!-- Image principale (grande) -->
                        <div class="relative bg-gray-100 rounded-lg overflow-hidden" style="height: 500px; display: flex; align-items: center; justify-content: center;">
                            <img src="{{ asset('storage/' . $allImages[0]) }}" 
                                 alt="{{ $product->name }}" 
                                 style="max-width: 100%; max-height: 100%; width: auto; height: auto; object-fit: contain;"
                                 x-bind:src="'{{ url('storage') }}/' + images[currentImage]"
                                 x-bind:alt="'{{ $product->name }} - Image ' + (currentImage + 1)">
                            @if(count($allImages) > 1)
                                <!-- Boutons navigation -->
                                <button @click="currentImage = (currentImage - 1 + {{ count($allImages) }}) % {{ count($allImages) }}"
                                        class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition">
                                    <i class="fas fa-chevron-left text-gray-700"></i>
                                </button>
                                <button @click="currentImage = (currentImage + 1) % {{ count($allImages) }}"
                                        class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white/90 hover:bg-white rounded-full p-2 shadow-lg transition">
                                    <i class="fas fa-chevron-right text-gray-700"></i>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Miniatures (petites) -->
                        @if(count($allImages) > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($allImages as $index => $imagePath)
                            <button @click="currentImage = {{ $index }}"
                                    :class="currentImage === {{ $index }} ? 'ring-2 ring-orange-500 border-orange-500' : 'border-2 border-gray-200 hover:border-orange-300'"
                                    class="overflow-hidden rounded-lg transition">
                                <img src="{{ asset('storage/' . $imagePath) }}" 
                                     alt="Image {{ $index + 1 }}" 
                                     class="w-full h-20 object-cover">
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @else
                    <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                    </div>
                @endif
            </div>

            <!-- Informations -->
            <div class="space-y-3">
                <div>
                    <p class="text-xs text-gray-500">Nom</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $product->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">SKU</p>
                    <p class="text-sm text-gray-800">{{ $product->sku ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Prix</p>
                    <p class="text-xl font-bold text-gray-800">
                        @if($product->is_on_sale && $product->sale_price)
                            <span class="text-red-600">{{ number_format($product->sale_price, 0, '', '.') }} FCFA</span>
                            <span class="text-gray-400 line-through text-sm ml-2">{{ number_format($product->price, 0, '', '.') }} FCFA</span>
                        @else
                            {{ number_format($product->price, 0, '', '.') }} FCFA
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Stock</p>
                    <p class="text-sm text-gray-800">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $product->in_stock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $product->stock_quantity }} unités
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Prix fournisseur</p>
                    <p class="text-sm font-semibold text-gray-800">
                        {{ $product->purchase_price ? number_format($product->purchase_price, 0, '', '.') . ' FCFA' : 'Non renseigné' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Fournisseur</p>
                    <p class="text-sm text-gray-800">{{ $product->supplier_name ?? 'Non renseigné' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Conditionnement</p>
                    <p class="text-sm text-gray-800">
                        {{ $product->sale_unit === 'dozen' ? 'Douzaine' : 'Unité' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Statut</p>
                    <p class="text-sm">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $product->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $product->status == 'active' ? 'Actif' : 'Inactif' }}
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Description et détails -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Description</h3>
            <p class="text-sm text-gray-600">{{ $product->description ?? 'Aucune description' }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Informations</h3>
            <div class="space-y-2 text-sm">
                <p><span class="font-semibold">Catégories:</span> 
                    @forelse($product->categories as $category)
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs">{{ $category->name }}</span>
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

    <!-- Attributs/Variables -->
    @if($product->productAttributes && $product->productAttributes->count() > 0)
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-4">Attributs/Variables</h3>
        <div class="space-y-4">
            @foreach($product->productAttributes as $attribute)
            <div class="border rounded-lg p-3">
                <h4 class="text-xs font-semibold text-gray-700 mb-2">{{ $attribute->name }}</h4>
                <div class="flex flex-wrap gap-2">
                    @foreach($attribute->values as $value)
                    <div class="flex items-center space-x-2 px-3 py-1.5 bg-gray-50 rounded-lg border">
                        @if($value->color_code)
                            <div class="w-5 h-5 rounded-full border border-gray-300 flex-shrink-0" style="background-color: {{ $value->color_code }}"></div>
                        @endif
                        @if($value->image)
                            <img src="{{ asset('storage/' . $value->image) }}" alt="{{ $value->value }}" class="w-5 h-5 object-cover rounded flex-shrink-0">
                        @endif
                        <span class="text-xs text-gray-700">{{ $value->value }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection


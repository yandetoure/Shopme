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
            <h1 class="text-2xl font-bold mb-3">{{ $product->name }}</h1>
            
            @if($product->category)
                <a href="{{ route('category.show', $product->category->slug) }}" class="text-orange-600 hover:underline mb-3 block text-sm">
                    {{ $product->category->name }}
                </a>
            @endif

            <!-- Prix (mis à jour dynamiquement selon les attributs sélectionnés) -->
            <div class="mb-4" id="price-display">
                @if($product->is_on_sale)
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-bold text-orange-600" id="current-price">{{ number_format($product->sale_price ?? $product->price, 0, ',', ' ') }} FCFA</span>
                        <span class="text-lg text-gray-400 line-through" id="original-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                        <span class="bg-red-500 text-white px-2 py-1 rounded text-xs font-semibold" id="discount-badge">
                            -{{ $product->discount_percentage }}%
                        </span>
                    </div>
                @else
                    <span class="text-2xl font-bold text-orange-600" id="current-price">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>

            <!-- Attributs (Couleur, Taille, etc.) -->
            @if($product->productAttributes->count() > 0)
                <div class="mb-4 space-y-4" id="product-attributes" 
                     x-data="{
                         selectedAttributes: {},
                         selectedVariation: null,
                         updatePrice() {
                             if (Object.keys(this.selectedAttributes).length === {{ $product->productAttributes->count() }}) {
                                 @foreach($product->variations as $variation)
                                     if (JSON.stringify(this.selectedAttributes) === JSON.stringify({{ json_encode($variation->attributes) }})) {
                                         this.selectedVariation = {{ $variation->id }};
                                         const price = {{ $variation->display_price ?? $product->price }};
                                         const originalPrice = {{ $variation->price ?? $product->price }};
                                         document.getElementById('current-price').textContent = price.toLocaleString('fr-FR') + ' FCFA';
                                         if (originalPrice > price) {
                                             document.getElementById('original-price').textContent = originalPrice.toLocaleString('fr-FR') + ' FCFA';
                                             document.getElementById('original-price').classList.remove('hidden');
                                             const discount = Math.round(((originalPrice - price) / originalPrice) * 100);
                                             document.getElementById('discount-badge').textContent = '-' + discount + '%';
                                             document.getElementById('discount-badge').classList.remove('hidden');
                                         } else {
                                             document.getElementById('original-price').classList.add('hidden');
                                             document.getElementById('discount-badge').classList.add('hidden');
                                         }
                                         return;
                                     }
                                 @endforeach
                             }
                             this.selectedVariation = null;
                             const price = {{ $product->display_price }};
                             const originalPrice = {{ $product->price }};
                             document.getElementById('current-price').textContent = price.toLocaleString('fr-FR') + ' FCFA';
                             @if($product->is_on_sale)
                                 document.getElementById('original-price').textContent = originalPrice.toLocaleString('fr-FR') + ' FCFA';
                                 document.getElementById('original-price').classList.remove('hidden');
                                 document.getElementById('discount-badge').textContent = '-{{ $product->discount_percentage }}%';
                                 document.getElementById('discount-badge').classList.remove('hidden');
                             @else
                                 document.getElementById('original-price').classList.add('hidden');
                                 document.getElementById('discount-badge').classList.add('hidden');
                             @endif
                         }
                     }">
                    @foreach($product->productAttributes as $attribute)
                        <div>
                            <label class="block font-semibold text-sm mb-2">
                                {{ $attribute->name }}: 
                                <span class="text-orange-600 font-normal" x-show="selectedAttributes['{{ $attribute->slug }}']" x-text="selectedAttributes['{{ $attribute->slug }}']"></span>
                            </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($attribute->values as $value)
                                    @if($attribute->slug === 'couleur' && $value->color_code)
                                        <!-- Sélecteur de couleur -->
                                        <button type="button"
                                                @click="selectedAttributes['{{ $attribute->slug }}'] = '{{ $value->value }}'; updatePrice();"
                                                :class="selectedAttributes['{{ $attribute->slug }}'] === '{{ $value->value }}' ? 'ring-2 ring-orange-500 ring-offset-2' : ''"
                                                class="w-10 h-10 rounded-full border-2 border-gray-300 hover:border-orange-500 transition"
                                                style="background-color: {{ $value->color_code }};"
                                                title="{{ $value->value }}">
                                        </button>
                                    @else
                                        <!-- Sélecteur de taille ou autre attribut -->
                                        <button type="button"
                                                @click="selectedAttributes['{{ $attribute->slug }}'] = '{{ $value->value }}'; updatePrice();"
                                                :class="selectedAttributes['{{ $attribute->slug }}'] === '{{ $value->value }}' ? 'bg-orange-500 text-white border-orange-500' : 'bg-white text-gray-700 border-gray-300 hover:border-orange-500'"
                                                class="px-4 py-2 border-2 rounded-lg text-sm font-medium transition">
                                            {{ $value->value }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    <input type="hidden" name="selected_attributes" :value="JSON.stringify(selectedAttributes)">
                    <input type="hidden" name="variation_id" :value="selectedVariation">
                </div>
            @endif

            <!-- Description -->
            @if($product->short_description)
                <p class="text-gray-700 mb-4 text-sm">{{ $product->short_description }}</p>
            @endif

            <!-- Stock -->
            <div class="mb-4">
                @if($product->in_stock)
                    <span class="text-green-600 font-medium text-sm">✓ En stock ({{ $product->stock_quantity }} disponibles)</span>
                @else
                    <span class="text-red-600 font-medium text-sm">✗ Rupture de stock</span>
                @endif
            </div>

            <!-- Actions -->
            @auth
                <div class="mb-4 space-y-3">
                    <!-- Bouton Favoris -->
                    <button onclick="toggleFavorite({{ $product->id }}, this)" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-2 rounded-lg border-2 {{ $isFavorite ? 'bg-red-50 border-red-500 text-red-600' : 'bg-gray-50 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-600' }} transition text-sm font-medium"
                            data-product-id="{{ $product->id }}">
                        <i class="fas fa-heart text-sm {{ $isFavorite ? 'text-red-500' : '' }}"></i>
                        <span class="text-sm">{{ $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris' }}</span>
                    </button>

                    @if($product->in_stock && $product->status === 'active')
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" id="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="variation_id" :value="selectedVariation ?? ''">
                            <input type="hidden" name="selected_attributes" :value="JSON.stringify(selectedAttributes)">
                            <div class="flex items-center gap-3 mb-3">
                                <label for="quantity" class="font-medium text-sm">Quantité:</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" 
                                       :max="selectedVariation ? {{ $product->variations->first()->stock_quantity ?? $product->stock_quantity }} : {{ $product->stock_quantity }}" 
                                       class="w-16 px-2 py-1.5 border rounded-lg text-sm">
                            </div>
                            <button type="submit" 
                                    :disabled="Object.keys(selectedAttributes).length > 0 && Object.keys(selectedAttributes).length < {{ $product->productAttributes->count() }}"
                                    class="w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 disabled:bg-gray-300 disabled:cursor-not-allowed text-sm font-medium flex items-center justify-center gap-2">
                                <i class="fas fa-shopping-cart text-sm"></i>
                                <span class="text-sm">Ajouter au panier</span>
                            </button>
                            <p class="text-xs text-red-500 mt-2" x-show="Object.keys(selectedAttributes).length > 0 && Object.keys(selectedAttributes).length < {{ $product->productAttributes->count() }}">
                                Veuillez sélectionner tous les attributs
                            </p>
                        </form>
                    @endif
                </div>
            @else
                <div class="mb-4">
                    <a href="{{ route('login') }}" class="block w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium text-center">
                        <i class="fas fa-shopping-cart mr-2 text-sm"></i>Connectez-vous pour acheter
                    </a>
                </div>
            @endauth

            <!-- Description détaillée -->
            @if($product->description)
                <div class="border-t pt-4">
                    <h3 class="font-bold text-base mb-3">Description</h3>
                    <p class="text-gray-700 whitespace-pre-line text-sm">{{ $product->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Produits similaires -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="mt-8">
            <h2 class="text-xl font-bold mb-4">Produits similaires</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @foreach($relatedProducts as $relatedProduct)
                    @include('partials.product-card', ['product' => $relatedProduct, 'favoriteIds' => Auth::check() ? Auth::user()->favorites()->pluck('product_id')->toArray() : []])
                @endforeach
            </div>
        </div>
    @endif
</div>

@auth
<script>
function toggleFavorite(productId, button) {
    fetch(`/favorites/toggle/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = button.querySelector('i');
            const span = button.querySelector('span');
            if (data.isFavorite) {
                button.classList.remove('bg-gray-50', 'border-gray-300', 'text-gray-700');
                button.classList.add('bg-red-50', 'border-red-500', 'text-red-600');
                icon.classList.add('text-red-500');
                span.textContent = 'Retirer des favoris';
            } else {
                button.classList.remove('bg-red-50', 'border-red-500', 'text-red-600');
                button.classList.add('bg-gray-50', 'border-gray-300', 'text-gray-700');
                icon.classList.remove('text-red-500');
                span.textContent = 'Ajouter aux favoris';
            }
        }
    });
}
</script>
@endauth
@endsection
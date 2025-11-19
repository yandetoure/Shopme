<div class="group relative bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
    <div class="relative">
        <a href="{{ route('products.show', $product->slug) }}">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300?text=' . urlencode($product->name) }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
        </a>
                @if($product->is_on_sale)
            <span class="absolute top-2 left-2 bg-red-500 text-white px-1.5 py-0.5 rounded text-xs font-semibold">
                -{{ $product->discount_percentage }}%
            </span>
        @endif
        @if($product->featured)
            <span class="absolute top-2 right-2 bg-yellow-400 text-black px-1.5 py-0.5 rounded text-xs font-semibold">
                ⭐ Vedette
            </span>
        @endif
    </div>
    <div class="p-3">
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="font-semibold text-gray-800 group-hover:text-orange-600 mb-1.5 line-clamp-2 text-sm">{{ $product->name }}</h3>
        </a>
        <div class="flex items-center justify-between mb-2">
            <div>
                @if($product->is_on_sale)
                    <span class="text-orange-600 font-bold text-base">{{ number_format($product->sale_price, 0, ',', ' ') }} FCFA</span>
                    <span class="text-gray-400 line-through text-xs ml-1">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @else
                    <span class="text-orange-600 font-bold text-base">{{ number_format($product->price, 0, ',', ' ') }} FCFA</span>
                @endif
            </div>
            @if(!$product->in_stock)
                <span class="text-red-500 text-xs">Rupture</span>
            @endif
        </div>

        <!-- Boutons d'action -->
        <div class="flex items-center gap-2">
            @auth
                <!-- Bouton Favoris -->
                @php
                    $isFavorite = isset($favoriteIds) && in_array($product->id, $favoriteIds);
                @endphp
                <button onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite({{ $product->id }}, this)" 
                        class="favorite-btn flex-1 flex items-center justify-center gap-1 px-2 py-1.5 rounded-lg border-2 transition text-sm font-medium {{ $isFavorite ? 'bg-red-50 border-red-500 text-red-600' : 'bg-gray-50 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-600' }}"
                        data-product-id="{{ $product->id }}">
                    <i class="fas fa-heart text-xs {{ $isFavorite ? 'text-red-500' : '' }}"></i>
                    <span class="hidden sm:inline text-xs">{{ $isFavorite ? 'Favori' : 'Favoris' }}</span>
                </button>

                <!-- Bouton Ajouter au panier -->
                @if($product->in_stock && $product->status === 'active')
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-1" onsubmit="event.stopPropagation(); return true;">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" onclick="event.stopPropagation();" class="w-full bg-orange-500 text-white px-2 py-1.5 rounded-lg hover:bg-orange-600 transition text-sm font-medium flex items-center justify-center gap-1">
                            <i class="fas fa-shopping-cart text-xs"></i>
                            <span class="hidden sm:inline text-xs">Ajouter</span>
                        </button>
                    </form>
                @else
                    <button disabled class="flex-1 bg-gray-300 text-gray-500 px-2 py-1.5 rounded-lg cursor-not-allowed text-sm font-medium">
                        Indispo
                    </button>
                @endif
            @else
                <!-- Bouton pour non connectés -->
                <a href="{{ route('login') }}" onclick="event.stopPropagation();" class="flex-1 block bg-orange-500 text-white px-2 py-1.5 rounded-lg hover:bg-orange-600 transition text-sm font-medium text-center">
                    <i class="fas fa-shopping-cart text-xs mr-1"></i><span class="text-xs">Ajouter</span>
                </a>
            @endauth
        </div>
    </div>
</div>

@auth
<script>
if (typeof toggleFavorite === 'undefined') {
    window.toggleFavorite = function(productId, button) {
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
                    if (span) span.textContent = 'Favori';
                } else {
                    button.classList.remove('bg-red-50', 'border-red-500', 'text-red-600');
                    button.classList.add('bg-gray-50', 'border-gray-300', 'text-gray-700');
                    icon.classList.remove('text-red-500');
                    if (span) span.textContent = 'Favoris';
                }
            }
        })
        .catch(error => console.error('Error:', error));
    };
}
</script>
@endauth
<div class="group relative bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
    <div class="relative">
        <a href="{{ route('products.show', $product->slug) }}">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300?text=' . urlencode($product->name) }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
        </a>
        @if($product->is_on_sale)
            <span class="absolute top-2 left-2 bg-red-500 text-white px-2 py-1 rounded text-sm font-semibold">
                -{{ $product->discount_percentage }}%
            </span>
        @endif
        @if($product->featured)
            <span class="absolute top-2 right-2 bg-yellow-400 text-black px-2 py-1 rounded text-sm font-semibold">
                ⭐ Vedette
            </span>
        @endif

        <!-- Bouton Favoris (visible au hover sur l'image) -->
        @auth
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity z-10">
                @php
                    $isFavorite = isset($favoriteIds) && in_array($product->id, $favoriteIds);
                @endphp
                <button onclick="event.preventDefault(); event.stopPropagation(); toggleFavorite({{ $product->id }}, this)" 
                        class="favorite-btn bg-white rounded-full p-2 shadow-lg hover:bg-red-50 transition"
                        data-product-id="{{ $product->id }}">
                    <i class="fas fa-heart {{ $isFavorite ? 'text-red-500' : 'text-gray-400' }}"></i>
                </button>
            </div>
        @endauth
    </div>
    <div class="p-4">
        <a href="{{ route('products.show', $product->slug) }}">
            <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 mb-2 line-clamp-2">{{ $product->name }}</h3>
        </a>
        <div class="flex items-center justify-between mb-3">
            <div>
                @if($product->is_on_sale)
                    <span class="text-indigo-600 font-bold text-lg">{{ number_format($product->sale_price, 2) }} €</span>
                    <span class="text-gray-400 line-through text-sm ml-2">{{ number_format($product->price, 2) }} €</span>
                @else
                    <span class="text-indigo-600 font-bold text-lg">{{ number_format($product->price, 2) }} €</span>
                @endif
            </div>
            @if(!$product->in_stock)
                <span class="text-red-500 text-sm">Rupture de stock</span>
            @endif
        </div>

        <!-- Bouton Ajouter au panier -->
        @auth
            @if($product->in_stock && $product->status === 'active')
                <form action="{{ route('cart.add', $product->id) }}" method="POST" onsubmit="event.stopPropagation(); return true;">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" onclick="event.stopPropagation();" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold flex items-center justify-center gap-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Ajouter au panier</span>
                    </button>
                </form>
            @else
                <button disabled class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-lg cursor-not-allowed font-semibold">
                    Indisponible
                </button>
            @endif
        @else
            <a href="{{ route('login') }}" onclick="event.stopPropagation();" class="block w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold text-center">
                <i class="fas fa-shopping-cart mr-2"></i>Ajouter au panier
            </a>
        @endauth
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
                if (data.isFavorite) {
                    icon.classList.remove('text-gray-400');
                    icon.classList.add('text-red-500');
                } else {
                    icon.classList.remove('text-red-500');
                    icon.classList.add('text-gray-400');
                }
            }
        })
        .catch(error => console.error('Error:', error));
    };
}
</script>
@endauth
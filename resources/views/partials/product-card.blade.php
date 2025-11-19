<a href="{{ route('products.show', $product->slug) }}" class="group">
    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
        <div class="relative">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://via.placeholder.com/300x300?text=' . urlencode($product->name) }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
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
        </div>
        <div class="p-4">
            <h3 class="font-semibold text-gray-800 group-hover:text-indigo-600 mb-2 line-clamp-2">{{ $product->name }}</h3>
            <div class="flex items-center justify-between">
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
        </div>
    </div>
</a>

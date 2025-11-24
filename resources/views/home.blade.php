@extends('layouts.app')

@section('title', 'Accueil - ShopMe')

@section('content')
<!-- Hero Section Carousel -->
@if(isset($heroProducts) && $heroProducts->count() > 0)
<section class="bg-black text-white relative min-h-[350px] md:min-h-[600px]" 
         style="overflow: hidden; position: relative; isolation: isolate;"
         x-data="{
             currentSlide: 0,
             slides: {{ $heroProducts->count() }},
             next() {
                 this.currentSlide = (this.currentSlide + 1) % this.slides;
             },
             prev() {
                 this.currentSlide = (this.currentSlide - 1 + this.slides) % this.slides;
             },
             goToSlide(index) {
                 this.currentSlide = index;
             }
         }"
         x-init="setInterval(() => next(), 5000)">
    <!-- Image de fond dynamique -->
    <div class="absolute inset-0 z-0" style="overflow: hidden; position: absolute; top: 0; left: 0; right: 0; bottom: 0; contain: layout style paint;">
        @foreach($heroProducts as $index => $product)
        <div x-show="currentSlide === {{ $index }}"
             x-cloak
             x-transition:enter="transition ease-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-30"
             x-transition:leave="transition ease-in duration-1000"
             x-transition:leave-start="opacity-30"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-cover bg-center bg-no-repeat"
             style="background-image: url('{{ asset('storage/' . $product->image) }}'); opacity: 0.3; position: absolute; top: 0; left: 0; right: 0; bottom: 0;">
        </div>
        @endforeach
        <!-- Overlay sombre pour lisibilité -->
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/70 to-black/50 z-10"></div>
    </div>

    <div class="container mx-auto px-4 py-6 md:py-16 relative z-10">
        <!-- Boutons navigation -->
        <div class="absolute top-1/2 left-4 transform -translate-y-1/2 z-20 hidden md:block">
            <button @click="prev()" class="bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition">
                <i class="fas fa-chevron-left text-xl"></i>
            </button>
        </div>
        <div class="absolute top-1/2 right-4 transform -translate-y-1/2 z-20 hidden md:block">
            <button @click="next()" class="bg-white/20 hover:bg-white/30 text-white rounded-full p-2 transition">
                <i class="fas fa-chevron-right text-xl"></i>
            </button>
        </div>

        @foreach($heroProducts as $index => $product)
        <div x-show="currentSlide === {{ $index }}" 
             x-transition:enter="transition ease-out duration-500"
             x-transition:enter-start="opacity-0 transform translate-x-10"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100 transform translate-x-0"
             x-transition:leave-end="opacity-0 transform -translate-x-10"
             class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <!-- Contenu texte -->
            <div class="z-10 relative">
                <div class="mb-4">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-2">SHOPME</h1>
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-3">PROMOTION</h2>
                    <p class="text-sm md:text-base text-gray-300 mb-4">
                        {{ now()->format('d M') }} - {{ now()->addDays(7)->format('d M') }}
                    </p>
                </div>

                <div class="mb-6">
                    <a href="{{ route('products.show', $product->slug) }}" class="block">
                        <h3 class="text-xl md:text-2xl lg:text-3xl font-semibold mb-4 hover:text-orange-500 transition">
                            {{ $product->name }}
                        </h3>
                    </a>
                    
                    <div class="flex flex-wrap items-baseline gap-3 mb-4">
                        <span class="text-3xl md:text-4xl lg:text-5xl font-bold text-orange-500">
                            {{ number_format($product->sale_price ?? $product->price, 0, ',', ' ') }} FCFA
                        </span>
                        @if($product->is_on_sale && $product->sale_price)
                        <span class="text-lg md:text-xl text-gray-400 line-through">
                            {{ number_format($product->price, 0, ',', ' ') }} FCFA
                        </span>
                        <span class="bg-orange-500 text-white px-4 py-2 rounded-lg text-sm md:text-base font-bold">
                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                        </span>
                        @endif
                    </div>
                </div>

                <a href="{{ route('products.show', $product->slug) }}" 
                   class="inline-block bg-orange-500 text-white px-8 py-4 rounded-lg text-base md:text-lg font-semibold hover:bg-orange-600 transition">
                    Voir le produit
                </a>
            </div>

            <!-- Image produit -->
            @if($product->image)
            <div class="z-10 relative">
                <div class="relative">
                    <div class="absolute inset-0 bg-green-500 opacity-10 rounded-full blur-3xl transform rotate-12"></div>
                    <a href="{{ route('products.show', $product->slug) }}" class="block relative">
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full max-w-lg mx-auto h-auto rounded-lg shadow-2xl transform hover:scale-105 transition duration-300">
                    </a>
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    
    <!-- Indicateurs carousel (points) -->
    <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex gap-2 z-20">
        @foreach($heroProducts as $index => $product)
        <button @click="goToSlide({{ $index }})" 
                class="w-3 h-3 rounded-full transition"
                :class="currentSlide === {{ $index }} ? 'bg-white w-8' : 'bg-gray-500'"
                aria-label="Aller au slide {{ $index + 1 }}">
        </button>
        @endforeach
    </div>
</section>
@else
<!-- Hero Section par défaut -->
<section class="bg-gradient-to-r from-orange-500 to-orange-600 text-white py-20">
    <div class="container mx-auto px-4">
        <div class="text-center">
            <h1 class="text-3xl md:text-4xl font-bold mb-3">Bienvenue sur ShopMe</h1>
            <p class="text-lg md:text-xl mb-6">Découvrez notre sélection de produits de qualité</p>
            <a href="{{ route('products.index') }}" class="bg-white text-orange-600 px-6 py-2 rounded-lg text-sm font-medium hover:bg-gray-100 transition">
                Découvrir les produits
            </a>
        </div>
    </div>
</section>
@endif

<!-- Ventes Flash -->
@if(isset($flashSaleProducts) && $flashSaleProducts->count() > 0)
<section class="bg-white py-6 container mx-auto px-4">
    <div class="bg-red-600 text-white px-4 py-3 rounded-t-lg flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <span class="font-bold text-sm md:text-base">Ventes flash - maintenant</span>
            <div class="flex items-center gap-2 text-xs md:text-sm" id="countdown">
                <span class="font-semibold">Termine dans:</span>
                <span id="hours" class="bg-white/20 px-2 py-1 rounded">00</span>
                <span>:</span>
                <span id="minutes" class="bg-white/20 px-2 py-1 rounded">00</span>
                <span>:</span>
                <span id="seconds" class="bg-white/20 px-2 py-1 rounded">00</span>
            </div>
        </div>
        <a href="{{ route('products.index', ['filter' => 'on_sale']) }}" class="text-white hover:text-gray-200 text-sm font-medium">
            Voir plus >
        </a>
    </div>
    
    <div class="relative" 
         x-data="{
             scrollPosition: 0,
             canScrollLeft: false,
             canScrollRight: true,
             checkScroll() {
                 const container = this.$refs.scrollContainer;
                 this.canScrollLeft = container.scrollLeft > 0;
                 this.canScrollRight = container.scrollLeft < (container.scrollWidth - container.clientWidth - 10);
             },
             scroll(direction) {
                 const container = this.$refs.scrollContainer;
                 const scrollAmount = 300;
                 container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
                 setTimeout(() => this.checkScroll(), 300);
             }
         }"
         x-init="checkScroll()">
        <!-- Bouton précédent -->
        <button @click="scroll(-1)" 
                :class="canScrollLeft ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                class="absolute left-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-lg rounded-full p-2 hover:bg-gray-100 transition">
            <i class="fas fa-chevron-left text-gray-600"></i>
        </button>
        
        <!-- Conteneur scrollable -->
        <div x-ref="scrollContainer" 
             @scroll="checkScroll()"
             class="flex gap-4 overflow-x-auto scrollbar-hide pb-4 px-10"
             style="scrollbar-width: none; -ms-overflow-style: none;">
            @foreach($flashSaleProducts as $product)
            <div class="flex-shrink-0 w-48 md:w-56 bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <a href="{{ route('products.show', $product->slug) }}" class="block">
                    <!-- Badge de réduction -->
                    @if($product->is_on_sale && $product->sale_price)
                    <div class="relative">
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-40 md:h-48 object-cover">
                        <span class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            -{{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                        </span>
                    </div>
                    @else
                    <img src="{{ asset('storage/' . $product->image) }}" 
                         alt="{{ $product->name }}" 
                         class="w-full h-40 md:h-48 object-cover">
                    @endif
                    
                    <!-- Contenu -->
                    <div class="p-3">
                        <h4 class="text-sm font-semibold text-gray-800 mb-2 line-clamp-2 h-10">
                            {{ $product->name }}
                        </h4>
                        
                        <!-- Prix -->
                        <div class="mb-2">
                            <div class="text-lg font-bold text-orange-600">
                                {{ number_format($product->sale_price ?? $product->price, 0, ',', ' ') }} FCFA
                            </div>
                            @if($product->is_on_sale && $product->sale_price)
                            <div class="text-xs text-gray-400 line-through">
                                {{ number_format($product->price, 0, ',', ' ') }} FCFA
                            </div>
                            @endif
                        </div>
                        
                        <!-- Articles restants -->
                        @if($product->stock_quantity > 0)
                        <div class="text-xs text-gray-500">
                            {{ $product->stock_quantity }} articles restants
                        </div>
                        @else
                        <div class="text-xs text-red-500">
                            Rupture de stock
                        </div>
                        @endif
                    </div>
                </a>
            </div>
            @endforeach
        </div>
        
        <!-- Bouton suivant -->
        <button @click="scroll(1)" 
                :class="canScrollRight ? 'opacity-100' : 'opacity-0 pointer-events-none'"
                class="absolute right-0 top-1/2 transform -translate-y-1/2 z-10 bg-white shadow-lg rounded-full p-2 hover:bg-gray-100 transition">
            <i class="fas fa-chevron-right text-gray-600"></i>
        </button>
    </div>
</section>

<script>
// Compteur de temps pour ventes flash (18 heures)
let totalSeconds = 18 * 3600; // 18 heures en secondes

function updateCountdown() {
    const hours = Math.floor(totalSeconds / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    
    document.getElementById('hours').textContent = String(hours).padStart(2, '0');
    document.getElementById('minutes').textContent = String(minutes).padStart(2, '0');
    document.getElementById('seconds').textContent = String(seconds).padStart(2, '0');
    
    if (totalSeconds > 0) {
        totalSeconds--;
    }
}

updateCountdown();
setInterval(updateCountdown, 1000);
</script>

<style>
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
@endif

<!-- Catégories -->
@if(isset($categories) && $categories->count() > 0)
<section class="py-12 container mx-auto px-4">
    <h2 class="text-2xl font-bold mb-6">Catégories</h2>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}" class="group">
                <div class="bg-white rounded-lg shadow-md p-6 text-center hover:shadow-xl transition">
                    <div class="w-16 h-16 bg-orange-100 rounded-full mx-auto mb-4 flex items-center justify-center group-hover:bg-orange-500 transition">
                        <i class="fas fa-tag text-orange-600 group-hover:text-white text-2xl"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-orange-600 text-sm">{{ $category->name }}</h3>
                    @if($category->children->count() > 0)
                        <p class="text-xs text-gray-500 mt-1">{{ $category->children->count() }} sous-catégories</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

<!-- Produits en promotion - Meilleures offres -->
@if(isset($onSaleProducts) && $onSaleProducts->count() > 0)
<section class="py-6 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500 text-white px-4 py-2 rounded">
                    <h2 class="text-base font-bold">Meilleures offres</h2>
                </div>
            </div>
            <a href="{{ route('products.index', ['filter' => 'on_sale']) }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                Voir plus >
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($onSaleProducts->take(5) as $product)
                @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Produits par catégories -->
@if(isset($categoryProducts) && count($categoryProducts) > 0)
    @foreach($categoryProducts as $slug => $data)
        @php
            $category = $data['category'];
            $products = $data['products'];
        @endphp
        @if($products->count() > 0)
        <section class="py-6 {{ $loop->even ? 'bg-white' : 'bg-blue-50' }}">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-600 text-white px-4 py-2 rounded">
                            <h2 class="text-base font-bold">{{ strtoupper($category->name) }} | Meilleures offres</h2>
                        </div>
                    </div>
                    <a href="{{ route('category.show', $category->slug) }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                        Voir plus >
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    @foreach($products->take(6) as $product)
                        @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
                    @endforeach
                </div>
            </div>
        </section>
        @endif
    @endforeach
@endif

<!-- Produits en vedette -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section class="py-6 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="bg-orange-500 text-white px-4 py-2 rounded">
                    <h2 class="text-base font-bold">Produits en vedette</h2>
                </div>
            </div>
            <a href="{{ route('products.index', ['sort' => 'popular']) }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                Voir plus >
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($featuredProducts->take(5) as $product)
                @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Nouveaux produits -->
@if(isset($latestProducts) && $latestProducts->count() > 0)
<section class="py-6 bg-white">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="bg-white border-2 border-orange-500 text-orange-600 px-4 py-2 rounded">
                    <h2 class="text-base font-bold">Nouveautés</h2>
                </div>
            </div>
            <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                Voir plus >
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($latestProducts->take(5) as $product)
                @include('partials.product-card', ['product' => $product, 'favoriteIds' => $favoriteIds ?? []])
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

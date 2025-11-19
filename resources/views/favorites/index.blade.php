@extends('layouts.app')

@section('title', 'Mes Favoris - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Mes Favoris</h1>

    @if($favorites->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($favorites as $favorite)
                @include('partials.product-card', ['product' => $favorite->product, 'favoriteIds' => auth()->user()->favorites()->pluck('product_id')->toArray()])
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-heart text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-600 mb-4">Vous n'avez pas encore de favoris.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                Découvrir les produits
            </a>
        </div>
    @endif
</div>
@endsection

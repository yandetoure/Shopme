@extends('layouts.app')

@section('title', 'Mes Commandes - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Mes Commandes</h1>

    @if($orders->count() > 0)
        <div class="space-y-4">
            @foreach($orders as $order)
                <a href="{{ route('orders.show', $order) }}" class="block bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-lg">Commande #{{ $order->order_number }}</h3>
                            <p class="text-gray-600 text-sm">Date: {{ $order->created_at->format('d/m/Y H:i') }}</p>
                            <p class="text-gray-600 text-sm">{{ $order->items->count() }} article(s)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-indigo-600">{{ number_format($order->total, 2) }} €</p>
                            <span class="inline-block px-3 py-1 rounded-full text-sm mt-2
                                @if($order->status == 'delivered') bg-green-100 text-green-800
                                @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                                @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $orders->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-12 text-center">
            <i class="fas fa-box-open text-gray-400 text-6xl mb-4"></i>
            <p class="text-gray-600 mb-4">Vous n'avez pas encore passé de commande.</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                Découvrir les produits
            </a>
        </div>
    @endif
</div>
@endsection

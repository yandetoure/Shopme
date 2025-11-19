@extends('layouts.app')

@section('title', 'Commande #' . $order->order_number . ' - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Commande #{{ $order->order_number }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Détails de la commande -->
        <div class="md:col-span-2 space-y-6">
            <!-- Statut -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-bold mb-3">Statut de la commande</h2>
                <div class="flex items-center gap-3">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold
                        @if($order->status == 'delivered') bg-green-100 text-green-800
                        @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                        @elseif($order->status == 'processing') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                    <span class="text-gray-600 text-sm">Date: {{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <!-- Articles -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-bold mb-3">Articles</h2>
                <div class="space-y-3">
                    @foreach($order->items as $item)
                        <div class="flex gap-3 pb-3 border-b last:border-0">
                            <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/100?text=' . urlencode($item->product_name) }}" 
                                 alt="{{ $item->product_name }}" 
                                 class="w-16 h-16 object-cover rounded">
                            <div class="flex-1">
                                <h3 class="font-semibold text-sm">{{ $item->product_name }}</h3>
                                <p class="text-gray-600 text-xs">Quantité: {{ $item->quantity }}</p>
                                <p class="text-orange-600 font-bold text-sm">{{ number_format($item->total, 0, ',', ' ') }} FCFA</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Adresse de livraison -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h2 class="text-lg font-bold mb-3">Adresse de livraison</h2>
                <p class="text-gray-700 text-sm">{{ $order->shipping_name }}</p>
                <p class="text-gray-700 text-sm">{{ $order->shipping_address }}</p>
                @if($order->shipping_city)
                    <p class="text-gray-700 text-sm">{{ $order->shipping_city }}</p>
                @endif
                @if($order->shipping_postal_code)
                    <p class="text-gray-700 text-sm">{{ $order->shipping_postal_code }}</p>
                @endif
                <p class="text-gray-700 text-sm">Tél: {{ $order->shipping_phone }}</p>
            </div>
        </div>

        <!-- Résumé -->
        <div class="bg-white rounded-lg shadow-md p-4 h-fit sticky top-20">
            <h2 class="text-lg font-bold mb-3">Résumé</h2>
            <div class="space-y-1.5 mb-3 text-sm">
                <div class="flex justify-between">
                    <span>Sous-total</span>
                    <span>{{ number_format($order->subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between">
                    <span>TVA</span>
                    <span>{{ number_format($order->tax, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between">
                    <span>Livraison</span>
                    <span>{{ number_format($order->shipping, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="border-t pt-1.5 flex justify-between font-bold text-base">
                    <span>Total</span>
                    <span class="text-orange-600">{{ number_format($order->total, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t">
                <p class="text-xs text-gray-600 mb-1">Méthode de paiement:</p>
                <p class="font-medium text-sm">{{ $order->payment_method }}</p>
                <p class="text-xs mt-1">
                    <span class="px-2 py-1 rounded {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

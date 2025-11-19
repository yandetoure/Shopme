@extends('dashboard.layout')

@section('title', 'Détails de la Commande - ShopMe')
@section('page-title', 'Détails de la Commande #{{ $order->order_number }}')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Actions -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Commande #{{ $order->order_number }}</h2>
                <p class="text-gray-600">Créée le {{ $order->created_at->format('d/m/Y à H:i') }}</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-2"></i> Retour
            </a>
        </div>
    </div>

    <!-- Informations client -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Informations client</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Nom</p>
                <p class="font-semibold text-gray-800">{{ $order->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-semibold text-gray-800">{{ $order->user->email }}</p>
            </div>
        </div>
    </div>

    <!-- Produits -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Produits commandés</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantité</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-4">{{ $item->product_name }}</td>
                        <td class="px-4 py-4">{{ number_format($item->price, 0, '', '.') }} FCFA</td>
                        <td class="px-4 py-4">{{ $item->quantity }}</td>
                        <td class="px-4 py-4 font-bold">{{ number_format($item->total, 0, '', '.') }} FCFA</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Totaux -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Totaux</h3>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-600">Sous-total</span>
                <span class="font-semibold">{{ number_format($order->subtotal, 0, '', '.') }} FCFA</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">TVA</span>
                <span class="font-semibold">{{ number_format($order->tax, 0, '', '.') }} FCFA</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Livraison</span>
                <span class="font-semibold">{{ number_format($order->shipping, 0, '', '.') }} FCFA</span>
            </div>
            @if($order->discount > 0)
            <div class="flex justify-between text-red-600">
                <span>Remise</span>
                <span class="font-semibold">-{{ number_format($order->discount, 0, '', '.') }} FCFA</span>
            </div>
            @endif
            <div class="flex justify-between text-xl font-bold border-t pt-2">
                <span>Total</span>
                <span>{{ number_format($order->total, 0, '', '.') }} FCFA</span>
            </div>
        </div>
    </div>

    <!-- Gestion du statut -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Gestion</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut de la commande</label>
                <div class="flex space-x-2">
                    <select name="status" class="flex-1 px-4 py-2 border rounded-lg">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>En traitement</option>
                        <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Livrée</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                    </select>
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                        Mettre à jour
                    </button>
                </div>
            </form>
            <form action="{{ route('admin.orders.updatePaymentStatus', $order) }}" method="POST">
                @csrf
                @method('PUT')
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut de paiement</label>
                <div class="flex space-x-2">
                    <select name="payment_status" class="flex-1 px-4 py-2 border rounded-lg">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>En attente</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Payé</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Échoué</option>
                        <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                    </select>
                    <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


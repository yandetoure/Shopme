@extends('dashboard.layout')

@section('title', 'Détails du Tarif de Livraison - ShopMe')
@section('page-title', 'Détails du Tarif de Livraison')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-gray-800">{{ $shippingRate->name }}</h2>
            <a href="{{ route('admin.shipping-rates.edit', $shippingRate) }}" class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
        </div>
        <div class="space-y-3 text-sm">
            <div>
                <span class="font-semibold">Description:</span> {{ $shippingRate->description ?? '-' }}
            </div>
            <div>
                <span class="font-semibold">Prix:</span> 
                @if($shippingRate->is_free)
                    <span class="text-green-600 font-bold">Gratuit</span>
                @else
                    {{ number_format($shippingRate->price, 0, '', '.') }} FCFA
                @endif
            </div>
            <div>
                <span class="font-semibold">Montant minimum:</span> {{ $shippingRate->min_order_amount ? number_format($shippingRate->min_order_amount, 0, '', '.') . ' FCFA' : '-' }}
            </div>
            <div>
                <span class="font-semibold">Montant maximum:</span> {{ $shippingRate->max_order_amount ? number_format($shippingRate->max_order_amount, 0, '', '.') . ' FCFA' : '-' }}
            </div>
            <div>
                <span class="font-semibold">Jours estimés:</span> {{ $shippingRate->estimated_days ?? '-' }}
            </div>
            <div>
                <span class="font-semibold">Statut:</span> {{ $shippingRate->is_active ? 'Actif' : 'Inactif' }}
            </div>
            <div>
                <span class="font-semibold">Commandes utilisant ce tarif:</span> {{ $shippingRate->orders->count() }}
            </div>
        </div>
    </div>
</div>
@endsection


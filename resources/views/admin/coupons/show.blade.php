@extends('dashboard.layout')

@section('title', 'Détails du Coupon - ShopMe')
@section('page-title', 'Détails du Coupon')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $coupon->name }}</h2>
                <p class="text-sm text-orange-600 font-bold">{{ $coupon->code }}</p>
            </div>
            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-semibold">Type:</span> {{ $coupon->type == 'percentage' ? 'Pourcentage' : 'Montant fixe' }}
            </div>
            <div>
                <span class="font-semibold">Valeur:</span> 
                @if($coupon->type == 'percentage')
                    {{ $coupon->value }}%
                @else
                    {{ number_format($coupon->value, 0, '', '.') }} FCFA
                @endif
            </div>
            <div>
                <span class="font-semibold">Montant minimum:</span> {{ $coupon->minimum_amount ? number_format($coupon->minimum_amount, 0, '', '.') . ' FCFA' : '-' }}
            </div>
            <div>
                <span class="font-semibold">Remise max:</span> {{ $coupon->maximum_discount ? number_format($coupon->maximum_discount, 0, '', '.') . ' FCFA' : '-' }}
            </div>
            <div>
                <span class="font-semibold">Utilisations:</span> {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
            </div>
            <div>
                <span class="font-semibold">Limite par utilisateur:</span> {{ $coupon->usage_limit_per_user ?? '∞' }}
            </div>
            <div>
                <span class="font-semibold">Valide du:</span> {{ $coupon->valid_from ? $coupon->valid_from->format('d/m/Y') : '-' }}
            </div>
            <div>
                <span class="font-semibold">Valide jusqu'au:</span> {{ $coupon->valid_until ? $coupon->valid_until->format('d/m/Y') : '-' }}
            </div>
            <div>
                <span class="font-semibold">Statut:</span> {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
            </div>
        </div>
    </div>

    @if($coupon->usages->count() > 0)
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Utilisations récentes</h3>
        <div class="space-y-2">
            @foreach($coupon->usages->take(10) as $usage)
            <div class="flex justify-between text-xs p-2 bg-gray-50 rounded">
                <span>{{ $usage->user->name ?? 'Utilisateur supprimé' }}</span>
                <span class="font-semibold">{{ number_format($usage->discount_amount, 0, '', '.') }} FCFA</span>
                <span class="text-gray-500">{{ $usage->created_at->format('d/m/Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection


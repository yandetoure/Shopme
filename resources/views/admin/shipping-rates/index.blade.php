@extends('dashboard.layout')

@section('title', 'Gestion des Tarifs de Livraison - ShopMe')
@section('page-title', 'Gestion des Tarifs de Livraison')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Tarifs de livraison</h2>
            <p class="text-sm text-gray-600">Gérez vos tarifs de livraison</p>
        </div>
        <a href="{{ route('admin.shipping-rates.create') }}" class="bg-orange-500 text-white px-4 py-2 text-sm rounded-lg hover:bg-orange-600 font-semibold">
            <i class="fas fa-plus mr-1"></i> Ajouter un tarif
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Prix</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant min/max</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jours estimés</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($shippingRates as $rate)
                <tr>
                    <td class="px-4 py-3">
                        <div class="text-sm font-medium text-gray-900">{{ $rate->name }}</div>
                        @if($rate->description)
                            <div class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($rate->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($rate->is_free)
                            <span class="text-green-600 font-bold">Gratuit</span>
                        @else
                            {{ number_format($rate->price, 0, '', '.') }} FCFA
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        @if($rate->min_order_amount || $rate->max_order_amount)
                            {{ $rate->min_order_amount ? number_format($rate->min_order_amount, 0, '', '.') . ' FCFA' : '0' }} - 
                            {{ $rate->max_order_amount ? number_format($rate->max_order_amount, 0, '', '.') . ' FCFA' : '∞' }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $rate->estimated_days ?? '-' }} jours</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $rate->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $rate->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.shipping-rates.edit', $rate) }}" class="text-orange-600 hover:text-orange-900">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.shipping-rates.destroy', $rate) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-3 text-center text-xs text-gray-500">Aucun tarif de livraison</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


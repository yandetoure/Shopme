@extends('dashboard.layout')

@section('title', 'Gestion des Coupons - ShopMe')
@section('page-title', 'Gestion des Coupons')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Coupons</h2>
            <p class="text-sm text-gray-600">Gérez vos codes promo</p>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="bg-orange-500 text-white px-4 py-2 text-sm rounded-lg hover:bg-orange-600 font-semibold">
            <i class="fas fa-plus mr-1"></i> Ajouter un coupon
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Code, nom..."
                       class="w-full px-3 py-1.5 text-sm border rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full px-3 py-1.5 text-sm border rounded-lg">
                    <option value="">Tous</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-blue-600">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Tableau -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valeur</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Utilisations</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($coupons as $coupon)
                <tr>
                    <td class="px-4 py-3">
                        <span class="text-sm font-bold text-orange-600">{{ $coupon->code }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-900">{{ $coupon->name }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $coupon->type == 'percentage' ? 'Pourcentage' : 'Montant fixe' }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($coupon->type == 'percentage')
                            {{ $coupon->value }}%
                        @else
                            {{ number_format($coupon->value, 0, '', '.') }} FCFA
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $coupon->used_count }} / {{ $coupon->usage_limit ?? '∞' }}
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $coupon->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $coupon->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" class="text-orange-600 hover:text-orange-900">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr ?');">
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
                    <td colspan="7" class="px-4 py-3 text-center text-xs text-gray-500">Aucun coupon</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-4 py-3 text-sm">
        {{ $coupons->links() }}
    </div>
</div>
@endsection


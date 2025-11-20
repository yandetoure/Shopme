@extends('dashboard.layout')

@section('title', 'Modifier le Coupon - ShopMe')
@section('page-title', 'Modifier le Coupon')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="code" class="block text-xs font-medium text-gray-700 mb-1">Code *</label>
                        <input type="text" id="code" name="code" value="{{ old('code', $coupon->code) }}" required
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="name" class="block text-xs font-medium text-gray-700 mb-1">Nom *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $coupon->name) }}" required
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="2"
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description', $coupon->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="type" class="block text-xs font-medium text-gray-700 mb-1">Type *</label>
                        <select id="type" name="type" required
                                class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="percentage" {{ old('type', $coupon->type) == 'percentage' ? 'selected' : '' }}>Pourcentage</option>
                            <option value="fixed" {{ old('type', $coupon->type) == 'fixed' ? 'selected' : '' }}>Montant fixe</option>
                        </select>
                    </div>

                    <div>
                        <label for="value" class="block text-xs font-medium text-gray-700 mb-1">Valeur *</label>
                        <input type="number" id="value" name="value" value="{{ old('value', $coupon->value) }}" step="0.01" min="0" required
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="minimum_amount" class="block text-xs font-medium text-gray-700 mb-1">Montant minimum (FCFA)</label>
                        <input type="number" id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount', $coupon->minimum_amount) }}" step="0.01" min="0"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="maximum_discount" class="block text-xs font-medium text-gray-700 mb-1">Remise max (FCFA)</label>
                        <input type="number" id="maximum_discount" name="maximum_discount" value="{{ old('maximum_discount', $coupon->maximum_discount) }}" step="0.01" min="0"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="usage_limit" class="block text-xs font-medium text-gray-700 mb-1">Limite d'utilisation</label>
                        <input type="number" id="usage_limit" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="usage_limit_per_user" class="block text-xs font-medium text-gray-700 mb-1">Limite par utilisateur</label>
                        <input type="number" id="usage_limit_per_user" name="usage_limit_per_user" value="{{ old('usage_limit_per_user', $coupon->usage_limit_per_user) }}" min="1"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="valid_from" class="block text-xs font-medium text-gray-700 mb-1">Valide à partir de</label>
                        <input type="date" id="valid_from" name="valid_from" value="{{ old('valid_from', $coupon->valid_from ? $coupon->valid_from->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="valid_until" class="block text-xs font-medium text-gray-700 mb-1">Valide jusqu'au</label>
                        <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until', $coupon->valid_until ? $coupon->valid_until->format('Y-m-d') : '') }}"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $coupon->is_active) ? 'checked' : '' }}
                           class="mr-2">
                    <label for="is_active" class="text-xs font-medium text-gray-700">Actif</label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.coupons.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold">
                        Mettre à jour
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


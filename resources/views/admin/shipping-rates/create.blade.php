@extends('dashboard.layout')

@section('title', 'Créer un Tarif de Livraison - ShopMe')
@section('page-title', 'Créer un Tarif de Livraison')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.shipping-rates.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-xs font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label for="description" class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="2"
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="price" class="block text-xs font-medium text-gray-700 mb-1">Prix (FCFA) *</label>
                        <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="estimated_days" class="block text-xs font-medium text-gray-700 mb-1">Jours estimés</label>
                        <input type="number" id="estimated_days" name="estimated_days" value="{{ old('estimated_days') }}" min="1"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="min_order_amount" class="block text-xs font-medium text-gray-700 mb-1">Montant minimum (FCFA)</label>
                        <input type="number" id="min_order_amount" name="min_order_amount" value="{{ old('min_order_amount') }}" step="0.01" min="0"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="max_order_amount" class="block text-xs font-medium text-gray-700 mb-1">Montant maximum (FCFA)</label>
                        <input type="number" id="max_order_amount" name="max_order_amount" value="{{ old('max_order_amount') }}" step="0.01" min="0"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label for="sort_order" class="block text-xs font-medium text-gray-700 mb-1">Ordre de tri</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_free" name="is_free" value="1" {{ old('is_free') ? 'checked' : '' }}
                           class="mr-2">
                    <label for="is_free" class="text-xs font-medium text-gray-700">Livraison gratuite</label>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           class="mr-2">
                    <label for="is_active" class="text-xs font-medium text-gray-700">Actif</label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.shipping-rates.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </a>
                    <button type="submit" class="px-4 py-2 text-sm bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold">
                        Créer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


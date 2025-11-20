@extends('dashboard.layout')

@section('title', 'Paramètres - ShopMe')
@section('page-title', 'Paramètres')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Informations générales</h3>
                </div>

                <div>
                    <label for="site_name" class="block text-xs font-medium text-gray-700 mb-1">Nom du site *</label>
                    <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label for="site_email" class="block text-xs font-medium text-gray-700 mb-1">Email du site *</label>
                    <input type="email" id="site_email" name="site_email" value="{{ old('site_email', $settings['site_email']) }}" required
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="pt-4">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Paramètres financiers</h3>
                </div>

                <div>
                    <label for="currency" class="block text-xs font-medium text-gray-700 mb-1">Devise</label>
                    <input type="text" id="currency" name="currency" value="{{ old('currency', $settings['currency']) }}" readonly
                           class="w-full px-3 py-1.5 text-sm border rounded-lg bg-gray-100">
                    <p class="text-xs text-gray-500 mt-1">La devise est fixée en FCFA</p>
                </div>

                <div>
                    <label for="tax_rate" class="block text-xs font-medium text-gray-700 mb-1">Taux de TVA (%) *</label>
                    <input type="number" id="tax_rate" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate']) }}" step="0.01" min="0" max="100" required
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label for="default_shipping" class="block text-xs font-medium text-gray-700 mb-1">Livraison par défaut (FCFA) *</label>
                    <input type="number" id="default_shipping" name="default_shipping" value="{{ old('default_shipping', $settings['default_shipping']) }}" step="0.01" min="0" required
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="flex justify-end space-x-4 pt-4">
                    <button type="submit" class="px-4 py-2 text-sm bg-orange-500 text-white rounded-lg hover:bg-orange-600 font-semibold">
                        Enregistrer
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection


@extends('layouts.app')

@section('title', 'Finaliser la commande - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Finaliser la commande</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Formulaire de commande -->
        <div class="md:col-span-2">
            <form method="POST" action="{{ route('orders.store') }}" class="bg-white rounded-lg shadow-md p-4 space-y-4">
                @csrf
                
                <h2 class="text-lg font-bold mb-3">Informations de livraison</h2>
                
                <div>
                    <label for="shipping_name" class="block text-xs font-medium mb-1">Nom complet *</label>
                    <input type="text" id="shipping_name" name="shipping_name" value="{{ old('shipping_name', Auth::user()->name) }}" required 
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500 @error('shipping_name') border-red-500 @enderror">
                    @error('shipping_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_phone" class="block text-xs font-medium mb-1">Téléphone *</label>
                    <input type="text" id="shipping_phone" name="shipping_phone" value="{{ old('shipping_phone', Auth::user()->phone) }}" required 
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500 @error('shipping_phone') border-red-500 @enderror">
                    @error('shipping_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_address" class="block text-xs font-medium mb-1">Adresse *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" required 
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500 @error('shipping_address') border-red-500 @enderror">{{ old('shipping_address', Auth::user()->address) }}</textarea>
                    @error('shipping_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="shipping_city" class="block text-xs font-medium mb-1">Ville</label>
                        <input type="text" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" 
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label for="shipping_postal_code" class="block text-xs font-medium mb-1">Code postal</label>
                        <input type="text" id="shipping_postal_code" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" 
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="shipping_country" class="block text-xs font-medium mb-1">Pays</label>
                    <input type="text" id="shipping_country" name="shipping_country" value="{{ old('shipping_country', 'France') }}" 
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="payment_method" class="block text-xs font-medium mb-1">Méthode de paiement *</label>
                    <select id="payment_method" name="payment_method" required 
                            class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500 @error('payment_method') border-red-500 @enderror">
                        <option value="">Sélectionner...</option>
                        <option value="Carte bancaire" {{ old('payment_method') == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                        <option value="PayPal" {{ old('payment_method') == 'PayPal' ? 'selected' : '' }}>PayPal</option>
                        <option value="Virement bancaire" {{ old('payment_method') == 'Virement bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                        <option value="Chèque" {{ old('payment_method') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs font-medium mb-1">Notes (optionnel)</label>
                    <textarea id="notes" name="notes" rows="3" 
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm font-medium">
                    Confirmer la commande
                </button>
            </form>
        </div>

        <!-- Résumé -->
        <div class="bg-white rounded-lg shadow-md p-4 h-fit sticky top-20">
            <h2 class="text-lg font-bold mb-3">Résumé</h2>
            <div class="space-y-3 mb-4">
                @foreach($cartItems as $item)
                    <div class="flex gap-3 pb-3 border-b">
                        <img src="{{ $item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/60?text=' . urlencode($item->product->name) }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-14 h-14 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-semibold text-xs">{{ $item->product->name }}</h3>
                            <p class="text-gray-600 text-xs">x{{ $item->quantity }}</p>
                            <p class="text-indigo-600 font-bold text-xs">{{ number_format($item->total, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="space-y-1.5 mb-3 text-sm">
                <div class="flex justify-between">
                    <span>Sous-total</span>
                    <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>TVA (20%)</span>
                    <span>{{ number_format($tax, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>Livraison</span>
                    <span>{{ number_format($shipping, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="border-t pt-1.5 flex justify-between font-bold text-base">
                    <span>Total</span>
                    <span class="text-indigo-600">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

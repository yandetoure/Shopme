@extends('layouts.app')

@section('title', 'Finaliser la commande - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Finaliser la commande</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Formulaire de commande -->
        <div class="md:col-span-2">
            <form method="POST" action="{{ route('orders.store') }}" id="checkout-form" class="bg-white rounded-lg shadow-md p-4 space-y-4">
                @csrf
                <input type="hidden" name="coupon_code" value="{{ request('coupon_code') }}">
                <input type="hidden" name="shipping_rate_id" id="shipping_rate_id_input" value="{{ isset($shippingRate) ? $shippingRate->id : '' }}">
                
                <h2 class="text-lg font-bold mb-3">Informations de livraison</h2>
                
                <div>
                    <label for="shipping_name" class="block text-xs font-medium mb-1">Nom complet *</label>
                    <input type="text" id="shipping_name" name="shipping_name" value="{{ old('shipping_name', Auth::user()->name) }}" required 
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 @error('shipping_name') border-red-500 @enderror">
                    @error('shipping_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_phone" class="block text-xs font-medium mb-1">Téléphone *</label>
                    <input type="text" id="shipping_phone" name="shipping_phone" value="{{ old('shipping_phone', Auth::user()->phone) }}" required 
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 @error('shipping_phone') border-red-500 @enderror">
                    @error('shipping_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="shipping_address" class="block text-xs font-medium mb-1">Adresse *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="3" required 
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 @error('shipping_address') border-red-500 @enderror">{{ old('shipping_address', Auth::user()->address) }}</textarea>
                    @error('shipping_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="shipping_city" class="block text-xs font-medium mb-1">Ville</label>
                        <input type="text" id="shipping_city" name="shipping_city" value="{{ old('shipping_city') }}" 
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>

                    <div>
                        <label for="shipping_postal_code" class="block text-xs font-medium mb-1">Code postal</label>
                        <input type="text" id="shipping_postal_code" name="shipping_postal_code" value="{{ old('shipping_postal_code') }}" 
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label for="shipping_country" class="block text-xs font-medium mb-1">Pays</label>
                    <input type="text" id="shipping_country" name="shipping_country" value="{{ old('shipping_country', 'France') }}" 
                           class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label for="payment_method" class="block text-xs font-medium mb-1">Méthode de paiement *</label>
                    <select id="payment_method" name="payment_method" required 
                            class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 @error('payment_method') border-red-500 @enderror">
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
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
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
                        <img src="{{ ($item->variation && $item->variation->image) ? asset('storage/' . $item->variation->image) : ($item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/60?text=' . urlencode($item->product->name)) }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-14 h-14 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-semibold text-xs">{{ $item->product->name }}</h3>
                            @if($item->selected_attributes && count($item->selected_attributes) > 0)
                                <p class="text-gray-500 text-xs">
                                    @foreach($item->selected_attributes as $key => $value)
                                        {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                    @endforeach
                                </p>
                            @endif
                            <p class="text-gray-600 text-xs">x{{ $item->quantity }}</p>
                            <p class="text-orange-600 font-bold text-xs">{{ number_format($item->total, 0, ',', ' ') }} FCFA</p>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Code promo -->
            <div class="mb-4 border-t pt-3">
                <form method="GET" action="{{ route('cart.checkout') }}" id="coupon-form" 
                      x-data="{ couponCode: '{{ request('coupon_code') ?? '' }}', loading: false }">
                    <label for="coupon_code" class="block text-xs font-medium mb-1">Code promo</label>
                    <div class="flex gap-2">
                        <input type="text" id="coupon_code" name="coupon_code" x-model="coupon_code"
                               placeholder="Entrez votre code"
                               class="flex-1 px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                        <button type="submit" @click="loading = true" :disabled="loading"
                                class="px-4 py-1.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-xs font-medium disabled:opacity-50">
                            <span x-show="!loading">Appliquer</span>
                            <span x-show="loading">...</span>
                        </button>
                    </div>
                    @if(request()->coupon_code && !$coupon)
                        <p class="text-red-500 text-xs mt-1">Code promo invalide.</p>
                    @endif
                    @if($coupon && $discount > 0)
                        <p class="text-green-600 text-xs mt-1 font-medium">Code promo appliqué : -{{ number_format($discount, 0, ',', ' ') }} FCFA</p>
                    @endif
                    @if(request()->coupon_code && $coupon)
                        <a href="{{ route('cart.checkout') }}" class="text-xs text-red-600 hover:underline mt-1 block">Retirer le code promo</a>
                    @endif
                </form>
            </div>
            
            <!-- Tarifs de livraison -->
            @if(isset($shippingRates) && $shippingRates->count() > 1)
            <div class="mb-4 border-t pt-3">
                <label for="shipping_rate_select" class="block text-xs font-medium mb-2">Méthode de livraison</label>
                <select id="shipping_rate_select" 
                        class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500"
                        onchange="document.getElementById('shipping_rate_id_input').value = this.value; window.location.href = '{{ route('cart.checkout') }}?coupon_code=' + encodeURIComponent('{{ request('coupon_code') ?? '' }}') + '&shipping_rate_id=' + this.value;">
                    @foreach($shippingRates as $rate)
                        <option value="{{ $rate->id }}" 
                                {{ (isset($shippingRate) && $shippingRate->id === $rate->id) || (!isset($shippingRate) && $loop->first) ? 'selected' : '' }}
                                data-price="{{ $rate->is_free ? 0 : $rate->price }}">
                            {{ $rate->name }} - 
                            @if($rate->is_free)
                                Gratuit
                            @else
                                {{ number_format($rate->price, 0, ',', ' ') }} FCFA
                            @endif
                            @if($rate->estimated_days)
                                ({{ $rate->estimated_days }} jours)
                            @endif
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="shipping_rate_id" value="{{ isset($shippingRate) ? $shippingRate->id : ($shippingRates->first()->id ?? '') }}">
            </div>
            @elseif(isset($shippingRate))
                <input type="hidden" name="shipping_rate_id" value="{{ $shippingRate->id }}">
            @endif
            
            <div class="space-y-1.5 mb-3 text-sm">
                <div class="flex justify-between">
                    <span>Sous-total</span>
                    <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between text-gray-600">
                    <span>TVA (20%)</span>
                    <span>{{ number_format($tax, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($discount > 0)
                <div class="flex justify-between text-green-600">
                    <span>Remise ({{ $coupon->code ?? '' }})</span>
                    <span>-{{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
                <div class="flex justify-between text-gray-600">
                    <span>Livraison</span>
                    <span>
                        @if($shipping == 0)
                            <span class="text-green-600">Gratuit</span>
                        @else
                            {{ number_format($shipping, 0, ',', ' ') }} FCFA
                        @endif
                    </span>
                </div>
                <div class="border-t pt-1.5 flex justify-between font-bold text-base">
                    <span>Total</span>
                    <span class="text-orange-600">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

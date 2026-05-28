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
                <input type="hidden" name="shipping_rate_id" id="shipping_rate_id_input" value="{{ $shippingRate ? $shippingRate->id : '' }}">
                
                <h2 class="text-lg font-bold mb-3">Informations de livraison</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="shipping_country" class="block text-xs font-medium mb-1">Pays *</label>
                        <select id="shipping_country" name="shipping_country" required 
                                onchange="window.location.href = '{{ route('cart.checkout') }}?coupon_code=' + encodeURIComponent('{{ request('coupon_code') ?? '' }}') + '&shipping_country=' + this.value;"
                                class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                            <option value="Sénégal" {{ $country == 'Sénégal' ? 'selected' : '' }}>Sénégal</option>
                            <option value="France" {{ $country == 'France' ? 'selected' : '' }}>France</option>
                            <option value="Côte d'Ivoire" {{ $country == "Côte d'Ivoire" ? 'selected' : '' }}>Côte d'Ivoire</option>
                            <!-- Ajoutez d'autres pays si nécessaire -->
                        </select>
                    </div>

                    <div>
                        <label for="shipping_region" class="block text-xs font-medium mb-1">Région / Ville *</label>
                        <input type="text" id="shipping_region" name="shipping_region" value="{{ old('shipping_region', $region) }}" required 
                               placeholder="Ex: Dakar, Thiès, Paris..."
                               onblur="if(this.value != '{{ $region }}') window.location.href = '{{ route('cart.checkout') }}?coupon_code=' + encodeURIComponent('{{ request('coupon_code') ?? '' }}') + '&shipping_country=' + document.getElementById('shipping_country').value + '&shipping_region=' + this.value;"
                               class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label for="shipping_address" class="block text-xs font-medium mb-1">Adresse exacte *</label>
                    <textarea id="shipping_address" name="shipping_address" rows="2" required 
                              placeholder="Rue, appartement, porte..."
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 @error('shipping_address') border-red-500 @enderror">{{ old('shipping_address', Auth::user()->address) }}</textarea>
                    @error('shipping_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="payment_method" class="block text-xs font-medium mb-1">Méthode de paiement *</label>
                    <select id="payment_method" name="payment_method" required 
                            class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500 @error('payment_method') border-red-500 @enderror">
                        <option value="">Sélectionner...</option>
                        <option value="Paiement à la livraison" {{ old('payment_method') == 'Paiement à la livraison' ? 'selected' : '' }}>Paiement à la livraison</option>
                        <option value="Wave / Orange Money" {{ old('payment_method') == 'Wave / Orange Money' ? 'selected' : '' }}>Wave / Orange Money</option>
                        <option value="Carte bancaire" {{ old('payment_method') == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="notes" class="block text-xs font-medium mb-1">Notes (optionnel)</label>
                    <textarea id="notes" name="notes" rows="2" 
                              class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-orange-500 text-white px-4 py-3 rounded-lg hover:bg-orange-600 text-base font-bold transition shadow-lg">
                    Confirmer la commande ({{ number_format($total, 0, ',', ' ') }} FCFA)
                </button>
            </form>
        </div>

        <!-- Résumé -->
        <div class="bg-white rounded-lg shadow-md p-4 h-fit sticky top-20">
            <h2 class="text-lg font-bold mb-3">Résumé</h2>
            <div class="space-y-3 mb-4 max-h-60 overflow-y-auto">
                @foreach($cartItems as $item)
                    <div class="flex gap-3 pb-3 border-b">
                        <img src="{{ ($item->variation && $item->variation->image) ? asset('storage/' . $item->variation->image) : ($item->product->image ? asset('storage/' . $item->product->image) : 'https://via.placeholder.com/60?text=' . urlencode($item->product->name)) }}" 
                             alt="{{ $item->product->name }}" 
                             class="w-14 h-14 object-cover rounded">
                        <div class="flex-1">
                            <h3 class="font-semibold text-xs leading-tight">{{ $item->product->name }}</h3>
                            @if($item->selected_attributes && count($item->selected_attributes) > 0)
                                <p class="text-gray-500 text-[10px]">
                                    @foreach($item->selected_attributes as $key => $value)
                                        {{ ucfirst($key) }}: {{ $value }}@if(!$loop->last), @endif
                                    @endforeach
                                </p>
                            @endif
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-gray-600 text-xs">x{{ $item->quantity }}</span>
                                <span class="text-orange-600 font-bold text-xs">{{ number_format($item->total, 0, ',', ' ') }} FCFA</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Code promo -->
            <div class="mb-4 border-t pt-3">
                <form method="GET" action="{{ route('cart.checkout') }}" id="coupon-form" 
                      x-data="{ coupon_code: '{{ request('coupon_code') ?? '' }}', loading: false }">
                    <input type="hidden" name="shipping_country" value="{{ $country }}">
                    <input type="hidden" name="shipping_region" value="{{ $region }}">
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
                        <div class="flex justify-between items-center mt-2 bg-green-50 p-2 rounded border border-green-100">
                            <span class="text-green-700 text-xs font-medium">Code: {{ $coupon->code }}</span>
                            <a href="{{ route('cart.checkout', ['shipping_country' => $country, 'shipping_region' => $region]) }}" class="text-[10px] text-red-600 hover:underline">Retirer</a>
                        </div>
                    @endif
                </form>
            </div>
            
            <!-- Méthode de livraison -->
            @if($shippingRates->count() > 0)
            <div class="mb-4 border-t pt-3">
                <label class="block text-xs font-medium mb-2">Livraison pour {{ $region ?? $country }}</label>
                <div class="space-y-2">
                    @foreach($shippingRates as $rate)
                        <label class="flex items-center p-2 border rounded-lg cursor-pointer hover:bg-gray-50 transition {{ (isset($shippingRate) && $shippingRate->id === $rate->id) ? 'border-orange-500 bg-orange-50' : 'border-gray-200' }}">
                            <input type="radio" name="shipping_rate_radio" value="{{ $rate->id }}" 
                                   {{ (isset($shippingRate) && $shippingRate->id === $rate->id) ? 'checked' : '' }}
                                   onchange="document.getElementById('shipping_rate_id_input').value = this.value; window.location.href = '{{ route('cart.checkout') }}?coupon_code=' + encodeURIComponent('{{ request('coupon_code') ?? '' }}') + '&shipping_country=' + encodeURIComponent('{{ $country }}') + '&shipping_region=' + encodeURIComponent('{{ $region }}') + '&shipping_rate_id=' + this.value;"
                                   class="text-orange-500 focus:ring-orange-500">
                            <div class="ml-3 flex-1">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold">{{ $rate->name }}</span>
                                    <span class="text-xs font-bold {{ $rate->is_free ? 'text-green-600' : 'text-gray-900' }}">
                                        {{ $rate->is_free ? 'Gratuit' : number_format($rate->price, 0, ',', ' ') . ' FCFA' }}
                                    </span>
                                </div>
                                @if($rate->estimated_days)
                                    <p class="text-[10px] text-gray-500">{{ $rate->estimated_days }} jours ouvrés</p>
                                @endif
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            @else
                <div class="mb-4 border-t pt-3">
                    <p class="text-xs text-red-500">Aucune méthode de livraison disponible pour cette zone.</p>
                </div>
            @endif
            
            <div class="space-y-2 mb-3 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Sous-total</span>
                    <span>{{ number_format($subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                
                @if($discount > 0)
                <div class="flex justify-between text-green-600">
                    <span class="flex items-center gap-1">
                        <i class="fas fa-tag text-[10px]"></i>
                        Remise
                    </span>
                    <span>-{{ number_format($discount, 0, ',', ' ') }} FCFA</span>
                </div>
                @endif
                
                <div class="flex justify-between text-gray-600">
                    <span>Livraison</span>
                    <span>
                        @if($shipping == 0)
                            <span class="text-green-600 font-medium">Gratuit</span>
                        @else
                            {{ number_format($shipping, 0, ',', ' ') }} FCFA
                        @endif
                    </span>
                </div>
                
                <div class="border-t pt-2 mt-2 flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span class="text-orange-600">{{ number_format($total, 0, ',', ' ') }} FCFA</span>
                </div>
                <p class="text-[10px] text-gray-400 text-center mt-2 italic">Prix net - TVA non applicable</p>
            </div>
        </div>
    </div>
        </div>
    </div>
</div>
@endsection

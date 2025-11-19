@extends('layouts.app')

@section('title', 'Mon Profil - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ activeTab: 'profile' }">
    <h1 class="text-3xl font-bold mb-8">Mon Profil</h1>

    <!-- Tabs Mobile -->
    <div class="md:hidden mb-6">
        <div class="bg-white rounded-lg shadow-md p-2 flex gap-2">
            <button @click="activeTab = 'profile'" 
                    :class="activeTab === 'profile' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="flex-1 px-4 py-2 rounded font-semibold transition">
                <i class="fas fa-user mr-2"></i>Profil
            </button>
            <button @click="activeTab = 'orders'" 
                    :class="activeTab === 'orders' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="flex-1 px-4 py-2 rounded font-semibold transition">
                <i class="fas fa-box mr-2"></i>Commandes
            </button>
            <button @click="activeTab = 'cart'" 
                    :class="activeTab === 'cart' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700'"
                    class="flex-1 px-4 py-2 rounded font-semibold transition">
                <i class="fas fa-shopping-cart mr-2"></i>Panier
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Sidebar Desktop -->
        <aside class="hidden md:block">
            <div class="bg-white rounded-lg shadow-md p-4 sticky top-20">
                <a href="{{ route('profile.index') }}" class="block px-4 py-2 mb-2 rounded hover:bg-indigo-50 text-indigo-600 font-semibold">
                    <i class="fas fa-user mr-2"></i>Mon Profil
                </a>
                <a href="{{ route('orders.index') }}" class="block px-4 py-2 mb-2 rounded hover:bg-gray-100 text-gray-700">
                    <i class="fas fa-box mr-2"></i>Mes Commandes
                </a>
                <a href="{{ route('cart.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 text-gray-700">
                    <i class="fas fa-shopping-cart mr-2"></i>Mon Panier
                    @if($cartCount > 0)
                        <span class="ml-2 bg-indigo-600 text-white text-xs rounded-full px-2 py-1">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </aside>

        <!-- Contenu -->
        <div class="md:col-span-3">
            <!-- Onglet Profil -->
            <div x-show="activeTab === 'profile'" x-cloak class="md:block">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-6">Informations personnelles</h2>
                    <form method="POST" action="#" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium mb-2">Nom complet</label>
                            <input type="text" value="{{ $user->name }}" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Téléphone</label>
                            <input type="text" value="{{ $user->phone ?? '' }}" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Adresse</label>
                            <textarea disabled class="w-full px-4 py-2 border rounded-lg bg-gray-50">{{ $user->address ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Rôle</label>
                            <input type="text" value="{{ ucfirst($user->role) }}" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-50">
                        </div>
                    </form>
                </div>
            </div>

            <!-- Onglet Commandes -->
            <div x-show="activeTab === 'orders'" x-cloak class="md:block">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-6">Mes Commandes récentes</h2>
                    @if($orders->count() > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                <a href="{{ route('orders.show', $order) }}" class="block border-b pb-4 hover:text-indigo-600">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="font-semibold">Commande #{{ $order->order_number }}</h3>
                                            <p class="text-sm text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-indigo-600">{{ number_format($order->total, 2) }} €</p>
                                            <span class="text-sm px-2 py-1 rounded 
                                                @if($order->status == 'delivered') bg-green-100 text-green-800
                                                @elseif($order->status == 'shipped') bg-blue-100 text-blue-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <a href="{{ route('orders.index') }}" class="block text-center mt-6 text-indigo-600 hover:underline">
                            Voir toutes les commandes
                        </a>
                    @else
                        <p class="text-gray-600">Aucune commande pour le moment.</p>
                    @endif
                </div>
            </div>

            <!-- Onglet Panier -->
            <div x-show="activeTab === 'cart'" x-cloak class="md:block">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold mb-6">Mon Panier</h2>
                    @if($cartCount > 0)
                        <p class="text-gray-600 mb-4">Vous avez {{ $cartCount }} article(s) dans votre panier.</p>
                        <a href="{{ route('cart.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                            Voir mon panier
                        </a>
                    @else
                        <p class="text-gray-600 mb-4">Votre panier est vide.</p>
                        <a href="{{ route('products.index') }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700">
                            Découvrir les produits
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

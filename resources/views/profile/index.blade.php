@extends('layouts.app')

@section('title', 'Mon Profil - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-8" x-data="{ activeTab: 'profile' }">
    <h1 class="text-2xl font-bold mb-6">Mon Profil</h1>

    <!-- Tabs Mobile -->
    <div class="md:hidden mb-6">
        <div class="bg-white rounded-lg shadow-md p-2 flex gap-2">
            <button @click="activeTab = 'profile'" 
                    :class="activeTab === 'profile' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700'"
                    class="flex-1 px-2 py-1.5 rounded text-xs font-medium transition">
                <i class="fas fa-user mr-1"></i>Profil
            </button>
            <button @click="activeTab = 'orders'" 
                    :class="activeTab === 'orders' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700'"
                    class="flex-1 px-2 py-1.5 rounded text-xs font-medium transition">
                <i class="fas fa-box mr-1"></i>Commandes
            </button>
            <button @click="activeTab = 'favorites'" 
                    :class="activeTab === 'favorites' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700'"
                    class="flex-1 px-2 py-1.5 rounded text-xs font-medium transition">
                <i class="fas fa-heart mr-1"></i>Favoris
            </button>
            <button @click="activeTab = 'cart'" 
                    :class="activeTab === 'cart' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700'"
                    class="flex-1 px-2 py-1.5 rounded text-xs font-medium transition">
                <i class="fas fa-shopping-cart mr-1"></i>Panier
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Sidebar Desktop -->
        <aside class="hidden md:block">
            <div class="bg-white rounded-lg shadow-md p-4 sticky top-20">
                <a href="{{ route('profile.index') }}" class="block px-3 py-1.5 mb-1.5 rounded hover:bg-indigo-50 text-orange-600 text-sm font-medium">
                    <i class="fas fa-user mr-2 text-xs"></i>Mon Profil
                </a>
                <a href="{{ route('favorites.index') }}" class="block px-3 py-1.5 mb-1.5 rounded hover:bg-gray-100 text-gray-700 text-sm">
                    <i class="fas fa-heart mr-2 text-xs"></i>Mes Favoris
                    @if(isset($favoritesCount) && $favoritesCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $favoritesCount }}</span>
                    @endif
                </a>
                <a href="{{ route('orders.index') }}" class="block px-3 py-1.5 mb-1.5 rounded hover:bg-gray-100 text-gray-700 text-sm">
                    <i class="fas fa-box mr-2 text-xs"></i>Mes Commandes
                </a>
                <a href="{{ route('cart.index') }}" class="block px-3 py-1.5 rounded hover:bg-gray-100 text-gray-700 text-sm">
                    <i class="fas fa-shopping-cart mr-2 text-xs"></i>Mon Panier
                    @if($cartCount > 0)
                        <span class="ml-2 bg-orange-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $cartCount }}</span>
                    @endif
                </a>
            </div>
        </aside>

        <!-- Contenu -->
        <div class="md:col-span-3">
            <!-- Onglet Profil -->
            <div x-show="activeTab === 'profile'" x-cloak class="md:block">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-4">Informations personnelles</h2>
                    <form method="POST" action="#" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium mb-1">Nom complet</label>
                            <input type="text" value="{{ $user->name }}" disabled class="w-full px-3 py-1.5 text-sm border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled class="w-full px-3 py-1.5 text-sm border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Téléphone</label>
                            <input type="text" value="{{ $user->phone ?? '' }}" disabled class="w-full px-3 py-1.5 text-sm border rounded-lg bg-gray-50">
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Adresse</label>
                            <textarea disabled class="w-full px-3 py-1.5 text-sm border rounded-lg bg-gray-50">{{ $user->address ?? '' }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium mb-1">Rôle</label>
                            <input type="text" value="{{ ucfirst($user->roles->first()->name ?? 'Aucun rôle') }}" disabled class="w-full px-3 py-1.5 text-sm border rounded-lg bg-gray-50">
                        </div>
                    </form>
                    <div class="mt-6 pt-4 border-t border-gray-100 md:hidden">
                        <form action="{{ route('logout') }}" method="POST" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-gray-100 text-gray-700 text-sm font-semibold py-2 rounded-lg hover:bg-gray-200">
                                <i class="fas fa-right-from-bracket"></i>
                                Déconnexion
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Onglet Favoris -->
            <div x-show="activeTab === 'favorites'" x-cloak class="md:block">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-4">Mes Favoris</h2>
                    @if(isset($favoritesCount) && $favoritesCount > 0)
                        <p class="text-gray-600 mb-3 text-sm">Vous avez {{ $favoritesCount }} produit(s) dans vos favoris.</p>
                        <a href="{{ route('favorites.index') }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                            Voir mes favoris
                        </a>
                    @else
                        <p class="text-gray-600 mb-3 text-sm">Vous n'avez pas encore de favoris.</p>
                        <a href="{{ route('products.index') }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                            Découvrir les produits
                        </a>
                    @endif
                </div>
            </div>

            <!-- Onglet Commandes -->
            <div x-show="activeTab === 'orders'" x-cloak class="md:block">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-4">Mes Commandes récentes</h2>
                    @if($orders->count() > 0)
                        <div class="space-y-3">
                            @foreach($orders as $order)
                                <a href="{{ route('orders.show', $order) }}" class="block border-b pb-3 hover:text-orange-600">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <h3 class="font-semibold text-sm">Commande #{{ $order->order_number }}</h3>
                                            <p class="text-xs text-gray-600">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-orange-600 text-sm">{{ number_format($order->total, 0, ',', ' ') }} FCFA</p>
                                            <span class="text-xs px-2 py-0.5 rounded 
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
                        <a href="{{ route('orders.index') }}" class="block text-center mt-4 text-sm text-orange-600 hover:underline">
                            Voir toutes les commandes
                        </a>
                    @else
                        <p class="text-gray-600 text-sm">Aucune commande pour le moment.</p>
                    @endif
                </div>
            </div>

            <!-- Onglet Panier -->
            <div x-show="activeTab === 'cart'" x-cloak class="md:block">
                <div class="bg-white rounded-lg shadow-md p-4">
                    <h2 class="text-lg font-bold mb-4">Mon Panier</h2>
                    @if($cartCount > 0)
                        <p class="text-gray-600 mb-3 text-sm">Vous avez {{ $cartCount }} article(s) dans votre panier.</p>
                        <a href="{{ route('cart.index') }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                            Voir mon panier
                        </a>
                    @else
                        <p class="text-gray-600 mb-3 text-sm">Votre panier est vide.</p>
                        <a href="{{ route('products.index') }}" class="inline-block bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm font-medium">
                            Découvrir les produits
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

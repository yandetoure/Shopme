@extends('dashboard.layout')

@section('title', 'Détails de l\'Utilisateur - ShopMe')
@section('page-title', 'Détails de l\'Utilisateur')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ $user->name }}</h2>
            <p class="text-sm text-gray-600">Informations de l'utilisateur</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.users.edit', $user) }}" class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <a href="{{ route('admin.users.index') }}" class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Informations personnelles</h3>
            <div class="space-y-2 text-sm">
                <p><span class="font-semibold">Nom:</span> {{ $user->name }}</p>
                <p><span class="font-semibold">Email:</span> {{ $user->email }}</p>
                <p><span class="font-semibold">Téléphone:</span> {{ $user->phone ?? '-' }}</p>
                <p><span class="font-semibold">Adresse:</span> {{ $user->address ?? '-' }}</p>
                <p>
                    <span class="font-semibold">Statut:</span> 
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                </p>
                <p><span class="font-semibold">Inscrit le:</span> {{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Rôles et Permissions</h3>
            <div class="space-y-2">
                <div>
                    <span class="font-semibold text-sm">Rôles:</span>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @forelse($user->roles as $role)
                            <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-500">Aucun rôle</span>
                        @endforelse
                    </div>
                </div>
                <div>
                    <span class="font-semibold text-sm">Permissions:</span>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @forelse($user->permissions as $permission)
                            <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-800">
                                {{ $permission->name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-500">Aucune permission directe</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Commandes</h3>
            <p class="text-xs text-gray-600">Nombre total: {{ $user->orders->count() }}</p>
            <p class="text-xs text-gray-600">Total dépensé: {{ number_format($user->orders->sum('total'), 0, '', '.') }} FCFA</p>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Panier et Favoris</h3>
            <p class="text-xs text-gray-600">Articles dans le panier: {{ $user->cartItems->count() }}</p>
            <p class="text-xs text-gray-600">Produits favoris: {{ $user->favorites->count() }}</p>
        </div>
    </div>
</div>
@endsection


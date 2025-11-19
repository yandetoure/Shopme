@extends('dashboard.layout')

@section('title', 'Détails de la Catégorie - ShopMe')
@section('page-title', 'Détails de la Catégorie')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $category->name }}</h2>
            <a href="{{ route('admin.categories.edit', $category) }}" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-2"></i> Modifier
            </a>
        </div>
        <div class="space-y-4">
            <p><span class="font-semibold">Description:</span> {{ $category->description ?? '-' }}</p>
            <p><span class="font-semibold">Parent:</span> {{ $category->parent->name ?? 'Aucun' }}</p>
            <p><span class="font-semibold">Statut:</span> {{ $category->is_active ? 'Actif' : 'Inactif' }}</p>
            <p><span class="font-semibold">Produits:</span> {{ $category->products->count() }}</p>
        </div>
    </div>
</div>
@endsection


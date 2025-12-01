@extends('dashboard.layout')

@section('title', 'Détails de la Permission - ShopMe')
@section('page-title', 'Détails de la Permission')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ $permission->name }}</h2>
            <p class="text-sm text-gray-500">Informations de la permission</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.permissions.edit', $permission) }}" class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <a href="{{ route('admin.permissions.index') }}" class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3">Rôles ayant cette permission ({{ $permission->roles->count() }})</h3>
        <div class="flex flex-wrap gap-2">
            @forelse($permission->roles as $role)
            <span class="px-3 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
            </span>
            @empty
            <p class="text-xs text-gray-500">Aucun rôle n'a cette permission</p>
            @endforelse
        </div>
    </div>
</div>
@endsection







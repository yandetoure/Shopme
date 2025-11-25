@extends('dashboard.layout')

@section('title', 'Détails du Rôle - ShopMe')
@section('page-title', 'Détails du Rôle')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</h2>
            <p class="text-sm text-gray-500">{{ $role->name }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('admin.roles.edit', $role) }}" class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <a href="{{ route('admin.roles.index') }}" class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-left mr-1"></i> Retour
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Utilisateurs ({{ $role->users->count() }})</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                @forelse($role->users as $user)
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded text-xs">
                    <span>{{ $user->name }}</span>
                    <span class="text-gray-500">{{ $user->email }}</span>
                </div>
                @empty
                <p class="text-xs text-gray-500">Aucun utilisateur avec ce rôle</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-semibold text-gray-800 mb-3">Permissions ({{ $role->permissions->count() }})</h3>
            <div class="flex flex-wrap gap-2 max-h-64 overflow-y-auto">
                @forelse($role->permissions as $permission)
                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                    {{ $permission->name }}
                </span>
                @empty
                <p class="text-xs text-gray-500">Aucune permission</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection






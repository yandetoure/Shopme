@extends('dashboard.layout')

@section('title', 'Gestion des Permissions - ShopMe')
@section('page-title', 'Gestion des Permissions')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Permissions</h2>
            <p class="text-sm text-gray-600">Gérez les permissions du système</p>
        </div>
        <a href="{{ route('admin.permissions.create') }}" class="bg-orange-500 text-white px-4 py-2 text-sm rounded-lg hover:bg-orange-600 font-semibold">
            <i class="fas fa-plus mr-1"></i> Ajouter une permission
        </a>
    </div>

    @foreach($permissions as $group => $perms)
    <div class="bg-white rounded-lg shadow p-4">
        <h3 class="text-sm font-semibold text-gray-800 mb-3 uppercase">{{ $group }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($perms as $permission)
            <div class="flex items-center justify-between p-3 border rounded-lg hover:bg-gray-50">
                <div class="flex-1">
                    <span class="text-xs font-medium text-gray-900">{{ $permission->name }}</span>
                    <p class="text-xs text-gray-500 mt-1">{{ $permission->roles_count ?? 0 }} rôle(s)</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.permissions.show', $permission) }}" class="text-blue-600 hover:text-blue-900 text-xs">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-orange-600 hover:text-orange-900 text-xs">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Êtes-vous sûr ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-xs">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection





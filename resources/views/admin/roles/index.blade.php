@extends('dashboard.layout')

@section('title', 'Gestion des Rôles - ShopMe')
@section('page-title', 'Gestion des Rôles')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Rôles</h2>
            <p class="text-sm text-gray-600">Gérez les rôles du système</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" class="bg-orange-500 text-white px-4 py-2 text-sm rounded-lg hover:bg-orange-600 font-semibold">
            <i class="fas fa-plus mr-1"></i> Ajouter un rôle
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Utilisateurs</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Permissions</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($roles as $role)
                <tr>
                    <td class="px-4 py-3">
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                        <p class="text-xs text-gray-500">{{ $role->name }}</p>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $role->users_count ?? 0 }} utilisateur(s)
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $role->permissions->count() }} permission(s)
                    </td>
                    <td class="px-4 py-3 text-xs font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.roles.show', $role) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-orange-600 hover:text-orange-900" title="Modifier">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            @php
                                $defaultRoles = ['super_admin', 'admin', 'vendeur', 'client'];
                                $canDelete = !in_array($role->name, $defaultRoles);
                            @endphp
                            @if($canDelete)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Êtes-vous sûr ? Cette action supprimera également toutes les permissions associées.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-3 text-center text-xs text-gray-500">Aucun rôle</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection






@extends('dashboard.layout')

@section('title', 'Gestion des Utilisateurs - ShopMe')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Utilisateurs</h2>
            <p class="text-sm text-gray-600">Gérez les utilisateurs du système</p>
        </div>
        <a href="{{ route('admin.users.create') }}" class="bg-orange-500 text-white px-4 py-2 text-sm rounded-lg hover:bg-orange-600 font-semibold">
            <i class="fas fa-plus mr-1"></i> Ajouter un utilisateur
        </a>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email, téléphone..." 
                       class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Rôle</label>
                <select name="role" class="w-full px-3 py-1.5 text-sm border rounded-lg focus:ring-2 focus:ring-orange-500">
                    <option value="">Tous les rôles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $role->name)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-blue-600">
                    <i class="fas fa-search mr-1"></i> Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Tableau des utilisateurs -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Téléphone</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Rôles</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Inscrit le</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="text-xs font-medium text-gray-900">{{ $user->name }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $user->email }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-600">{{ $user->phone ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach($user->roles as $role)
                                    <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                                        {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-0.5 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.users.show', $user) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-orange-600 hover:text-orange-900" title="Modifier">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                @php
                                    $canToggleStatus = true;
                                    // Empêcher de désactiver le dernier super admin actif
                                    if ($user->hasRole('super_admin') && $user->is_active) {
                                        $activeSuperAdminCount = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first()->users()->where('is_active', true)->count();
                                        $canToggleStatus = $activeSuperAdminCount > 1;
                                    }
                                @endphp
                                @if($canToggleStatus)
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" 
                                            class="{{ $user->is_active ? 'text-yellow-600 hover:text-yellow-900' : 'text-green-600 hover:text-green-900' }}" 
                                            title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }} text-sm"></i>
                                    </button>
                                </form>
                                @endif
                                @php
                                    $canDelete = !$user->hasRole('super_admin');
                                    if ($user->hasRole('super_admin')) {
                                        $superAdminCount = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first()->users()->count();
                                        $canDelete = $superAdminCount > 1;
                                    }
                                @endphp
                                @if($canDelete)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
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
                        <td colspan="7" class="px-4 py-3 text-center text-xs text-gray-500">Aucun utilisateur trouvé</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-4 py-3 border-t border-gray-200 text-sm">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection


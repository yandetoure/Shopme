@extends('dashboard.layout')

@section('title', 'Gestion des Variables - ShopMe')
@section('page-title', 'Gestion des Variables')

@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Types d'attributs</h2>
            <p class="text-sm text-gray-600">Gérez les variables globales (couleurs, tailles, etc.)</p>
        </div>
        <a href="{{ route('admin.variables.create') }}" class="bg-orange-500 text-white px-4 py-2 text-sm rounded-lg hover:bg-orange-600 font-semibold">
            <i class="fas fa-plus mr-1"></i> Ajouter un type
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Valeurs</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($attributeTypes as $type)
                <tr>
                    <td class="px-4 py-3">
                        <span class="text-sm font-medium text-gray-900">{{ $type->name }}</span>
                        <p class="text-xs text-gray-500">{{ $type->slug }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-800">
                            {{ ucfirst($type->type) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        {{ $type->all_values_count ?? 0 }} valeur(s)
                    </td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-0.5 text-xs rounded-full {{ $type->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $type->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs font-medium">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.variables.show', $type) }}" class="text-blue-600 hover:text-blue-900" title="Voir">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <a href="{{ route('admin.variables.edit', $type) }}" class="text-orange-600 hover:text-orange-900" title="Modifier">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form action="{{ route('admin.variables.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('Êtes-vous sûr ? Cette action supprimera également toutes les valeurs associées.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" title="Supprimer">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-xs text-gray-500">Aucun type d'attribut</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection


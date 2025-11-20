@extends('dashboard.layout')

@section('title', 'Détails du Type d\'Attribut - ShopMe')
@section('page-title', 'Détails du Type d\'Attribut')

@section('content')
<div class="max-w-4xl mx-auto space-y-4">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $variable->name }}</h2>
                <p class="text-xs text-gray-500">{{ $variable->slug }} - {{ ucfirst($variable->type) }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.variables.edit', $variable) }}" class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                    <i class="fas fa-edit mr-1"></i> Modifier
                </a>
                <a href="{{ route('admin.variables.index') }}" class="bg-gray-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-gray-600">
                    <i class="fas fa-arrow-left mr-1"></i> Retour
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm mb-4">
            <div>
                <span class="font-semibold">Type:</span> {{ ucfirst($variable->type) }}
            </div>
            <div>
                <span class="font-semibold">Statut:</span> 
                <span class="px-2 py-0.5 text-xs rounded-full {{ $variable->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $variable->is_active ? 'Actif' : 'Inactif' }}
                </span>
            </div>
            <div>
                <span class="font-semibold">Ordre:</span> {{ $variable->sort_order }}
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-sm font-semibold text-gray-800">Valeurs ({{ $variable->allValues->count() }})</h3>
            <button onclick="document.getElementById('add-value-form').classList.toggle('hidden')" 
                class="bg-orange-500 text-white px-3 py-1.5 text-sm rounded-lg hover:bg-orange-600">
                <i class="fas fa-plus mr-1"></i> Ajouter une valeur
            </button>
        </div>

        <!-- Formulaire d'ajout de valeur -->
        <div id="add-value-form" class="hidden mb-4 p-4 bg-gray-50 rounded-lg">
            <form action="{{ route('admin.variables.values.store', $variable) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Valeur *</label>
                        <input type="text" name="value" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    @if($variable->type == 'color')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Code couleur (hex)</label>
                        <input type="color" name="color_code" 
                            class="w-full h-10 border border-gray-300 rounded-lg">
                    </div>
                    @endif
                    @if($variable->type == 'image')
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Image</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Ordre</label>
                        <input type="number" name="sort_order" value="0" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" value="1" checked
                            class="w-4 h-4 text-orange-600 border-gray-300 rounded">
                        <label class="ml-2 text-xs text-gray-700">Actif</label>
                    </div>
                </div>
                <div class="mt-3 flex justify-end space-x-2">
                    <button type="button" onclick="document.getElementById('add-value-form').classList.add('hidden')"
                        class="px-3 py-1.5 text-xs border border-gray-300 rounded-lg hover:bg-gray-50">
                        Annuler
                    </button>
                    <button type="submit" class="px-3 py-1.5 text-xs bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>

        <!-- Liste des valeurs -->
        <div class="space-y-2">
            @forelse($variable->allValues as $value)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    @if($variable->type == 'color' && $value->color_code)
                        <div class="w-8 h-8 rounded-full border border-gray-300" style="background-color: {{ $value->color_code }}"></div>
                    @endif
                    @if($variable->type == 'image' && $value->image)
                        <img src="{{ asset('storage/' . $value->image) }}" alt="{{ $value->value }}" class="w-8 h-8 object-cover rounded">
                    @endif
                    <span class="text-sm font-medium">{{ $value->value }}</span>
                    @if($value->color_code)
                        <span class="text-xs text-gray-500">{{ $value->color_code }}</span>
                    @endif
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-2 py-0.5 text-xs rounded-full {{ $value->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $value->is_active ? 'Actif' : 'Inactif' }}
                    </span>
                    <form action="{{ route('admin.variables.values.destroy', [$variable, $value]) }}" method="POST" class="inline" 
                        onsubmit="return confirm('Êtes-vous sûr ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <p class="text-xs text-gray-500 text-center py-4">Aucune valeur définie</p>
            @endforelse
        </div>
    </div>
</div>
@endsection


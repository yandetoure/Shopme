@extends('dashboard.layout')

@section('title', 'Créer un Type d\'Attribut - ShopMe')
@section('page-title', 'Créer un Type d\'Attribut')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.variables.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Ex: Couleur, Taille, Matériau">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" id="type" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="text" {{ old('type') == 'text' ? 'selected' : '' }}>Texte</option>
                        <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Couleur</option>
                        <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Image</option>
                        <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Sélection</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Ordre</label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('sort_order')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Actif</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.variables.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                    Annuler
                </a>
                <button type="submit" class="px-4 py-2 text-sm bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


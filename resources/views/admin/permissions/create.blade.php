@extends('dashboard.layout')

@section('title', 'Créer une Permission - ShopMe')
@section('page-title', 'Créer une Permission')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.permissions.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom de la permission *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Ex: view reports, manage inventory">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assigner aux rôles</label>
                    <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                        @foreach($roles as $role)
                        <label class="flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                   class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                   {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                            <span class="ml-2 text-xs text-gray-700">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('roles')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
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







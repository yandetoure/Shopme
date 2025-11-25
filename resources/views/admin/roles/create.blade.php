@extends('dashboard.layout')

@section('title', 'Créer un Rôle - ShopMe')
@section('page-title', 'Créer un Rôle')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow p-4">
        <form action="{{ route('admin.roles.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nom du rôle *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Ex: moderateur, manager">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Permissions</label>
                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-4">
                        @foreach($permissions as $group => $perms)
                        <div>
                            <h4 class="text-xs font-semibold text-gray-700 mb-2 uppercase">{{ $group }}</h4>
                            <div class="space-y-1">
                                @foreach($perms as $permission)
                                <label class="flex items-center">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                           class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                    <span class="ml-2 text-xs text-gray-700">{{ $permission->name }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @error('permissions')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
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






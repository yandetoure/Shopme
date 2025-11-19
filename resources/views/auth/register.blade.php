@extends('layouts.app')

@section('title', 'Inscription - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-3xl font-bold text-center mb-8">Inscription</h2>
        
        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium mb-2">Nom complet</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium mb-2">Mot de passe</label>
                <input type="password" id="password" name="password" required 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-sm font-medium mb-2">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-sm font-medium mb-2">Téléphone (optionnel)</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" 
                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
            </div>

            <div class="mb-6">
                <label for="address" class="block text-sm font-medium mb-2">Adresse (optionnel)</label>
                <textarea id="address" name="address" rows="3" 
                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('address') }}</textarea>
            </div>

            <button type="submit" class="w-full bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 font-semibold mb-4">
                S'inscrire
            </button>

            <p class="text-center text-gray-600">
                Déjà un compte ? 
                <a href="{{ route('login') }}" class="text-orange-600 hover:underline">Se connecter</a>
            </p>
        </form>
    </div>
</div>
@endsection

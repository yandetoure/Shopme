@extends('layouts.app')

@section('title', 'Connexion - ShopMe')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h2 class="text-3xl font-bold text-center mb-8">Connexion</h2>
        
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
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

            <div class="mb-6 flex items-center justify-between">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm text-gray-600">Se souvenir de moi</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-orange-500 text-white px-6 py-3 rounded-lg hover:bg-orange-600 font-semibold mb-4">
                Se connecter
            </button>

            <p class="text-center text-gray-600">
                Pas encore de compte ? 
                <a href="{{ route('register') }}" class="text-orange-600 hover:underline">S'inscrire</a>
            </p>
        </form>
    </div>
</div>
@endsection

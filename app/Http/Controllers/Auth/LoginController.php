<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Vérifier si le compte est actif
            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.',
                ])->onlyInput('email');
            }
            
            // Rediriger selon le rôle
            if ($user->isSuperAdmin()) {
                return redirect()->intended(route('dashboard.superadmin'));
            } elseif ($user->isAdmin()) {
                return redirect()->intended(route('dashboard.admin'));
            } elseif ($user->isVendeur()) {
                return redirect()->intended(route('dashboard.vendeur'));
            }
            
            // Par défaut, rediriger vers la page d'accueil pour les clients
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Les identifiants fournis ne correspondent pas.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}

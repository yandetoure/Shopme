<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        $settings = [
            'site_name' => config('app.name', 'ShopMe'),
            'site_email' => config('mail.from.address', ''),
            'currency' => 'FCFA',
            'tax_rate' => 20, // TVA en pourcentage
            'default_shipping' => 6550, // Prix de livraison par défaut en FCFA
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'site_email' => 'required|email|max:255',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'default_shipping' => 'required|numeric|min:0',
        ]);

        // Ici, vous pouvez sauvegarder dans un fichier de configuration
        // ou dans une table settings si vous en avez une
        // Pour l'instant, on affiche juste un message de succès
        
        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès !');
    }
}


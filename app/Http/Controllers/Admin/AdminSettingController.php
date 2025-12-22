<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    /**
     * Afficher la page des paramètres
     */
    public function index()
    {
        $settings = Setting::getSettings();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Mettre à jour les paramètres
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            // Informations générales
            'site_name' => 'required|string|max:255',
            'site_email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'slogan' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'phone' => 'nullable|string|max:50',
            'email_contact' => 'nullable|email|max:255',
            
            // Couleurs
            'navbar_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'navbar_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'primary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'secondary_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            
            // Typographie
            'font_family' => 'nullable|string|max:255',
            'heading_font' => 'nullable|string|max:255',
            
            // Paramètres financiers
            'currency' => 'nullable|string|max:10',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'default_shipping' => 'required|numeric|min:0',
            
            // Réseaux sociaux
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
        ]);

        $settings = Setting::getSettings();
        $data = $validated;

        // Gérer l'upload du logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo s'il existe
            if ($settings->logo && Storage::disk('public')->exists($settings->logo)) {
                Storage::disk('public')->delete($settings->logo);
            }
            
            // Sauvegarder le nouveau logo
            $logoPath = $request->file('logo')->store('settings', 'public');
            $data['logo'] = $logoPath;
        } else {
            // Garder l'ancien logo si aucun nouveau n'est fourni
            unset($data['logo']);
        }

        // Mettre à jour les paramètres
        $settings->update($data);

        // Vider le cache de la vue pour que les changements soient visibles immédiatement
        \Artisan::call('view:clear');

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès ! Veuillez recharger la page pour voir les changements.');
    }
}


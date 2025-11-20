<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminShippingRateController extends Controller
{
    /**
     * Afficher la liste des tarifs de livraison
     */
    public function index()
    {
        $shippingRates = ShippingRate::orderBy('sort_order')->orderBy('created_at', 'desc')->get();
        return view('admin.shipping-rates.index', compact('shippingRates'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.shipping-rates.create');
    }

    /**
     * Enregistrer un nouveau tarif
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_order_amount' => 'nullable|numeric|min:0|gt:min_order_amount',
            'is_free' => 'boolean',
            'estimated_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Générer le slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (ShippingRate::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validated['slug'] = $slug;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        ShippingRate::create($validated);

        return redirect()->route('admin.shipping-rates.index')
            ->with('success', 'Tarif de livraison créé avec succès !');
    }

    /**
     * Afficher un tarif
     */
    public function show(ShippingRate $shippingRate)
    {
        return view('admin.shipping-rates.show', compact('shippingRate'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(ShippingRate $shippingRate)
    {
        return view('admin.shipping-rates.edit', compact('shippingRate'));
    }

    /**
     * Mettre à jour un tarif
     */
    public function update(Request $request, ShippingRate $shippingRate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_order_amount' => 'nullable|numeric|min:0|gt:min_order_amount',
            'is_free' => 'boolean',
            'estimated_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Générer le slug si le nom a changé
        if ($shippingRate->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (ShippingRate::where('slug', $slug)->where('id', '!=', $shippingRate->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $shippingRate->update($validated);

        return redirect()->route('admin.shipping-rates.index')
            ->with('success', 'Tarif de livraison mis à jour avec succès !');
    }

    /**
     * Supprimer un tarif
     */
    public function destroy(ShippingRate $shippingRate)
    {
        // Vérifier si le tarif est utilisé dans des commandes
        if ($shippingRate->orders()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un tarif utilisé dans des commandes.');
        }

        $shippingRate->delete();

        return redirect()->route('admin.shipping-rates.index')
            ->with('success', 'Tarif de livraison supprimé avec succès !');
    }
}


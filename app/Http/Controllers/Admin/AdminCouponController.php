<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class AdminCouponController extends Controller
{
    /**
     * Afficher la liste des coupons
     */
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where('is_active', true);
            } elseif ($request->status == 'inactive') {
                $query->where('is_active', false);
            }
        }

        $coupons = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Enregistrer un nouveau coupon
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);
        $validated['used_count'] = 0;

        Coupon::create($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon créé avec succès !');
    }

    /**
     * Afficher un coupon
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('usages.user', 'orders');
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Mettre à jour un coupon
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:255|unique:coupons,code,' . $coupon->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'minimum_amount' => 'nullable|numeric|min:0',
            'maximum_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'is_active' => 'boolean',
        ]);

        $validated['code'] = strtoupper($validated['code']);

        $coupon->update($validated);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon mis à jour avec succès !');
    }

    /**
     * Supprimer un coupon
     */
    public function destroy(Coupon $coupon)
    {
        // Vérifier si le coupon est utilisé dans des commandes
        if ($coupon->orders()->count() > 0) {
            return back()->with('error', 'Impossible de supprimer un coupon utilisé dans des commandes.');
        }

        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon supprimé avec succès !');
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with('children')
            ->orderBy('sort_order')
            ->limit(6)
            ->get();

        $featuredProducts = Product::active()
            ->featured()
            ->limit(8)
            ->get();

        $onSaleProducts = Product::active()
            ->onSale()
            ->limit(8)
            ->get();

        $latestProducts = Product::active()
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Récupérer les IDs des favoris si l'utilisateur est connecté
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        }

        return view('home', compact('categories', 'featuredProducts', 'onSaleProducts', 'latestProducts', 'favoriteIds'));
    }
}

<?php declare(strict_types=1); 

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

        // Produits par catégories populaires (pour les sections)
        $categoryProducts = [];
        $popularCategories = Category::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        foreach ($popularCategories as $category) {
            $products = Product::whereHas('categories', function ($query) use ($category) {
                $categoryIds = [$category->id];
                $categoryIds = array_merge($categoryIds, $category->children->pluck('id')->toArray());
                $query->whereIn('categories.id', $categoryIds);
            })
            ->active()
            ->onSale()
            ->limit(6)
            ->get();

            if ($products->count() > 0) {
                $categoryProducts[$category->slug] = [
                    'category' => $category,
                    'products' => $products
                ];
            }
        }

        // Produits vedettes pour la section hero carousel (3 produits avec les plus grandes promotions)
        $heroProducts = Product::active()
            ->onSale()
            ->whereNotNull('image')
            ->orderByRaw('((price - sale_price) / price) DESC')
            ->limit(3)
            ->get();

        // Produits pour ventes flash (carousel horizontal)
        $flashSaleProducts = Product::active()
            ->onSale()
            ->whereNotNull('image')
            ->orderByRaw('((price - sale_price) / price) DESC')
            ->limit(10)
            ->get();

        // Récupérer les IDs des favoris si l'utilisateur est connecté
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        }

        return view('home', compact('categories', 'featuredProducts', 'onSaleProducts', 'latestProducts', 'categoryProducts', 'heroProducts', 'flashSaleProducts', 'favoriteIds'));
    }
}

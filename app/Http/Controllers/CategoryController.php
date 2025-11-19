<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with(['parent', 'children'])
            ->firstOrFail();

        $products = Product::where('category_id', $category->id)
            ->active()
            ->paginate(12);

        // Récupérer les IDs des favoris si l'utilisateur est connecté
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        }

        return view('category.show', compact('category', 'products', 'favoriteIds'));
    }

    public function subcategory($parentSlug, $slug)
    {
        $parentCategory = Category::where('slug', $parentSlug)
            ->where('is_active', true)
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }])
            ->firstOrFail();
        
        $category = Category::where('slug', $slug)
            ->where('parent_id', $parentCategory->id)
            ->where('is_active', true)
            ->with(['parent', 'children'])
            ->firstOrFail();

        // Récupérer les produits avec tri optionnel
        $query = Product::where('category_id', $category->id)->active();
        
        // Appliquer le tri
        $sort = request('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('sale_price', 'asc')->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('sale_price', 'desc')->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->orderBy('sales_count', 'desc')->orderBy('views', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12);

        // Récupérer les IDs des favoris si l'utilisateur est connecté
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        }

        return view('category.subcategory', compact('category', 'products', 'favoriteIds', 'parentCategory'));
    }
}

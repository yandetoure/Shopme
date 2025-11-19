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
        $parentCategory = Category::where('slug', $parentSlug)->firstOrFail();
        
        $category = Category::where('slug', $slug)
            ->where('parent_id', $parentCategory->id)
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
}

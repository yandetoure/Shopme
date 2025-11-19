<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

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

        return view('category.show', compact('category', 'products'));
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

        return view('category.show', compact('category', 'products'));
    }
}

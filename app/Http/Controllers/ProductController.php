<?php declare(strict_types=1); 

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with('category');

        // Filtres
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_low':
                    $query->orderByRaw('CASE WHEN is_on_sale AND sale_price IS NOT NULL THEN sale_price ELSE price END ASC');
                    break;
                case 'price_high':
                    $query->orderByRaw('CASE WHEN is_on_sale AND sale_price IS NOT NULL THEN sale_price ELSE price END DESC');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('sales_count', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        $categories = Category::whereNull('parent_id')->where('is_active', true)->get();

        // Récupérer les IDs des favoris si l'utilisateur est connecté
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = Auth::user()->favorites()->pluck('product_id')->toArray();
        }

        return view('products.index', compact('products', 'categories', 'favoriteIds'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with('category')
            ->firstOrFail();

        // Incrémenter les vues
        $product->increment('views');

        // Produits similaires
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->limit(4)
            ->get();

        // Vérifier si le produit est en favoris
        $isFavorite = false;
        if (Auth::check()) {
            $isFavorite = Auth::user()->favorites()->where('product_id', $product->id)->exists();
        }

        return view('products.show', compact('product', 'relatedProducts', 'isFavorite'));
    }
}

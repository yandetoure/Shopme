<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index()
    {
        $favorites = Auth::user()->favorites()->with('product')->paginate(12);
        return view('favorites.index', compact('favorites'));
    }

    public function add($productId)
    {
        $product = Product::findOrFail($productId);

        // Vérifier si déjà en favoris
        $existingFavorite = Favorite::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existingFavorite) {
            return back()->with('info', 'Ce produit est déjà dans vos favoris.');
        }

        Favorite::create([
            'user_id' => Auth::id(),
            'product_id' => $productId,
        ]);

        return back()->with('success', 'Produit ajouté aux favoris.');
    }

    public function remove($productId)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->firstOrFail();

        $favorite->delete();

        return back()->with('success', 'Produit retiré des favoris.');
    }

    public function toggle($productId)
    {
        $favorite = Favorite::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorite = false;
        } else {
            Favorite::create([
                'user_id' => Auth::id(),
                'product_id' => $productId,
            ]);
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'isFavorite' => $isFavorite,
            'message' => $isFavorite ? 'Produit ajouté aux favoris.' : 'Produit retiré des favoris.'
        ]);
    }
}

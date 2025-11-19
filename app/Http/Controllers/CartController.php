<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with(['product', 'variation'])->get();
        $total = $cartItems->sum(function($item) {
            return $item->total;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $variationId = $request->input('variation_id');
        $selectedAttributes = json_decode($request->input('selected_attributes', '{}'), true);
        
        // Déterminer le prix et le stock selon la variation ou le produit
        $variation = null;
        $stockQuantity = $product->stock_quantity;
        $price = $product->display_price;
        
        if ($variationId) {
            $variation = ProductVariation::where('id', $variationId)
                ->where('product_id', $product->id)
                ->firstOrFail();
            
            if (!$variation->in_stock || !$variation->stock_quantity) {
                return back()->with('error', 'Cette variation n\'est pas disponible.');
            }
            
            $stockQuantity = $variation->stock_quantity;
            $price = $variation->display_price;
        }
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $stockQuantity
        ]);

        if (!$product->in_stock || $product->status !== 'active') {
            return back()->with('error', 'Ce produit n\'est pas disponible.');
        }

        // Rechercher un article du panier avec le même produit et la même variation
        $cartItemQuery = CartItem::where('user_id', Auth::id())
            ->where('product_id', $product->id);
        
        if ($variationId) {
            $cartItemQuery->where('variation_id', $variationId);
        } else {
            $cartItemQuery->whereNull('variation_id');
        }
        
        $cartItem = $cartItemQuery->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $stockQuantity) {
                return back()->with('error', 'Quantité en stock insuffisante.');
            }
            $cartItem->quantity = $newQuantity;
        } else {
            $cartItem = new CartItem([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'variation_id' => $variationId,
                'selected_attributes' => !empty($selectedAttributes) ? $selectedAttributes : null,
                'quantity' => $request->quantity,
                'price' => $price,
            ]);
        }

        $cartItem->price = $price;
        $cartItem->save();

        return back()->with('success', 'Produit ajouté au panier avec succès.');
    }

    public function update(Request $request, $cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        // Déterminer le stock selon la variation ou le produit
        $stockQuantity = $cartItem->variation 
            ? $cartItem->variation->stock_quantity 
            : $cartItem->product->stock_quantity;
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $stockQuantity
        ]);

        $cartItem->quantity = $request->quantity;
        
        // Mettre à jour le prix selon la variation ou le produit
        if ($cartItem->variation) {
            $cartItem->price = $cartItem->variation->display_price;
        } else {
            $cartItem->price = $cartItem->product->display_price;
        }
        
        $cartItem->save();

        return back()->with('success', 'Panier mis à jour avec succès.');
    }

    public function remove($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();

        return back()->with('success', 'Produit retiré du panier.');
    }
}

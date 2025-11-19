<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->total;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock_quantity
        ]);

        if (!$product->in_stock || $product->status !== 'active') {
            return back()->with('error', 'Ce produit n\'est pas disponible.');
        }

        $cartItem = CartItem::firstOrNew([
            'user_id' => Auth::id(),
            'product_id' => $product->id
        ]);

        if ($cartItem->exists) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', 'Quantité en stock insuffisante.');
            }
            $cartItem->quantity = $newQuantity;
        } else {
            $cartItem->quantity = $request->quantity;
        }

        $cartItem->price = $product->display_price;
        $cartItem->save();

        return back()->with('success', 'Produit ajouté au panier avec succès.');
    }

    public function update(Request $request, $cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        
        if ($cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock_quantity
        ]);

        $cartItem->quantity = $request->quantity;
        $cartItem->price = $cartItem->product->display_price;
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

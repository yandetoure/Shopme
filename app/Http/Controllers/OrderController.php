<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function checkout()
    {
        $cartItems = Auth::user()->cartItems()->with('product')->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->total;
        });
        $tax = $subtotal * 0.20;
        $shipping = 10.00;
        $total = $subtotal + $tax + $shipping;

        return view('orders.checkout', compact('cartItems', 'subtotal', 'tax', 'shipping', 'total'));
    }

    public function index()
    {
        $orders = Auth::user()->orders()->with('items')->orderBy('created_at', 'desc')->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product');
        return view('orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_phone' => 'required|string|max:255',
            'shipping_address' => 'required|string',
            'shipping_city' => 'nullable|string|max:255',
            'shipping_postal_code' => 'nullable|string|max:255',
            'shipping_country' => 'nullable|string|max:255',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $cartItems = Auth::user()->cartItems()->with('product')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Votre panier est vide.');
        }

        DB::beginTransaction();
        try {
            // Calculer les totaux
            $subtotal = $cartItems->sum(function($item) {
                return $item->total;
            });
            $tax = $subtotal * 0.20; // 20% de TVA (à ajuster)
            $shipping = 10.00; // Frais de livraison fixes (à ajuster)
            $total = $subtotal + $tax + $shipping;

            // Créer la commande
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'shipping_name' => $request->shipping_name,
                'shipping_phone' => $request->shipping_phone,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country' => $request->shipping_country,
                'notes' => $request->notes,
            ]);

            // Créer les éléments de commande
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->total,
                ]);

                // Décrémenter le stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
                $cartItem->product->increment('sales_count', $cartItem->quantity);

                // Vérifier si le stock est épuisé
                if ($cartItem->product->stock_quantity <= 0) {
                    $cartItem->product->update(['in_stock' => false]);
                }
            }

            // Vider le panier
            Auth::user()->cartItems()->delete();

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Votre commande a été passée avec succès !');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue lors de la création de la commande.');
        }
    }
}

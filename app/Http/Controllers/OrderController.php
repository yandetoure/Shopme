<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\ShippingRate;
use App\Models\Coupon;
use App\Models\CouponUsage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout()
    {
        $cartItems = Auth::user()->cartItems()->with(['product', 'variation'])->get();
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->total;
        });
        $tax = $subtotal * 0.20;
        
        // Calculer le poids total pour la livraison
        $totalWeight = $cartItems->sum(function($item) {
            return ($item->product->weight ?? 0) * $item->quantity * 1000; // En grammes
        });
        
        // Obtenir le tarif de livraison
        $shippingRate = null;
        $shipping = 6550; // Par défaut 6550 FCFA
        
        if (request()->has('shipping_rate_id') && request()->shipping_rate_id) {
            $shippingRate = ShippingRate::find(request()->shipping_rate_id);
        }
        
        if (!$shippingRate) {
            $shippingRate = ShippingRate::getRateForAmount($subtotal) ?? ShippingRate::getDefaultRate();
        }
        
        if ($shippingRate) {
            $shipping = $shippingRate->is_free ? 0 : $shippingRate->price;
        }

        $discount = 0;
        $coupon = null;
        $discountAmount = $subtotal + $tax; // Montant après réduction avant livraison

        // Vérifier si un code promo est fourni
        if (request()->has('coupon_code') && request()->coupon_code) {
            $coupon = Coupon::findByCode(request()->coupon_code);
            if ($coupon) {
                $validation = $coupon->isValid($subtotal, Auth::id());
                if ($validation['valid']) {
                    $discount = $coupon->calculateDiscount($subtotal + $tax);
                    $discountAmount = ($subtotal + $tax) - $discount;
                }
            }
        }

        $total = max(0, $discountAmount + $shipping); // Total final

        $shippingRates = ShippingRate::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('orders.checkout', compact('cartItems', 'subtotal', 'tax', 'shipping', 'discount', 'discountAmount', 'total', 'coupon', 'shippingRate', 'shippingRates'));
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
            'shipping_rate_id' => 'nullable|exists:shipping_rates,id',
            'coupon_code' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $cartItems = Auth::user()->cartItems()->with(['product', 'variation'])->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Votre panier est vide.');
        }

        DB::beginTransaction();
        try {
            // Calculer les totaux
            $subtotal = $cartItems->sum(function($item) {
                return $item->total;
            });
            $tax = $subtotal * 0.20; // 20% de TVA
            
            // Calculer le poids total
            $totalWeight = $cartItems->sum(function($item) {
                return ($item->product->weight ?? 0) * $item->quantity * 1000; // En grammes
            });
            
            // Obtenir le tarif de livraison
            $shippingRate = null;
            $shipping = 6550; // Par défaut
            
            if ($request->shipping_rate_id) {
                $shippingRate = ShippingRate::findOrFail($request->shipping_rate_id);
                if ($shippingRate->is_free) {
                    $shipping = 0;
                } else {
                    $shipping = $shippingRate->price;
                }
            } else {
                $shippingRate = ShippingRate::getRateForAmount($subtotal) ?? ShippingRate::getDefaultRate();
                if ($shippingRate) {
                    $shipping = $shippingRate->is_free ? 0 : $shippingRate->price;
                }
            }
            
            // Gérer le coupon
            $coupon = null;
            $discount = 0;
            $discountAmount = $subtotal + $tax;
            
            if ($request->coupon_code) {
                $coupon = Coupon::findByCode($request->coupon_code);
                if ($coupon) {
                    $validation = $coupon->isValid($subtotal, Auth::id());
                    if ($validation['valid']) {
                        $discount = $coupon->calculateDiscount($subtotal + $tax);
                        $discountAmount = max(0, ($subtotal + $tax) - $discount);
                    } else {
                        return back()->with('error', $validation['message']);
                    }
                } else {
                    return back()->with('error', 'Code promo invalide.');
                }
            }
            
            $total = max(0, $discountAmount + $shipping);

            // Créer la commande
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'shipping_rate_id' => $shippingRate?->id,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'discount' => $discount,
                'discount_amount' => $discountAmount,
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

            // Enregistrer l'utilisation du coupon
            if ($coupon) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'discount_amount' => $discount,
                ]);
                
                // Incrémenter le compteur d'utilisation
                $coupon->increment('used_count');
            }

            // Créer les éléments de commande
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'variation_id' => $cartItem->variation_id,
                    'selected_attributes' => $cartItem->selected_attributes,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->variation && $cartItem->variation->sku 
                        ? $cartItem->variation->sku 
                        : $cartItem->product->sku,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'total' => $cartItem->total,
                ]);

                // Décrémenter le stock (produit ou variation)
                if ($cartItem->variation) {
                    $cartItem->variation->decrement('stock_quantity', $cartItem->quantity);
                    if ($cartItem->variation->stock_quantity <= 0) {
                        $cartItem->variation->update(['in_stock' => false]);
                    }
                } else {
                    $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
                    $cartItem->product->increment('sales_count', $cartItem->quantity);
                    if ($cartItem->product->stock_quantity <= 0) {
                        $cartItem->product->update(['in_stock' => false]);
                    }
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

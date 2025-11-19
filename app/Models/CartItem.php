<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le produit
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculer le total de l'élément
     */
    public function getTotalAttribute()
    {
        return $this->quantity * $this->price;
    }

    /**
     * Augmenter la quantité
     */
    public function incrementQuantity(int $amount = 1)
    {
        $this->increment('quantity', $amount);
        $this->price = $this->product->display_price;
        $this->save();
    }
}

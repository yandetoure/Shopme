<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'variation_id',
        'selected_attributes',
        'quantity',
        'price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'selected_attributes' => 'array',
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
     * Relation avec la variation
     */
    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
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

<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'attributes',
        'price',
        'sale_price',
        'is_on_sale',
        'stock_quantity',
        'in_stock',
        'image',
        'sort_order',
    ];

    protected $casts = [
        'attributes' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_on_sale' => 'boolean',
        'in_stock' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relation avec le produit
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Obtenir le prix à afficher
     */
    public function getDisplayPriceAttribute()
    {
        return $this->is_on_sale && $this->sale_price ? $this->sale_price : ($this->price ?? $this->product->price);
    }

    /**
     * Trouver une variation par ses attributs
     */
    public static function findByAttributes(int $productId, array $attributes): ?self
    {
        return self::where('product_id', $productId)
            ->where('attributes', json_encode($attributes, JSON_UNESCAPED_UNICODE))
            ->first();
    }
}

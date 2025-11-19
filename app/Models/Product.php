<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'category_id', // Catégorie principale (pour compatibilité)
        'price',
        'sale_price',
        'is_on_sale',
        'stock_quantity',
        'in_stock',
        'image',
        'images',
        'status',
        'featured',
        'weight',
        'attributes',
        'views',
        'sales_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_on_sale' => 'boolean',
        'in_stock' => 'boolean',
        'featured' => 'boolean',
        'images' => 'array',
        'attributes' => 'array',
        'weight' => 'decimal:2',
    ];

    /**
     * Relation avec la catégorie principale (pour compatibilité)
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relation many-to-many avec les catégories
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product')
            ->withTimestamps();
    }

    /**
     * Relation avec les éléments du panier
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relation avec les éléments de commande
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relation avec les favoris
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Relation avec les attributs
     */
    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class)->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Relation avec les variations
     */
    public function variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class)->orderBy('sort_order');
    }

    /**
     * Obtenir le prix à afficher (prix de vente ou prix normal)
     */
    public function getDisplayPriceAttribute()
    {
        return $this->is_on_sale && $this->sale_price ? $this->sale_price : $this->price;
    }

    /**
     * Calculer le pourcentage de réduction
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->is_on_sale || !$this->sale_price) {
            return 0;
        }
        return round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * Scope pour les produits actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les produits en promotion
     */
    public function scopeOnSale($query)
    {
        return $query->where('is_on_sale', true);
    }

    /**
     * Scope pour les produits en vedette
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}

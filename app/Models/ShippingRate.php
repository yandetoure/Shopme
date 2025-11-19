<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingRate extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'min_order_amount',
        'max_order_amount',
        'is_free',
        'estimated_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_order_amount' => 'decimal:2',
        'is_free' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'estimated_days' => 'integer',
    ];

    /**
     * Relation avec les commandes
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Obtenir le tarif de livraison selon le montant de la commande
     */
    public static function getRateForAmount(float $amount): ?self
    {
        return self::where('is_active', true)
            ->where(function($query) use ($amount) {
                $query->whereNull('min_order_amount')
                      ->orWhere('min_order_amount', '<=', $amount);
            })
            ->where(function($query) use ($amount) {
                $query->whereNull('max_order_amount')
                      ->orWhere('max_order_amount', '>=', $amount);
            })
            ->orderBy('sort_order')
            ->orderBy('price')
            ->first();
    }

    /**
     * Obtenir le tarif de livraison par défaut
     */
    public static function getDefaultRate(): ?self
    {
        return self::where('is_active', true)
            ->orderBy('sort_order')
            ->first();
    }

    /**
     * Scope pour les tarifs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'usage_limit_per_user',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_limit_per_user' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec les commandes
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relation avec les utilisations
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Vérifier si le coupon est valide
     */
    public function isValid(float $orderAmount = 0, ?int $userId = null): array
    {
        // Vérifier si actif
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Ce code promo n\'est plus actif.'];
        }

        // Vérifier les dates
        $now = Carbon::now();
        if ($this->valid_from && $now->lt($this->valid_from)) {
            return ['valid' => false, 'message' => 'Ce code promo n\'est pas encore valide.'];
        }
        if ($this->valid_until && $now->gt($this->valid_until)) {
            return ['valid' => false, 'message' => 'Ce code promo a expiré.'];
        }

        // Vérifier le montant minimum
        if ($this->minimum_amount && $orderAmount < $this->minimum_amount) {
            return ['valid' => false, 'message' => 'Le montant minimum de commande est de ' . number_format($this->minimum_amount, 0, ',', ' ') . ' FCFA.'];
        }

        // Vérifier la limite d'utilisation totale
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Ce code promo a atteint sa limite d\'utilisation.'];
        }

        // Vérifier la limite par utilisateur
        if ($userId && $this->usage_limit_per_user) {
            $userUsageCount = $this->usages()->where('user_id', $userId)->count();
            if ($userUsageCount >= $this->usage_limit_per_user) {
                return ['valid' => false, 'message' => 'Vous avez déjà utilisé ce code promo le nombre maximum de fois autorisé.'];
            }
        }

        return ['valid' => true, 'message' => 'Code promo valide.'];
    }

    /**
     * Calculer le montant de la remise
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->type === 'percentage') {
            $discount = ($orderAmount * $this->value) / 100;
            // Appliquer la remise maximum si spécifiée
            if ($this->maximum_discount && $discount > $this->maximum_discount) {
                $discount = $this->maximum_discount;
            }
            return round($discount / 100) * 100; // Arrondir aux centaines
        } else {
            // Montant fixe
            $discount = $this->value;
            // La remise ne peut pas dépasser le montant de la commande
            return $discount > $orderAmount ? $orderAmount : round($discount / 100) * 100;
        }
    }

    /**
     * Trouver un coupon par code
     */
    public static function findByCode(string $code): ?self
    {
        return self::where('code', strtoupper($code))->first();
    }
}

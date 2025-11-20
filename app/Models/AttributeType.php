<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AttributeType extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Générer automatiquement le slug à partir du nom
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attributeType) {
            if (empty($attributeType->slug)) {
                $attributeType->slug = Str::slug($attributeType->name);
            }
        });

        static::updating(function ($attributeType) {
            if ($attributeType->isDirty('name') && empty($attributeType->slug)) {
                $attributeType->slug = Str::slug($attributeType->name);
            }
        });
    }

    /**
     * Relation avec les valeurs d'attribut
     */
    public function values(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_type_id')->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Toutes les valeurs (actives et inactives)
     */
    public function allValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_type_id')->orderBy('sort_order');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'parent_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relation avec la catégorie parent
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relation avec les sous-catégories
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Relation many-to-many avec les produits
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'category_product')
            ->withTimestamps();
    }

    /**
     * Récupérer tous les IDs des sous-catégories (récursif)
     */
    public function getAllChildrenIds(): array
    {
        $ids = [$this->id];
        // Charger les enfants si pas déjà chargés
        if (!$this->relationLoaded('children')) {
            $this->load('children');
        }
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllChildrenIds());
        }
        return $ids;
    }

    /**
     * Récupérer tous les produits de cette catégorie et de ses sous-catégories
     */
    public function getAllProducts()
    {
        $categoryIds = $this->getAllChildrenIds();
        return Product::whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('categories.id', $categoryIds);
        })->active();
    }
}

<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_type_id',
        'value',
        'color_code',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relation avec le type d'attribut
     */
    public function attributeType(): BelongsTo
    {
        return $this->belongsTo(AttributeType::class, 'attribute_type_id');
    }
}


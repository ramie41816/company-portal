<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'parent_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    // Main item belongs to a quotation
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    // A main item has many child sub-items
    public function subItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class, 'parent_id');
    }

    // A sub-item belongs to a parent main item
    public function parent(): BelongsTo
    {
        return $this->belongsTo(QuotationItem::class, 'parent_id');
    }
}

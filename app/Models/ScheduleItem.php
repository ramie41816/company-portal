<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleItem extends Model
{
    protected $fillable = [
        'schedule_id',
        'parent_id',
        'description',
        'quantity',
        'unit_price',
        'total',
    ];

    // Main item belongs to a schedule
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    // A main item has many child sub-items
    public function subItems(): HasMany
    {
        return $this->hasMany(ScheduleItem::class, 'parent_id');
    }

    // A sub-item belongs to a parent main item
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ScheduleItem::class, 'parent_id');
    }
}

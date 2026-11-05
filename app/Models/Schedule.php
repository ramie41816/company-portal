<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_id',
        'schedule_number',
        'schedule_date',
        'start_date',
        'end_date',
        'reference_project',
        'prepared_by',
        'other_info',
        'quoted_to',
        'client_company',
        'client_address',
        'client_phone',
        'attention',
        'contact_email',
        'status',
        'remarks',
        'status_modified_by',
        'status_modified_at',
        'created_by',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'datetime',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'status_modified_at' => 'datetime',
        ];
    }

    // Optional link back to quotation if originated from one
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    // A schedule has many line items
    public function items(): HasMany
    {
        return $this->hasMany(ScheduleItem::class);
    }
}

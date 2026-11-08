<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'quotation_number',
        'quotation_date',
        'valid_until',
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
            'quotation_date' => 'date',
            'valid_until' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::deleted(function (Quotation $quotation) {
            if (method_exists($quotation, 'isForceDeleting') && !$quotation->isForceDeleting()) {
                \App\Models\Schedule::where('quotation_id', $quotation->id)
                    ->each(function ($schedule) {
                        $schedule->delete();
                    });
            }
        });

        static::restored(function (Quotation $quotation) {
            \App\Models\Schedule::onlyTrashed()
                ->where('quotation_id', $quotation->id)
                ->each(function ($schedule) {
                    $schedule->restore();
                });
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'quotation_id');
    }
}

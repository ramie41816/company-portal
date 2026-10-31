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

    // A quotation has many items
    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }
}

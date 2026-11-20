<?php

namespace App\Models;

use App\Services\PortalService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'reference_id',
        'name',
        'phone',
        'email',
        'service_type',
        'address',
        'notes',
        'status',
        'ip_address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Inquiry $inquiry) {
            if (empty($inquiry->reference_id)) {
                $inquiry->reference_id = static::generateReferenceId();
            }
        });

        static::saved(fn() => PortalService::clearPendingInquiriesCache());
        static::deleted(fn() => PortalService::clearPendingInquiriesCache());
    }

    public static function generateReferenceId(): string
    {
        $prefix = 'INQ-' . now()->format('Ymd') . '-';

        do {
            $code = $prefix . strtoupper(Str::random(5));
        } while (static::withTrashed()->where('reference_id', $code)->exists());

        return $code;
    }
}

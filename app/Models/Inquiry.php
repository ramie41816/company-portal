<?php

namespace App\Models;

use App\Services\PortalService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'service_type',
        'address',
        'notes',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saved(fn() => PortalService::clearPendingInquiriesCache());
        static::deleted(fn() => PortalService::clearPendingInquiriesCache());
    }
}

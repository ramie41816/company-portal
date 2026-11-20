<?php

namespace App\Services;

use App\Models\Inquiry;
use Illuminate\Support\Facades\Cache;

class PortalService
{
    public const PENDING_INQUIRIES_CACHE_KEY = 'inquiries.pending_count';

    public static function getPendingInquiriesCount(int $ttlSeconds = 60): int
    {
        return Cache::remember(self::PENDING_INQUIRIES_CACHE_KEY, $ttlSeconds, function () {
            return Inquiry::where('status', 'pending')->count();
        });
    }

    public static function clearPendingInquiriesCache(): void
    {
        Cache::forget(self::PENDING_INQUIRIES_CACHE_KEY);
    }
}

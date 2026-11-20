<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceApiController;
use App\Http\Controllers\Api\InquiryApiController;

Route::prefix('v1')->group(function () {
    // Public Services Fetching (Read-Only)
    Route::get('/services', [ServiceApiController::class, 'index']);

    // Protected Inquiry Submission
    Route::middleware(['api.key', 'throttle:5,1'])->group(function () {
        Route::post('/inquiries', [InquiryApiController::class, 'store']);
    });
});

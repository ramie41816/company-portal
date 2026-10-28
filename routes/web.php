<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'home' : 'login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    $loginRoute = Route::post('/login', [LoginController::class, 'login']);

    if (app()->environment('production')) {
        $loginRoute->middleware('throttle:5,1');
    }
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/home', fn () => view('home'))->name('home');

    // Quotations Routes
    Route::get('quotations/dashboard', [QuotationController::class, 'dashboard'])->name('quotations.dashboard');
    Route::get('quotations/archives', [QuotationController::class, 'archives'])->name('quotations.archives');
    Route::patch('quotations/{id}/restore', [QuotationController::class, 'restore'])->name('quotations.restore');
    Route::delete('quotations/{id}/force-delete', [QuotationController::class, 'forceDelete'])->name('quotations.force-delete');
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
    Route::post('/quotations/{quotation}/send', [QuotationController::class, 'sendEmail'])->name('quotations.send');

    // User Management Routes
    Route::get('users/archives', [UserController::class, 'archives'])->name('users.archives');
    Route::put('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.force-delete');
    Route::put('users/{user}/reset-password', [UserController::class, 'sendTemporaryPassword'])
        ->name('users.reset-password');
    Route::resource('users', UserController::class);

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

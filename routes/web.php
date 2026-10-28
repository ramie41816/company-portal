<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'home' : 'login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/home', fn () => view('home'))->name('home');

    // Quotations Routes
    Route::get('quotations/archives', [QuotationController::class, 'archives'])->name('quotations.archives');
    Route::patch('quotations/{id}/restore', [QuotationController::class, 'restore'])->name('quotations.restore');
    Route::delete('quotations/{id}/force-delete', [QuotationController::class, 'forceDelete'])->name('quotations.force-delete');
    Route::resource('quotations', QuotationController::class);
    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])->name('quotations.pdf');
    Route::post('/quotations/{quotation}/send', [QuotationController::class, 'sendEmail'])->name('quotations.send');

    // User Management Routes
    Route::get('users/archives', [UserController::class, 'archives'])->name('users.archives');
    Route::resource('users', UserController::class);
});

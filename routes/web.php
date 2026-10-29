<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RoleController;
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

    // --- Quotations Individual Route Security ---
    Route::get('quotations', [QuotationController::class, 'dashboard'])
        ->middleware('permission:quotations.dashboard')->name('quotations.dashboard');

    Route::get('quotations/archives', [QuotationController::class, 'archives'])
        ->middleware('permission:quotations.archives')->name('quotations.archives');
    Route::patch('quotations/{id}/restore', [QuotationController::class, 'restore'])
        ->middleware('permission:quotations.restore')->name('quotations.restore');

    Route::delete('quotations/{id}/force-delete', [QuotationController::class, 'forceDelete'])
        ->middleware('permission:quotations.force-delete')->name('quotations.force-delete');

    Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'generatePDF'])
        ->middleware('permission:quotations.pdf')->name('quotations.pdf');

    Route::post('/quotations/{quotation}/send', [QuotationController::class, 'sendEmail'])
        ->middleware('permission:quotations.send')->name('quotations.send');

    // Quotation Resource Actions individually mapped
    Route::get('quotations/list', [QuotationController::class, 'index'])
        ->middleware('permission:quotations.index')->name('quotations.index');
    Route::get('quotations/create', [QuotationController::class, 'create'])
        ->middleware('permission:quotations.create')->name('quotations.create');
    Route::post('quotations', [QuotationController::class, 'store'])
        ->middleware('permission:quotations.store')->name('quotations.store');
    Route::get('quotations/{quotation}', [QuotationController::class, 'show'])
        ->middleware('permission:quotations.show')->name('quotations.show');
    Route::get('quotations/{quotation}/edit', [QuotationController::class, 'edit'])
        ->middleware('permission:quotations.edit')->name('quotations.edit');
    Route::put('quotations/{quotation}', [QuotationController::class, 'update'])
        ->middleware('permission:quotations.update')->name('quotations.update');
    Route::delete('quotations/{quotation}', [QuotationController::class, 'destroy'])
        ->middleware('permission:quotations.destroy')->name('quotations.destroy');

    // --- User Management Individual Route Security ---
    Route::get('users', [UserController::class, 'dashboard'])
        ->middleware('permission:users.dashboard')->name('users.dashboard');

    Route::get('users/list', [UserController::class, 'index'])
        ->middleware('permission:users.index')->name('users.index');

    Route::get('users/archives', [UserController::class, 'archives'])
        ->middleware('permission:users.archives')->name('users.archives');

    Route::put('users/{id}/restore', [UserController::class, 'restore'])
        ->middleware('permission:users.restore')->name('users.restore');

    Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])
        ->middleware('permission:users.force-delete')->name('users.force-delete');

    Route::put('users/{user}/reset-password', [UserController::class, 'sendTemporaryPassword'])
        ->middleware('permission:users.reset-password')->name('users.reset-password');

    // User Resource Actions individually mapped
    Route::get('users/create', [UserController::class, 'create'])
        ->middleware('permission:users.create')->name('users.create');
    Route::post('users', [UserController::class, 'store'])
        ->middleware('permission:users.store')->name('users.store');
    Route::get('users/{user}', [UserController::class, 'show'])
        ->middleware('permission:users.show')->name('users.show');
    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.edit')->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('permission:users.update')->name('users.update');
    Route::delete('users/{user}', [UserController::class, 'destroy'])
        ->middleware('permission:users.destroy')->name('users.destroy');

    // --- Profile Routes ---
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- Role & Permission Management Individual Route Security ---
    Route::get('roles/list', [RoleController::class, 'index'])
        ->middleware('permission:roles.index')->name('roles.index');
    Route::get('roles/create', [RoleController::class, 'create'])
        ->middleware('permission:roles.create')->name('roles.create');
    Route::post('roles', [RoleController::class, 'store'])
        ->middleware('permission:roles.store')->name('roles.store');
    Route::get('roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('permission:roles.edit')->name('roles.edit');
    Route::put('roles/{role}', [RoleController::class, 'update'])
        ->middleware('permission:roles.update')->name('roles.update');
    Route::delete('roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('permission:roles.destroy')->name('roles.destroy');
});

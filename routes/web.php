<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// --- Root Redirect ---
Route::get('/', fn() => redirect()->route(auth()->check() ? 'home' : 'login'));

// --- Guest Routes ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');

    $loginRoute = Route::post('/login', [LoginController::class, 'login']);

    if (app()->environment('production')) {
        $loginRoute->middleware('throttle:5,1');
    }
});

// --- Authenticated Routes ---
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/home', fn() => view('home'))->name('home');

    // --- Profile Routes ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // --- Quotations Module ---
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::get('/', [QuotationController::class, 'dashboard'])
            ->middleware('permission:quotations.dashboard')->name('dashboard');
        Route::get('/archives', [QuotationController::class, 'archives'])
            ->middleware('permission:quotations.archives')->name('archives');
        Route::patch('/{id}/restore', [QuotationController::class, 'restore'])
            ->middleware('permission:quotations.restore')->name('restore');
        Route::delete('/{id}/force-delete', [QuotationController::class, 'forceDelete'])
            ->middleware('permission:quotations.force-delete')->name('force-delete');

        Route::get('/{quotation}/pdf', [QuotationController::class, 'generatePDF'])
            ->middleware('permission:quotations.pdf')->name('pdf');
        Route::post('/{quotation}/send', [QuotationController::class, 'sendEmail'])
            ->middleware('permission:quotations.send')->name('send');

        // Certificate of Completion (COC) Routes
        Route::get('/{quotation}/coc', [QuotationController::class, 'createCoc'])
            ->middleware('permission:quotations.coc')->name('coc.create');
        Route::post('/{quotation}/coc/pdf', [QuotationController::class, 'generateCocPdf'])
            ->middleware('permission:quotations.coc')->name('coc.pdf');

        // Resource Actions
        Route::get('/list', [QuotationController::class, 'index'])
            ->middleware('permission:quotations.index')->name('index');
        Route::get('/create', [QuotationController::class, 'create'])
            ->middleware('permission:quotations.create')->name('create');
        Route::post('/', [QuotationController::class, 'store'])
            ->middleware('permission:quotations.store')->name('store');
        Route::get('/{quotation}', [QuotationController::class, 'show'])
            ->middleware('permission:quotations.show')->name('show');
        Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])
            ->middleware('permission:quotations.edit')->name('edit');
        Route::put('/{quotation}', [QuotationController::class, 'update'])
            ->middleware('permission:quotations.update')->name('update');
        Route::delete('/{quotation}', [QuotationController::class, 'destroy'])
            ->middleware('permission:quotations.destroy')->name('destroy');
    });

    // --- User Management Module ---
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'dashboard'])
            ->middleware('permission:users.dashboard')->name('dashboard');
        Route::get('/list', [UserController::class, 'index'])
            ->middleware('permission:users.index')->name('index');
        Route::get('/archives', [UserController::class, 'archives'])
            ->middleware('permission:users.archives')->name('archives');
        Route::put('/{id}/restore', [UserController::class, 'restore'])
            ->middleware('permission:users.restore')->name('restore');
        Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])
            ->middleware('permission:users.force-delete')->name('force-delete');
        Route::put('/{user}/reset-password', [UserController::class, 'sendTemporaryPassword'])
            ->middleware('permission:users.reset-password')->name('reset-password');

        // Resource Actions
        Route::get('/create', [UserController::class, 'create'])
            ->middleware('permission:users.create')->name('create');
        Route::post('/', [UserController::class, 'store'])
            ->middleware('permission:users.store')->name('store');
        Route::get('/{user}', [UserController::class, 'show'])
            ->middleware('permission:users.show')->name('show');
        Route::get('/{user}/edit', [UserController::class, 'edit'])
            ->middleware('permission:users.edit')->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])
            ->middleware('permission:users.update')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])
            ->middleware('permission:users.destroy')->name('destroy');
    });

    // --- Role Management Module ---
    Route::prefix('roles')->name('roles.')->group(function () {
        Route::get('/list', [RoleController::class, 'index'])
            ->middleware('permission:roles.index')->name('index');
        Route::get('/create', [RoleController::class, 'create'])
            ->middleware('permission:roles.create')->name('create');
        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:roles.store')->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])
            ->middleware('permission:roles.edit')->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])
            ->middleware('permission:roles.update')->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])
            ->middleware('permission:roles.destroy')->name('destroy');
    });

    // --- Permission Management Module ---
    Route::prefix('permissions')->name('permissions.')->middleware('permission:permissions.manage')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('index');
        // Add this route for syncing permissions from routes
        Route::post('/sync-routes', [PermissionController::class, 'syncFromRoutes'])->name('sync-routes');
        Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
        Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
        Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
    });
});

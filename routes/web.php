<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TemplateManagerController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route(auth()->check() ? 'home' : 'login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    $loginRoute = Route::post('/login', [LoginController::class, 'login']);
    if (app()->environment('production')) {
        $loginRoute->middleware('throttle:5,1');
    }

    // Show Forgot Password Form
    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    $forgotRoute = Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');
    if (app()->environment('production')) {
        $forgotRoute->middleware('throttle:3,1');
    }

    // Show Reset Password Form
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    $resetRoute = Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
    if (app()->environment('production')) {
        $resetRoute->middleware('throttle:5,1');
    }
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/home', fn() => view('home'))->name('home');

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/update', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password');
    });

    // Quotations Module
    Route::prefix('quotations')->name('quotations.')->group(function () {
        Route::middleware('permission:quotations.add')->group(function () {
            Route::get('/create', [QuotationController::class, 'create'])->name('create');
            Route::post('/', [QuotationController::class, 'store'])->name('store');
        });

        // Read / View
        Route::middleware('permission:quotations.view')->group(function () {
            Route::get('/', [QuotationController::class, 'dashboard'])->name('dashboard');
            Route::get('/list', [QuotationController::class, 'index'])->name('index');
            Route::get('/archives', [QuotationController::class, 'archives'])->name('archives');
            Route::get('/{quotation}', [QuotationController::class, 'show'])->name('show');
        });

        // Update / Edit / Workflows
        Route::middleware('permission:quotations.update')->group(function () {
            Route::get('/{quotation}/edit', [QuotationController::class, 'edit'])->name('edit');
            Route::put('/{quotation}', [QuotationController::class, 'update'])->name('update');
            Route::delete('/{quotation}', [QuotationController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/restore', [QuotationController::class, 'restore'])->name('restore');
            Route::get('/{quotation}/prepare/quotation', [QuotationController::class, 'prepareQuotation'])->name('prepare');
        });

        // Generate PDF
        Route::middleware('permission:quotations.generate.pdf')->group(function () {
            Route::post('/{quotation}/quotation/pdf', [QuotationController::class, 'generateQuotationPdf'])->name('generate.pdf');
        });
        // Sending of Email
        Route::middleware('permission:quotations.send.email')->group(function () {
            Route::post('/{quotation}/quotation/email', [QuotationController::class, 'sendQuotationEmail'])->name('email');
        });


        // Delete (Hard-Delete)
        Route::middleware('permission:quotations.delete')->group(function () {
            Route::delete('/{id}/force-delete', [QuotationController::class, 'forceDelete'])->name('force-delete');
        });
    });

    // Schedules Module
    Route::prefix('schedules')->name('schedules.')->group(function () {
        // Create / Store
        Route::middleware('permission:schedules.add')->group(function () {
            Route::get('/create', [ScheduleController::class, 'create'])->name('create');
            Route::get('/create-from-quote/{quotation}', [ScheduleController::class, 'createFromQuotation'])->name('from-quote');
            Route::post('/', [ScheduleController::class, 'store'])->name('store');
        });

        // Read / View
        Route::middleware('permission:schedules.view')->group(function () {
            Route::get('/list', [ScheduleController::class, 'index'])->name('index');
            Route::get('/archives', [ScheduleController::class, 'archives'])->name('archives');
            Route::get('/{schedule}', [ScheduleController::class, 'show'])->name('show');
            Route::get('/', [ScheduleController::class, 'dashboard'])->name('dashboard');
        });

        // Update / Edit / Workflows
        Route::middleware('permission:schedules.update')->group(function () {
            Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
            Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('update');
            Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
            Route::patch('/{id}/restore', [ScheduleController::class, 'restore'])->name('restore');
            Route::get('/{schedule}/prepare/coc', [ScheduleController::class, 'prepareCoc'])->name('coc');
        });

        // Generate PDF
        Route::middleware('permission:schedules.coc.generate.pdf')->group(function () {
            Route::post('/{schedule}/coc/pdf', [ScheduleController::class, 'generateCocPdf'])->name('coc-generate.pdf');
        });
        // Sending of Email
        Route::middleware('permission:schedules.coc.send.email')->group(function () {
            Route::post('/{schedule}/coc/email', [ScheduleController::class, 'sendCocEmail'])->name('coc-email');
        });

        // Delete (Hard-Delete)
        Route::middleware('permission:schedules.delete')->group(function () {
            Route::delete('/{id}/force-delete', [ScheduleController::class, 'forceDelete'])->name('force-delete');
        });
    });

    // User Management Module
    Route::prefix('users')->name('users.')->group(function () {
        // Create / Add
        Route::middleware('permission:users.add')->group(function () {
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/store', [UserController::class, 'store'])->name('store');
        });

        // Read / View
        Route::middleware('permission:users.view')->group(function () {
            Route::get('/', [UserController::class, 'dashboard'])->name('dashboard');
            Route::get('/list', [UserController::class, 'index'])->name('index');
            Route::get('/archives', [UserController::class, 'archives'])->name('archives');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
        });

        // Update / Edit
        Route::middleware('permission:users.update')->group(function () {
            Route::put('/{id}/restore', [UserController::class, 'restore'])->name('restore');
            Route::put('/{user}/reset-password', [UserController::class, 'sendTemporaryPassword'])->name('reset-password');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Delete (Hard-Delete)
        Route::middleware('permission:users.delete')->group(function () {
            Route::delete('/{id}/force-delete', [UserController::class, 'forceDelete'])->name('force-delete');
        });
    });

    // Role Management Module
    Route::prefix('roles')->name('roles.')->group(function () {
        // Create / Add
        Route::middleware('permission:roles.add')->group(function () {
            Route::get('/create', [RoleController::class, 'create'])->name('create');
            Route::post('/store', [RoleController::class, 'store'])->name('store');
        });

        // Read / View
        Route::middleware('permission:roles.view')->group(function () {
            Route::get('/list', [RoleController::class, 'index'])->name('index');
        });

        // Update / Edit
        Route::middleware('permission:roles.update')->group(function () {
            Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
        });
    });

    // Permission Management Module
    Route::prefix('permissions')->name('permissions.')->group(function () {
        // Create / Add
        Route::middleware('permission:permissions.add')->group(function () {
            Route::post('/sync-routes', [PermissionController::class, 'syncFromRoutes'])->name('sync-routes');
        });

        // Read / View
        Route::middleware('permission:permissions.view')->group(function () {
            Route::get('/', [PermissionController::class, 'index'])->name('index');
        });

        // Update / Edit
        Route::middleware('permission:permissions.update')->group(function () {
            Route::get('/{permission}/edit', [PermissionController::class, 'edit'])->name('edit');
            Route::put('/{permission}', [PermissionController::class, 'update'])->name('update');
            Route::delete('/{permission}', [PermissionController::class, 'destroy'])->name('destroy');
        });
    });

    // Template Management Module
    Route::prefix('templates')->name('templates.')->middleware('permission:templates.manage')->group(function () {
        Route::get('/render', [TemplateManagerController::class, 'render'])->name('render');
        Route::get('/stream-pdf', [TemplateManagerController::class, 'streamPdf'])->name('stream.pdf');
        Route::get('/', [TemplateManagerController::class, 'index'])->name('index');
    });
});

<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\TwoFactorController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\FeedbackController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\QrCodeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    // ============================================================
    // Guest (not authenticated)
    // ============================================================
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'show'])->name('login');
        Route::post('login', [LoginController::class, 'login'])
            ->middleware('throttle:login')
            ->name('login.submit');

        Route::get('2fa', [TwoFactorController::class, 'show'])->name('2fa.show');
        Route::post('2fa', [TwoFactorController::class, 'verify'])
            ->middleware('throttle:two-factor')
            ->name('2fa.verify');
        Route::post('2fa/resend', [TwoFactorController::class, 'resend'])
            ->name('2fa.resend');
    });

    // ============================================================
    // Authenticated (any logged-in admin)
    // ============================================================
    Route::middleware('auth')->group(function () {
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');

        // ---------- Dashboard ----------
        Route::get('dashboard', [DashboardController::class, 'index'])
            ->middleware('permission:dashboard.view')
            ->name('dashboard');

        // ---------- Feedback ----------
        Route::middleware('permission:feedback.view')->group(function () {
            Route::get('feedback', [FeedbackController::class, 'index'])->name('feedback.index');
            Route::get('feedback/datatable', [FeedbackController::class, 'datatable'])->name('feedback.datatable');
            Route::get('feedback/{feedback:uuid}', [FeedbackController::class, 'show'])->name('feedback.show');

            Route::middleware('permission:feedback.update')->group(function () {
                Route::patch('feedback/{feedback:uuid}/status', [FeedbackController::class, 'updateStatus'])
                    ->name('feedback.status');
                Route::patch('feedback/{feedback:uuid}/priority', [FeedbackController::class, 'updatePriority'])
                    ->name('feedback.priority');
                Route::post('feedback/{feedback:uuid}/assign', [FeedbackController::class, 'assign'])
                    ->name('feedback.assign');
            });

            Route::middleware('permission:feedback.attachments.download')->group(function () {
                Route::get('feedback/{feedback:uuid}/attachment/{attachment:uuid}/download',
                    [FeedbackController::class, 'downloadAttachment'])
                    ->name('feedback.attachment.download');
            });

            Route::middleware('permission:feedback.delete')->group(function () {
                Route::delete('feedback/{feedback:uuid}', [FeedbackController::class, 'destroy'])
                    ->name('feedback.destroy');
            });
        });

        // ---------- Categories ----------
        Route::middleware('permission:categories.view')->group(function () {
            Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        });
        Route::middleware('permission:categories.manage')->group(function () {
            Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
            Route::patch('categories/{category:uuid}', [CategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category:uuid}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        });

        // ---------- Locations ----------
        Route::middleware('permission:locations.view')->group(function () {
            Route::get('locations', [LocationController::class, 'index'])->name('locations.index');
        });
        Route::middleware('permission:locations.manage')->group(function () {
            Route::post('locations', [LocationController::class, 'store'])->name('locations.store');
            Route::patch('locations/{location:uuid}', [LocationController::class, 'update'])->name('locations.update');
            Route::delete('locations/{location:uuid}', [LocationController::class, 'destroy'])->name('locations.destroy');
        });

        // ---------- QR Codes ----------
        // ---------- QR Codes ----------
        Route::middleware('permission:qr.view')->group(function () {
            Route::get('qr', [QrCodeController::class, 'index'])->name('qr.index');

            // ⚠️ STATIC routes MUST come before dynamic {qrCode:uuid} routes
            Route::get('qr/create', [QrCodeController::class, 'create'])
                ->middleware('permission:qr.manage')
                ->name('qr.create');

            // Now the dynamic routes
            Route::get('qr/{qrCode:uuid}', [QrCodeController::class, 'show'])->name('qr.show');
            Route::get('qr/{qrCode:uuid}/image', [QrCodeController::class, 'image'])->name('qr.image');
        });

        Route::middleware('permission:qr.manage')->group(function () {
            Route::post('qr', [QrCodeController::class, 'store'])->name('qr.store');
            Route::patch('qr/{qrCode:uuid}/status', [QrCodeController::class, 'updateStatus'])->name('qr.status');
            Route::post('qr/{qrCode:uuid}/regenerate', [QrCodeController::class, 'regenerate'])->name('qr.regenerate');
            Route::delete('qr/{qrCode:uuid}', [QrCodeController::class, 'destroy'])->name('qr.destroy');
        });

        Route::middleware('permission:qr.download')->group(function () {
            Route::get('qr/{qrCode:uuid}/download', [QrCodeController::class, 'download'])->name('qr.download');
        });

        // ---------- Departments ----------
        Route::middleware('permission:departments.view')->group(function () {
            Route::get('departments', [DepartmentController::class, 'index'])->name('departments.index');
        });
        Route::middleware('permission:departments.manage')->group(function () {
            Route::post('departments', [DepartmentController::class, 'store'])->name('departments.store');
            Route::patch('departments/{department:uuid}', [DepartmentController::class, 'update'])->name('departments.update');
            Route::delete('departments/{department:uuid}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        });

        // ---------- Reports ----------
        Route::middleware('permission:reports.view')->group(function () {
            Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        });
        Route::middleware('permission:reports.export')->group(function () {
            Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
        });

        // ---------- Audit Logs ----------
        Route::middleware('permission:audit.view')->group(function () {
            Route::get('audit', [AuditLogController::class, 'index'])->name('audit.index');
        });

        // ---------- Users ----------
        Route::middleware('permission:users.manage')->group(function () {
            Route::resource('users', UserController::class)->except(['show']);
        });
    });
});

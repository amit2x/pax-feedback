<?php

use App\Http\Controllers\Public\FeedbackSubmitController;
use App\Http\Controllers\Public\FeedbackWizardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/feedback');

Route::prefix('feedback')
    ->name('feedback.')
    ->middleware(['web', 'throttle:feedback-public'])
    ->group(function () {
        Route::get('/', [FeedbackWizardController::class, 'welcome'])->name('welcome');
        Route::get('/f/{token}', [FeedbackWizardController::class, 'fromQr'])->name('qr');
        Route::post('/start', [FeedbackWizardController::class, 'start'])->name('start');
        Route::get('/step/{step}', [FeedbackWizardController::class, 'step'])->name('step');

        Route::get('/api/subcategories', [FeedbackWizardController::class, 'subcategories'])
            ->name('api.subcategories');

        Route::post('/submit', [FeedbackSubmitController::class, 'submit'])
            ->middleware('throttle:feedback-submit')
            ->name('submit');

        Route::get('/thank-you/{reference}', [FeedbackSubmitController::class, 'thankYou'])
            ->name('thank-you');
    });

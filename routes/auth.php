<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerificationCodeController;
use App\Http\Controllers\BrandSetupController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');
    
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Email verification code routes - must be accessible to logged-in users (not verified yet)
Route::middleware('auth')->group(function () {
    Route::get('email-verification', [VerificationCodeController::class, 'show'])
        ->name('verification.notice');

    Route::post('email-verification/send', [VerificationCodeController::class, 'send'])
        ->name('verification.send');

    Route::post('email-verification/verify', [VerificationCodeController::class, 'verify'])
        ->name('verification.verify');

    // Brand setup routes (before email verification)
    Route::get('brand-setup', [BrandSetupController::class, 'show'])->name('brand-setup.show');
    Route::post('brand-setup/step', [BrandSetupController::class, 'storeStep'])->name('brand-setup.store-step');
    Route::get('brand-setup/data', [BrandSetupController::class, 'getSetupData'])->name('brand-setup.get-data');
    Route::post('brand-setup/complete', [BrandSetupController::class, 'complete'])->name('brand-setup.complete');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});

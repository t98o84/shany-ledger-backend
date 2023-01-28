<?php

//TODO: リクエスト数の制限追加
Route::name('auth.')->group(function () {
    Route::post('/sign-up', [\App\Http\Controllers\Auth\RegistrationController::class, 'signUpWithEmailAndPassword'])
        ->name('sign-up-with-email-and-password');
    Route::post('/sign-in', [\App\Http\Controllers\Auth\AuthenticationController::class, 'signInWithEmailAndPassword'])
        ->name('sign-in-with-email-and-password');
    Route::post('/sign-out', [\App\Http\Controllers\Auth\AuthenticationController::class, 'signOut'])
        ->middleware(['auth:sanctum'])
        ->name('sign-out');

    Route::prefix('/reset-password')->group(function () {
        Route::post('/', [\App\Http\Controllers\Auth\PasswordController::class, 'sendPasswordResetLink'])
            ->name('send-password-reset-link');
        Route::put('/', [\App\Http\Controllers\Auth\PasswordController::class, 'resetPassword'])
            ->name('reset-password');
    });

    Route::prefix('/users')->name('user.')->group(function () {
        Route::prefix('/{user}')->group(function () {
            Route::get('/verify-email', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'verify'])
                ->name('verify-email');
            Route::post('/verify-email', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'sendEmailVerificationNotification'])
                ->middleware(['auth:sanctum'])
                ->name('send-email-verification-notification');
        });
    });
});

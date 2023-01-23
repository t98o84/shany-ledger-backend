<?php

Route::name('auth.')->group(function () {
    Route::post('/sign-up', [\App\Http\Controllers\Auth\RegistrationController::class, 'signUpWithEmailAndPassword'])
        ->name('sign-up-with-email-and-password');

    Route::prefix('/users')->name('user.')->group(function () {
        Route::prefix('/{user}')->group(function () {
            Route::get('/verify-email')->name('verify-email');
            Route::post('/verify-email', [\App\Http\Controllers\Auth\EmailVerificationController::class, 'sendEmailVerificationNotification'])
                ->middleware(['auth:sanctum'])
                ->name('send-email-verification-notification');
        });
    });
});

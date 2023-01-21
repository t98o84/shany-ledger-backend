<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('auth.')->group(function () {
    Route::post('sign-up', [\App\Http\Controllers\Auth\RegistrationController::class, 'signUpWithEmailAndPassword'])
        ->name('sign-up-with-email-and-password');
});

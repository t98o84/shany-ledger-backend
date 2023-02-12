<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SendEmailVerificationNotification;
use App\Actions\Auth\VerifyEmail;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\VerifyEmailRequest;

class EmailVerificationController extends Controller
{
    public function verify(VerifyEmailRequest $request, $user, VerifyEmail $verifyEmail): \Illuminate\Http\RedirectResponse
    {
        $error = $verifyEmail->handle($user, $request->hash, $request->expiration, $request->signature);

        if ($error instanceof AuthErrorCode) {
            return response()->redirectTo(config('app.front_url') . "/email-verification?user=$user&error=" . $error->title());
        }

        return response()->redirectTo(config('app.front_url') . "/email-verified?user=$user");
    }

    public function sendEmailVerificationNotification(string $user, SendEmailVerificationNotification $sendEmailVerificationNotification): \Illuminate\Http\Response
    {
        $verificationUrl = $sendEmailVerificationNotification->handle($user);

        if ($verificationUrl instanceof AuthErrorCode) {
            throw $verificationUrl->toProblemDetailException();
        }

        return response()->noContent();
    }
}

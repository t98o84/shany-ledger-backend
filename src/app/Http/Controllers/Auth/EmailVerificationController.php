<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SendEmailVerificationNotification;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{

    public function sendEmailVerificationNotification(string $user, SendEmailVerificationNotification $sendEmailVerificationNotification): \Illuminate\Http\Response
    {
        $verificationUrl = $sendEmailVerificationNotification->handle($user);

        if (is_a($verificationUrl, AuthErrorCode::class)) {
            throw new BadRequestsErrorException(errorCode: $verificationUrl->value, title: __("error/auth/index.$verificationUrl->value"));
        }

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\ResetPassword;
use App\Actions\Auth\SendPasswordResetLink;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;

class PasswordController extends Controller
{
    /**
     * @param SendPasswordResetLinkRequest $request
     * @param SendPasswordResetLink $sendPasswordResetLink
     * @return \Illuminate\Http\Response
     * @throws BadRequestsErrorException
     */
    public function sendPasswordResetLink(SendPasswordResetLinkRequest $request, SendPasswordResetLink $sendPasswordResetLink): \Illuminate\Http\Response
    {
        $error = $sendPasswordResetLink->handle($request->email);

        if ($error instanceof AuthErrorCode) {
            throw new BadRequestsErrorException($error->value, __("error/auth/index.$error->value"));
        }

        return response()->noContent();
    }

    /**
     * @throws \Throwable
     * @throws BadRequestsErrorException
     */
    public function resetPassword(ResetPasswordRequest $request, ResetPassword $resetPassword): \Illuminate\Http\Response
    {
        $error = $resetPassword->handle($request->password, $request->email, $request->token);

        if ($error instanceof AuthErrorCode) {
            throw new BadRequestsErrorException($error->value, __("error/auth/index.$error->value"));
        }

        return response()->noContent();
    }
}

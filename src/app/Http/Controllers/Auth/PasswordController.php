<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\ResetPassword;
use App\Actions\Auth\SendPasswordResetLink;
use App\Actions\Auth\UpdatePassword;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Exceptions\ProblemDetails\ProblemDetailsException;
use App\Http\Controllers\Controller;
use App\Http\ErrorCodeHandlers\Auth\AuthErrorCodeHandler;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendPasswordResetLinkRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;

class PasswordController extends Controller
{
    public function sendPasswordResetLink(SendPasswordResetLinkRequest $request, SendPasswordResetLink $sendPasswordResetLink): \Illuminate\Http\Response
    {
        $error = $sendPasswordResetLink->handle($request->email);

        if ($error instanceof AuthErrorCode) {
            throw $error->toProblemDetailException();
        }

        return response()->noContent();
    }

    public function resetPassword(ResetPasswordRequest $request, ResetPassword $resetPassword): \Illuminate\Http\Response
    {
        $error = $resetPassword->handle($request->password, $request->email, $request->token);

        if ($error instanceof AuthErrorCode) {
            throw $error->toProblemDetailException();
        }

        return response()->noContent();
    }

    public function update(string $user, UpdatePasswordRequest $request, UpdatePassword $updatePassword): \Illuminate\Http\Response
    {
        $error = $updatePassword->handle(id: $user, oldPassword: $request->old_password, newPassword: $request->new_password);

        if ($error instanceof AuthErrorCode) {
            throw $error->toProblemDetailException();
        }

        return response()->noContent();
    }
}

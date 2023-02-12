<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SignUpAndInWithEmailAndPassword;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpAndInWithEmailAndPasswordRequest;
use App\Http\Resources\Auth\UserResource;

class RegistrationController extends Controller
{
    /**
     * @throws \Throwable
     */
    public function signUpWithEmailAndPassword(SignUpAndInWithEmailAndPasswordRequest $request, SignUpAndInWithEmailAndPassword $signUpAndIn): \Illuminate\Http\JsonResponse
    {
        $userAndToken = $signUpAndIn->handle($request->email, $request->password);

        if ($userAndToken instanceof AuthErrorCode) {
            throw $userAndToken->toProblemDetailException();
        }

        return response()->json([
            'user' => UserResource::make($userAndToken['user']),
            'access_token' => $userAndToken['token'],
        ], 201);
    }
}

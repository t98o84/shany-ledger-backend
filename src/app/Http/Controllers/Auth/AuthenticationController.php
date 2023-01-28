<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SignInWithEmailAndPassword;
use App\Actions\Auth\SignOut;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignInWithEmailAndPasswordRequest;

class AuthenticationController extends Controller
{
    /**
     * @throws BadRequestsErrorException
     */
    public function signInWithEmailAndPassword(SignInWithEmailAndPasswordRequest $request, SignInWithEmailAndPassword $signInAndIn): \Illuminate\Http\JsonResponse
    {
        $token = $signInAndIn->handle($request->email, $request->password);

        if ($token instanceof AuthErrorCode) {
            throw new BadRequestsErrorException($token->value, __("error/auth/index.$token->value"));
        }

        return response()->json([
            'access_token' => $token,
        ]);
    }

    public function signOut(SignOut $signOut): \Illuminate\Http\Response
    {
        $signOut->handle(\Auth::user()?->currentAccessToken());

        return response()->noContent();
    }
}

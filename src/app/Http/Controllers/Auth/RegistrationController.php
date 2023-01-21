<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SignUpWithEmailAndPassword;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SignUpWithEmailAndPasswordRequest;
use App\Http\Resources\Auth\UserResource;

class RegistrationController extends Controller
{
    public function signUpWithEmailAndPassword(SignUpWithEmailAndPasswordRequest $request, SignUpWithEmailAndPassword $signUp)
    {
        $user = $signUp->handle($request->email, $request->password);

        return [
            'user' => UserResource::make($user),
        ];
    }
}

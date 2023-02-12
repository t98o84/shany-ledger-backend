<?php

namespace App\Actions\Auth;

use App\Events\Auth\SignedIn;
use App\Models\User;

class SignInWithEmailAndPassword
{
    public function handle(string $email, string $password): string|AuthErrorCode
    {
        $user = User::where('email', $email)->first();

        if (is_null($user) || !$user->equalPassword($password)) {
            return AuthErrorCode::InvalidEmailOrPassword;
        }

        $token = $user->createToken('access-token');

        SignedIn::dispatch($user);

        return $token->plainTextToken;
    }
}

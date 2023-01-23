<?php

namespace App\Actions\Auth;

use App\Models\User;

class VerifyEmail
{
    public function handle(string $id): AuthErrorCode|null
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::VerifyEmailUserNotExists;
        }

        if ($user->hasVerifiedEmail()) {
            return AuthErrorCode::VerifyEmailEmailVerified;
        }

        $user->markEmailAsVerified();

        return null;
    }
}

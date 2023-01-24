<?php

namespace App\Actions\Auth;

use App\Events\Auth\PasswordReset as PasswordResetEvent;
use App\Models\Auth\PasswordReset;
use App\Models\User;

class ResetPassword
{
    /**
     * @throws \Throwable
     */
    public function handle(string $password, string $email, string $token): AuthErrorCode|true
    {
        $passwordReset = PasswordReset::find($email);

        if (is_null($passwordReset) || !$passwordReset->equalsToken($token)) {
            return AuthErrorCode::ResetPasswordInvalidRequest;
        }

        if ($passwordReset->expired()) {
            return AuthErrorCode::ResetPasswordTokenExpired;
        }

        $user = User::where('email', $email)->first();

        if (is_null($user)) {
            return AuthErrorCode::ResetPasswordInvalidRequest;
        }

        $user->password = User::hashPassword($password);

        return \DB::transaction(function () use ($passwordReset, $user) {
            $passwordReset->delete();
            $user->save();
            PasswordResetEvent::dispatch($user);

            return true;
        });
    }
}

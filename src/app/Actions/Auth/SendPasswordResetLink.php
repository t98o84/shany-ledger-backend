<?php

namespace App\Actions\Auth;

use App\Mail\Auth\PasswordReset;
use  App\Models\Auth\PasswordReset as PasswordResetModel;
use App\Models\User;

class SendPasswordResetLink
{
    public function handle(string $email): AuthErrorCode|true
    {
        $user = User::where('email', $email)->first();

        if (is_null($user)) {
            return AuthErrorCode::InvalidUserId;
        }

        return \DB::transaction(function () use ($email, $user) {
            PasswordResetModel::find($email)?->delete();

            $token = PasswordResetModel::createToken();

            PasswordResetModel::create(['email' => $email, 'token' => PasswordResetModel::hashToken($token)]);

            $resetUrl = static::buildResetUrl($token);

            \Mail::send(new PasswordReset($user, $resetUrl));

            return true;
        });
    }

    private static function buildResetUrl(string $token): string
    {
        return config('app.front_url') . "/reset-password?token=$token";
    }
}

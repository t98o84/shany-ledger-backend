<?php

namespace App\Actions\Auth;

use App\Mail\Auth\EmailVerification;
use App\Models\Shared\Signature;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class SendEmailVerificationNotification
{
    public function handle(string $id): string|AuthErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::InvalidUserId;
        }

        if ($user->hasVerifiedEmail()) {
            return AuthErrorCode::EmailVerified;
        }

        $hash = \Hash::make($user->name . $user->getEmailForVerification());
        $expiration = Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60));

        $signature = Signature::make(
            [
                'user' => $user->id,
                'hash' => $hash,
            ],
            $expiration
        );

        $verificationUrl = route('auth.user.verify-email', [
            'user' => $user->id,
            'hash' => $hash,
            'expiration' => $expiration->getTimestamp(),
            'signature' =>$signature->signature,
        ]);

        \Mail::send(new EmailVerification(user: $user, verificationUrl: $verificationUrl));

        return $verificationUrl;
    }
}

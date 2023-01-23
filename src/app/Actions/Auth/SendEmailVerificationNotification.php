<?php

namespace App\Actions\Auth;

use App\Mail\Auth\EmailVerification;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class SendEmailVerificationNotification
{
    public function handle(string $id): string|AuthErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::SendEmailVerificationNotificationUserNotExists;
        }

        if ($user->hasVerifiedEmail()) {
            return AuthErrorCode::SendEmailVerificationNotificationEmailVerified;
        }

        $verificationUrl = URL::temporarySignedRoute(
            'auth.user.verify-email',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'user' => $user->id,
                'hash' => \Hash::make($user->name . $user->getEmailForVerification()),
            ]
        );

        \Mail::send(new EmailVerification(user: $user, verificationUrl: $verificationUrl));

        return $verificationUrl;
    }
}

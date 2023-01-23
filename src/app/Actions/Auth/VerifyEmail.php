<?php

namespace App\Actions\Auth;

use App\Models\Shared\Signature;
use App\Models\User;
use Carbon\Carbon;

class VerifyEmail
{
    public function handle(string $id, string $hash, int $expiration, string $signature): AuthErrorCode|true
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::VerifyEmailUserNotExists;
        }

        if ($user->hasVerifiedEmail()) {
            return AuthErrorCode::VerifyEmailEmailVerified;
        }

        $signatureModel = new Signature($signature, [
            'user' => $id,
            'hash' => $hash,
        ], new Carbon($expiration));

        if ($signatureModel->expired()) {
            return AuthErrorCode::VerifyEmailSignatureExpired;
        }

        if (!$signatureModel->valid()) {
            return AuthErrorCode::VerifyEmailInvalidSignature;
        }

        $user->markEmailAsVerified();

        return true;
    }
}

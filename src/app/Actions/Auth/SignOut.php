<?php

namespace App\Actions\Auth;

use App\Events\Auth\SignedOut;
use App\Models\Auth\PersonalAccessToken;

class SignOut
{
    public function handle(string $token): true
    {
        $tokenModel = PersonalAccessToken::findToken($token);

        if (is_null($tokenModel)) {
            return true;
        }

        $tokenModel->delete();

        SignedOut::dispatch($tokenModel->tokenable);

        return true;
    }
}

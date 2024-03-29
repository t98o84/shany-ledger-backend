<?php

namespace App\Actions\Auth;

use App\Events\Auth\SignedIn;
use App\Models\User;

readonly class SignUpAndInWithEmailAndPassword
{
    public function __construct(private SignUpWithEmailAndPassword $signUp, private SignInWithEmailAndPassword $signIn)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $email, string $password): array|AuthErrorCode
    {
        return \DB::transaction(function() use ($email, $password) {
            $user = $this->signUp->handle($email, $password);

            if ($user instanceof AuthErrorCode) {
                return $user;
            }

            $token = $this->signIn->handle($email, $password);

            if ($token instanceof AuthErrorCode) {
                return $token;
            }

            return [
                'user' => $user,
                'token'=> $token,
            ];
        }) ;
    }
}

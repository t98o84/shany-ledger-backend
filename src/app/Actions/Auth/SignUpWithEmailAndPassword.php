<?php

namespace App\Actions\Auth;

use App\Events\Auth\SignedUp;
use App\Models\User;
use Illuminate\Support\Str;

class SignUpWithEmailAndPassword
{
    public function handle(string $email, string $password): User|AuthErrorCode
    {
        if (User::where('email', $email)->exists()) {
            return AuthErrorCode::SignUpEmailExists;
        }

        $user = User::create([
            'id' => (string)Str::uuid(),
            'name' => $this->convertEmailToName($email),
            'email' => $email,
            'password' => User::hashPassword($password),
        ]);

        SignedUp::dispatch($user);

        return $user;
    }

    private function convertEmailToName(string $email): string
    {
        $name = Str::of($email)->split('/@/')->first();
        $name = Str::substr($name, 0, 255);
        $name = preg_replace('/[^a-z0-9]/', ' ', $name);

        if (preg_replace('/ /', '', $name) === '') {
            $name = 'Unknown';
        }

        return $name;
    }
}

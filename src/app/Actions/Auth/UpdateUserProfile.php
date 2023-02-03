<?php

namespace App\Actions\Auth;

use App\Events\Auth\UnverifiedEmail;
use App\Events\Auth\UpdatedUserProfile;
use App\Models\User;

class UpdateUserProfile
{
    public function handle(string $id, string $name, string $email): User|AuthErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::UserNotExists;
        }

        $authUser = \Auth::user();
        if (is_null($authUser) || !$authUser->can('update', $user)) {
            return AuthErrorCode::Forbidden;
        }

        if ($user->email !== $email) {
            $user->email = $email;
            $user->email_verified_at = null;
        }

        $user->fill(['name' => $name])->save();

        UpdatedUserProfile::dispatch($user);

        if (is_null($user->email_verified_at)) {
            UnverifiedEmail::dispatch($user);
        }

        return $user;
    }
}

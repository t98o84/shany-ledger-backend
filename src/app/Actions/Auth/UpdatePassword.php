<?php

namespace App\Actions\Auth;

use App\Events\Auth\UpdatedPassword;
use App\Models\User;

class UpdatePassword
{
    public function handle(string $id, string $oldPassword, string $newPassword): User|AuthErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::UserNotExists;
        }

        $authUser = \Auth::user();
        if (is_null($authUser) || !$authUser->can('update', $user)) {
            return AuthErrorCode::Forbidden;
        }

        if (!$user->equalPassword($oldPassword)) {
            return AuthErrorCode::PasswordMismatch;
        }

        $user->setPassword($newPassword)->save();

        UpdatedPassword::dispatch($user);

        return $user;
    }
}

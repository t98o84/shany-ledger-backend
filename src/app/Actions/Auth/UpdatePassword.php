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
            return AuthErrorCode::InvalidUserId;
        }

        $authUser = \Auth::user();
        if (is_null($authUser) || !$authUser->can('update', $user)) {
            return AuthErrorCode::Unauthorized;
        }

        if (!$user->equalPassword($oldPassword)) {
            return AuthErrorCode::InvalidPassword;
        }

        $user->setPassword($newPassword)->save();

        UpdatedPassword::dispatch($user);

        return $user;
    }
}

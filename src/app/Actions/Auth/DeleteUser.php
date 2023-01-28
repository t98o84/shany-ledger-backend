<?php

namespace App\Actions\Auth;

use App\Events\Auth\DeletedUser;
use App\Models\Auth\PersonalAccessToken;
use App\Models\User;

class DeleteUser
{
    /**
     * @throws \Throwable
     */
    public function handle(string $id, string $token): true|AuthErrorCode
    {
        $user = User::find($id);
        $tokenModel = PersonalAccessToken::findToken($token);

        if (is_null($user) || is_null($tokenModel)) {
            return AuthErrorCode::InvalidRequest;
        }

        if (!method_exists($tokenModel->tokenable, 'getAuthIdentifier')) {
            throw new \LogicException("getAuthIdentifier メソッドを実装してください。");
        }

        if ($user->getAuthIdentifier() !== $tokenModel->tokenable->getAuthIdentifier()) {
            return AuthErrorCode::InvalidRequest;
        }

        \DB::transaction(static function () use ($user) {
            $user->tokens()->delete();
            $user->valid_email = null;
            $user->save();
            $user->delete();

            DeletedUser::dispatch($user);
        });

        return true;
    }
}

<?php

namespace App\Actions\Auth;

use App\Actions\Shared\FileErrorCode;
use App\Actions\Shared\RemoveFile;
use App\Models\User;

readonly class DeleteUserAvatar
{
    public function __construct(private RemoveFile $removeFile)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $id): true|AuthErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::InvalidUserId;
        }

        $authUser = \Auth::user();
        if (is_null($authUser) || !$authUser->can('delete', $user)) {
            return AuthErrorCode::Unauthorized;
        }

        if (is_null($user->avatar?->file)) {
            return true;
        }

        return \DB::transaction(function () use ($user) {
            $error = $this->removeFile->handle($user->avatar->file->id);

            if ($error instanceof FileErrorCode) {
                return AuthErrorCode::FileIOFailed;
            }

            $user->avatar->delete();

            return true;
        });
    }
}

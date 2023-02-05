<?php

namespace App\Actions\Auth;

use App\Actions\Shared\FileErrorCode;
use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Http\UploadedFile;

class DeleteUserAvatar
{
    public function __construct(private readonly RemoveFile $removeFile)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $id): true|AuthErrorCode|FileErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::UserNotExists;
        }

        $authUser = \Auth::user();
        if (is_null($authUser) || !$authUser->can('delete', $user)) {
            return AuthErrorCode::Forbidden;
        }

        if (is_null($user->avatar?->file)) {
            return true;
        }

        return \DB::transaction(function () use ($user) {
            $error = $this->removeFile->handle($user->avatar->file->id);

            if ($error instanceof FileErrorCode) {
                return AuthErrorCode::FileRemoveFailed;
            }

            $user->avatar->delete();

            return true;
        });
    }
}

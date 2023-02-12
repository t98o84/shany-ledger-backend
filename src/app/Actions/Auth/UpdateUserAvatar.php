<?php

namespace App\Actions\Auth;

use App\Actions\Shared\FileErrorCode;
use App\Actions\Shared\UploadFile;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Http\UploadedFile;

class UpdateUserAvatar
{
    public function __construct(private readonly UploadFile $uploadFile)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $id, UploadedFile $uploadedFile): UserAvatar|AuthErrorCode|FileErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::InvalidUserId;
        }

        $authUser = \Auth::user();
        if (is_null($authUser) || !$authUser->can('update', $user)) {
            return AuthErrorCode::Unauthorized;
        }

        try {
            \DB::beginTransaction();
            $avatar = $user->avatar ?? UserAvatar::create(['user_id' => $user->id]);

            $file = $this->uploadFile->handle(
                fileable: $avatar,
                uploadedFile: $uploadedFile,
                path: $user->baseFilePath(),
                overwriteFileId: $user->avatar?->file?->id
            );

            if ($file instanceof FileErrorCode) {
                \DB::rollBack();
                return $file;
            }

            \DB::commit();

            return $avatar->unsetRelation('file');
        } catch (\Throwable $throwable) {
            \DB::rollBack();
            throw $throwable;
        }
    }
}

<?php

namespace App\Actions\Auth;

use App\Actions\Shared\FileErrorCode;
use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Events\Auth\UnverifiedEmail;
use App\Events\Auth\UpdatedUserProfile;
use App\Models\Shared\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;

class UpdateUserProfile
{
    public function __construct(private readonly UploadFile $uploadFile, private readonly RemoveFile $removeFile)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $id, string $name, string $email, bool $needRemoveAvatar = false, ?UploadedFile $avatar = null): true|AuthErrorCode
    {
        $user = User::find($id);

        if (is_null($user)) {
            return AuthErrorCode::UserNotExists;
        }

        if ($user->id !== \Auth::user()->id) {
            return AuthErrorCode::InvalidRequest;
        }

        $user = $this->updateAvatar($user, $needRemoveAvatar, $avatar);

        $user->fill([
            'name' => $name,
        ]);

        if ($user->email !== $email) {
            $user->email = $email;
            $user->email_verified_at = null;
        }

        try {
            return \DB::transaction(function () use ($user) {
                $user->save();

                UpdatedUserProfile::dispatch($user);

                if (is_null($user->email_verified_at)) {
                    UnverifiedEmail::dispatch($user);
                }

                return true;
            });
        } catch (\Throwable $throwable) {
            if ($avatar) {
                $this->removeAvatar($user->id, $user->avatar_id, $throwable);
            }

            throw $throwable;
        }
    }

    private function updateAvatar(User $user, bool $needRemoveAvatar, ?UploadedFile $avatar): User
    {
        if ($this->needRemoveAvatar($user->avatar_id, $needRemoveAvatar, $avatar)) {
            $this->removeAvatar($user->id, $user->avatar_id);
            $user->avatar_id = null;
        }

        if ($avatar) {
            $avatarFile = $this->uploadAvatar($user->id, $avatar);
            $user->avatar_id = $avatarFile->id;
        }

        return $user;
    }

    private function needRemoveAvatar(?string $avatarId, bool $needRemoveAvatar, ?UploadedFile $avatar): bool
    {
        return ($needRemoveAvatar && $avatarId) || ($avatar && $avatarId);
    }

    private function removeAvatar(string $userId, string $avatarId, ?\Throwable $throwable = null): void
    {
        $error = $this->removeFile->handle($avatarId, User::buildFilePath($userId));

        if ($error instanceof FileErrorCode) {
            throw new \RuntimeException("アバター画像の削除でエラーが発生しました。（code: {$error->value}）", 0, $throwable);
        }
    }

    private function uploadAvatar(string $userId, UploadedFile $avatar): File
    {
        $avatarFile = $this->uploadFile->handle($avatar, User::buildFilePath($userId));

        if ($avatarFile instanceof FileErrorCode) {
            throw new \RuntimeException("アバター画像のアップロードでエラーが発生しました。（code: {$avatarFile->value}）");
        }

        return $avatarFile;
    }
}

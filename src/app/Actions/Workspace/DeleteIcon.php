<?php

namespace App\Actions\Workspace;

use App\Actions\Shared\FileErrorCode;
use App\Actions\Shared\RemoveFile;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceIcon;
use Illuminate\Http\UploadedFile;

class DeleteIcon
{
    public function __construct(private RemoveFile $removeFile)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $userId, string $workspaceId): true|WorkspaceErrorCode
    {
        $user = User::find($userId);
        if (is_null($user)) {
            return WorkspaceErrorCode::InvalidUserId;
        }

        $workspace = Workspace::find($workspaceId);
        if (is_null($workspace)) {
            return WorkspaceErrorCode::InvalidWorkspaceId;
        }

        $workspaceAccount = $workspace->accounts()->where('user_id', $userId)->first();
        if (is_null($workspaceAccount) || $user->cannot('update', [$workspace, $workspaceAccount])) {
            return WorkspaceErrorCode::Unauthorized;
        }

        if (is_null($workspace->icon)) {
            return true;
        }

        \DB::beginTransaction();
        try {
            $error = $this->removeFile->handle($workspace->icon->file->id);

            if ($error instanceof FileErrorCode) {
                return WorkspaceErrorCode::FileIOFailed;
            }

            $workspace->icon->delete();


            \App\Events\Workspace\DeleteIcon::dispatch($workspace, $workspaceAccount);

            \DB::commit();

            return true;
        } catch (\Throwable $throwable) {
            \DB::rollBack();
            throw $throwable;
        }
    }
}

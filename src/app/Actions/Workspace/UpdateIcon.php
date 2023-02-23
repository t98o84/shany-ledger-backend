<?php

namespace App\Actions\Workspace;

use App\Actions\Shared\FileErrorCode;
use App\Actions\Shared\UploadFile;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceIcon;
use Illuminate\Http\UploadedFile;

class UpdateIcon
{
    public function __construct(private readonly UploadFile $uploadFile)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $userId, string $workspaceId, UploadedFile $uploadedIcon): WorkspaceIcon|WorkspaceErrorCode
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

        try {
            \DB::beginTransaction();
            $icon = $workspace->icon ?? WorkspaceIcon::create(['workspace_id' => $workspace->id]);

            $file = $this->uploadFile->handle(
                fileable: $icon,
                uploadedFile: $uploadedIcon,
                path: $workspace->baseFilePath(),
                overwriteFileId: $workspace->icon?->file?->id
            );

            if ($file instanceof FileErrorCode) {
                \DB::rollBack();
                return WorkspaceErrorCode::FileIOFailed;
            }

            \App\Events\Workspace\UpdatedIcon::dispatch($icon, $workspaceAccount);

            \DB::commit();

            return $icon->unsetRelation('file');
        } catch (\Throwable $throwable) {
            \DB::rollBack();
            throw $throwable;
        }
    }
}

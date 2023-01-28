<?php

namespace App\Actions\Workspace;

use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Events\Workspace\CreatedWorkspace;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceParticipationSetting;
use App\Models\Workspace\WorkspaceParticipationSettingMethod;
use App\Models\Workspace\WorkspacePublicationSetting;
use App\Models\Workspace\WorkspacePublicationSettingState;
use Illuminate\Http\UploadedFile;

class CreateWorkspace
{
    public function __construct(private readonly UploadFile $uploadFile, private readonly RemoveFile $removeFile)
    {
    }

    /**
     * @throws \Throwable
     */
    public function handle(string $ownerId, string $url, string $name, ?UploadedFile $icon = null, string $description = null): Workspace|WorkspaceErrorCode
    {
        $user = User::find($ownerId);

        if (is_null($user)) {
            return WorkspaceErrorCode::UserNotExists;
        }

        // TODO: プランに応じて作成数の上限に達していたらエラーを返す

        $id = (string) \Str::orderedUuid();

        $iconFile = null;
        if ($icon) {
            $iconFile = $this->uploadFile->handle($icon, Workspace::buildFilePath($id));
        }

        try {
            return \DB::transaction(static function () use ($id, $ownerId, $url, $name, $description, $iconFile) {
                $workspace = Workspace::create([
                    'id' => $id,
                    'owner_id' => $ownerId,
                    'url' => $url,
                    'name' => $name,
                    'description' => $description,
                    'icon_id' => $iconFile?->id,
                ]);

                WorkspaceParticipationSetting::create([
                    'workspace_id' => $id,
                    'method' => WorkspaceParticipationSettingMethod::default()->value,
                ]);

                WorkspacePublicationSetting::create([
                    'workspace_id' => $id,
                    'state' => WorkspacePublicationSettingState::default()->value,
                ]);

                CreatedWorkspace::dispatch($workspace);

                return $workspace;
            });
        } catch (\Throwable $throwable) {
            if ($iconFile) {
                $this->removeFile->handle($iconFile->name, Workspace::buildFilePath($id), null, true);
            }

            throw $throwable;
        }
    }
}

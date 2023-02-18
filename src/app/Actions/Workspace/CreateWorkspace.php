<?php

namespace App\Actions\Workspace;

use App\Events\Workspace\CreatedWorkspace;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;

class CreateWorkspace
{
    /**
     * @return WorkspaceErrorCode|array{workspace: Workspace, workspace_account: WorkspaceAccount}
     * @throws \Throwable
     */
    public function handle(string $ownerId, string $url, string $name, string $description = null, bool $isPublic = false): WorkspaceErrorCode|array
    {
        $user = User::find($ownerId);
        if (is_null($user)) {
            return WorkspaceErrorCode::InvalidUserId;
        }

        \DB::beginTransaction();
        try {
            $createdWorkspaceNum = Workspace::query()->where('owner_id', $ownerId)->lockForUpdate()->count();

            if ($createdWorkspaceNum >= 1) {
                // TODO: プランに応じて上限を変更する
                \DB::rollBack();
                return WorkspaceErrorCode::TooManyWorkspaces;
            }

            $workspace = Workspace::create([
                'id' => (string)\Str::orderedUuid(),
                'owner_id' => $ownerId,
                'url' => $url,
                'name' => $name,
                'description' => $description,
                'is_public' => $isPublic,
            ]);

            $workspaceAccount = WorkspaceAccount::create([
                'id' => (string)\Str::orderedUuid(),
                'user_id' => $ownerId,
                'workspace_id' => $workspace->id,
                'role' => WorkspaceAccountRole::Administrator->value,
            ]);

            CreatedWorkspace::dispatch($workspace, $workspaceAccount);

            \DB::commit();

            return ['workspace' => $workspace, 'workspace_account' => $workspaceAccount];
        } catch (\Throwable $throwable) {
            \DB::rollBack();
            throw $throwable;
        }
    }
}

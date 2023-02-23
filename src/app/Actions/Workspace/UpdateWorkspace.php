<?php

namespace App\Actions\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;

class UpdateWorkspace
{
    public function handle(string $userId, string $workspaceId, string $url, string $name, string $description = null, bool $isPublic = false): Workspace|WorkspaceErrorCode
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

        $workspace->fill([
            'url' => $url,
            'name' => $name,
            'description' => $description,
            'is_public' => $isPublic,
        ])->save();

        \App\Events\Workspace\UpdatedWorkspace::dispatch($workspace, $workspaceAccount);

        return $workspace;

    }
}

<?php

namespace App\Actions\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;

class DeleteWorkspace
{
    public function handle(string $userId, string $workspaceId): WorkspaceErrorCode|true
    {
        $user = User::find($userId);
        if (is_null($user)) {
            return WorkspaceErrorCode::InvalidUserId;
        }

        $workspace  = Workspace::find($workspaceId);
        if (!($workspace instanceof Workspace)) {
            return WorkspaceErrorCode::InvalidWorkspaceId;
        }

        $workspaceAccount = $workspace->accounts()->where('user_id', $userId)->first();
        if (is_null($workspaceAccount) || $user->cannot('delete', [$workspace, $workspaceAccount])) {
            return WorkspaceErrorCode::Unauthorized;
        }

        $workspace->delete();

        \App\Events\Workspace\DeleteWorkspace::dispatch($workspace, $workspaceAccount);

        return true;
    }
}

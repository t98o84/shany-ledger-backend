<?php

namespace App\Models\Workspace\Policies;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkspacePolicy
{
    use HandlesAuthorization;

    public function update(User $user, Workspace $workspace, WorkspaceAccount $account): bool
    {
        return $user->id === $workspace->owner_id || $account->isAdminister();
    }
}

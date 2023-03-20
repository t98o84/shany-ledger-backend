<?php

namespace Tests\Feature\Traits;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;

trait HasWorkspace
{
    protected readonly User $owner;

    protected readonly Workspace $workspace;

    protected readonly WorkspaceAccount $workspaceOwnerAccount;

    protected function initWorkspace(): void
    {
        $this->owner = User::factory()->create();
        $this->workspace = Workspace::factory(['owner_id' => $this->owner->id])->create();
        $this->workspaceOwnerAccount = WorkspaceAccount::factory(['user_id' => $this->owner->id, 'workspace_id' => $this->workspace->id])->create();
    }
}

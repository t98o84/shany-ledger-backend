<?php

namespace App\Models\Ledger\Policies;

use App\Models\Ledger\Ledger;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Auth\Access\HandlesAuthorization;

class LedgerPolicy
{
    use HandlesAuthorization;

    public function store(User $user, Ledger $ledger, Workspace $workspace, ?WorkspaceAccount $account): bool
    {
        return $user->id === $workspace->owner_id || $account?->isEditorHigher();
    }
}

<?php

namespace Tests\Feature\Models\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testOwner_CreateOwner_OwnerReturned(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory(['owner_id' => $user->id])->create();
        WorkspaceAccount::factory([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
        ])->create();

        $this->assertInstanceOf(User::class, $workspace->owner);
    }

    public function testAccounts_CreateAccount_AccountCollectionReturned(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory(['owner_id' => $user->id])->create();
        WorkspaceAccount::factory([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
        ])->create();

        $this->assertInstanceOf(WorkspaceAccount::class, $workspace->accounts->first());
    }
}

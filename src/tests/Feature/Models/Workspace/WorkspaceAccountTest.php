<?php

namespace Tests\Feature\Models\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkspaceAccountTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

    private Workspace $workspace;

    private WorkspaceAccount $workspaceAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory(['owner_id' => $this->user->id])->create();
        $this->workspaceAccount = WorkspaceAccount::factory([
            'user_id' => $this->user->id,
            'workspace_id' => $this->workspace->id,
        ])->create();
    }

    public function testUser_CreateUser_UserReturned(): void
    {
        $this->assertInstanceOf(User::class, $this->workspaceAccount->user);
    }

    public function testWorkspace_CreateWorkspace_WorkspaceReturned(): void
    {
        $this->assertInstanceOf(Workspace::class, $this->workspaceAccount->workspace);
    }
}

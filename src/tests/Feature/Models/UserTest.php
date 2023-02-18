<?php

namespace Tests\Feature\Models;

use App\Models\Shared\File;
use App\Models\User;
use App\Models\UserAvatar;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testAvatar_CreateAvatar_AvatarReturned(): void
    {
        $user = User::factory()->hasAvatar()->create();

        $this->assertInstanceOf(UserAvatar::class, $user->avatar);
    }

    public function testAvatarFile_CreateAvatarFile_AvatarFileReturned(): void
    {
        $user = User::factory()->has(UserAvatar::factory()->hasFile(), 'avatar')->create();

        $this->assertInstanceOf(File::class, $user->avatar->file);
    }

    public function testWorkspaces_CreateWorkspace_WorkspaceCollectionReturned(): void
    {
        $user = User::factory()->create();
        $this->createWorkspace($user);

        $this->assertInstanceOf(Workspace::class, $user->workspaces->first());
    }

    public function testWorkspaceAccounts_CreateWorkspaceAccount_WorkspaceAccountCollectionReturned(): void
    {
        $user = User::factory()->create();
        $this->createWorkspace($user);

        $this->assertInstanceOf(WorkspaceAccount::class, $user->workspaceAccounts->first());
    }

    /**
     * @param User $user
     * @return array{workspace: Workspace, workspace_account: WorkspaceAccount}
     */
    private function createWorkspace(User $user): array
    {
        $workspace = Workspace::factory(['owner_id' => $user->id])->create();
        $workspaceAccount = WorkspaceAccount::factory([
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
        ])->create();

        return [
            'workspace' => $workspace,
            'workspace_account' => $workspaceAccount,
        ];
    }
}

<?php

namespace Tests\Feature\Actions\Workspace;

use App\Actions\Shared\RemoveFile;
use App\Actions\Workspace\DeleteIcon;
use App\Actions\Workspace\WorkspaceErrorCode;
use App\Models\Shared\File;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use App\Models\Workspace\WorkspaceIcon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteIconTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly DeleteIcon $action;

    private readonly Workspace $workspace;

    private readonly WorkspaceAccount $workspaceAccount;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Storage::fake();
        $this->action = new DeleteIcon(new RemoveFile());
        $this->workspace = Workspace::factory()->hasOwner()->has(WorkspaceIcon::factory()->hasFile(), 'icon')->create();
        $this->workspaceAccount = WorkspaceAccount::factory(['user_id' => $this->workspace->owner->id, 'workspace_id' => $this->workspace->id])->create();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_IconRegistered_IconDeleted(): void
    {
        Sanctum::actingAs($this->workspace->owner);

        $this->assertDatabaseCount(WorkspaceIcon::class, 1);
        $this->assertDatabaseCount(File::class, 1);

        $true = $this->action->handle(userId: $this->workspace->owner->id, workspaceId: $this->workspace->id);

        $this->assertTrue($true);
        $this->assertDatabaseCount(WorkspaceIcon::class, 0);
        $this->assertDatabaseCount(File::class, 0);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_IconNotRegistered_TrueReturned(): void
    {
        Sanctum::actingAs($this->workspace->owner);

        $this->action->handle(userId: $this->workspace->owner->id, workspaceId: $this->workspace->id);

        $this->assertDatabaseCount(WorkspaceIcon::class, 0);
        $this->assertDatabaseCount(File::class, 0);

        $true = $this->action->handle(userId: $this->workspace->owner->id, workspaceId: $this->workspace->id);

        $this->assertTrue($true);
    }
    /**
     * @dataProvider unauthorizedWorkspaceAccountRoleProvider
     * @throws \Throwable
     */
    public function testHandle_WorkspaceMember_UnauthorizedReturned(WorkspaceAccountRole $role): void
    {
        $user = User::factory()->create();
        WorkspaceAccount::factory(['user_id' => $user->id, 'workspace_id' => $this->workspace->id, 'role' => $role->value])->create();

        $error = $this->action->handle(userId: $user->id, workspaceId: $this->workspace->id);

        $this->assertSame(WorkspaceErrorCode::Unauthorized, $error);
    }

    public function unauthorizedWorkspaceAccountRoleProvider()
    {
        return [
            WorkspaceAccountRole::Editor->value => [WorkspaceAccountRole::Editor],
            WorkspaceAccountRole::Viewer->value => [WorkspaceAccountRole::Viewer],
            WorkspaceAccountRole::Guest->value => [WorkspaceAccountRole::Guest],
        ];
    }
}

<?php

namespace Tests\Feature\Actions\Workspace;

use App\Actions\Workspace\DeleteWorkspace;
use App\Actions\Workspace\WorkspaceErrorCode;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteWorkspaceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly DeleteWorkspace $action;

    private readonly Workspace $workspace;

    private readonly WorkspaceAccount $workspaceAccount;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Storage::fake();
        $this->action = new DeleteWorkspace();
        $this->workspace = Workspace::factory()->hasOwner()->create();
        $this->workspaceAccount = WorkspaceAccount::factory(['user_id' => $this->workspace->owner->id, 'workspace_id' => $this->workspace->id])->create();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_WorkspaceRegistered_WorkspaceDeleted(): void
    {
        Sanctum::actingAs($this->workspace->owner);

        $this->assertDatabaseCount(Workspace::class, 1);

        $true = $this->action->handle(userId: $this->workspace->owner->id, workspaceId: $this->workspace->id);

        $this->assertTrue($true);
        $this->assertSoftDeleted($this->workspace->getTable(), [
            'id' => $this->workspace->id,
        ]);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_WorkspaceRegistered_DeleteWorkspaceEventDispatched(): void
    {
        Sanctum::actingAs($this->workspace->owner);
        $this->action->handle(userId: $this->workspace->owner->id, workspaceId: $this->workspace->id);

        \Event::assertDispatched(\App\Events\Workspace\DeleteWorkspace::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_WorkspaceSoftDeleted_InvalidWorkspaceIdReturned(): void
    {
        Sanctum::actingAs($this->workspace->owner);

        $this->action->handle(userId: $this->workspace->owner->id, workspaceId: $this->workspace->id);

        $error = $this->action->handle(userId: $this->workspace->owner->id, workspaceId: $this->workspace->id);

        $this->assertSame(WorkspaceErrorCode::InvalidWorkspaceId, $error);
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

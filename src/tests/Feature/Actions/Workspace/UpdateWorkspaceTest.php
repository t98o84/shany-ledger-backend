<?php

namespace Tests\Feature\Actions\Workspace;

use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Actions\Workspace\UpdateWorkspace;
use App\Actions\Workspace\WorkspaceErrorCode;
use App\Models\Shared\File;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use App\Models\Workspace\WorkspaceParticipationSetting;
use App\Models\Workspace\WorkspaceParticipationSettingMethod;
use App\Models\Workspace\WorkspacePublicationSetting;
use App\Models\Workspace\WorkspacePublicationSettingState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UpdateWorkspaceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly UpdateWorkspace $updateWorkspace;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake();
        $this->updateWorkspace = new UpdateWorkspace();
    }

    public function testHandle_ValidData_WorkspaceReturned(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory(['owner_id' => $user->id])->create();
        WorkspaceAccount::factory(['user_id' => $user->id, 'workspace_id' => $workspace->id])->create();

        $updatedWorkspace = $this->updateWorkspace
            ->handle(userId: $user->id, workspaceId: $workspace->id,  url: 'test-url', name: 'test-name', description: 'test-description', isPublic: true);

        $this->assertInstanceOf(Workspace::class, $updatedWorkspace);
        $this->assertTrue(\Str::isUuid($updatedWorkspace->id));
        $this->assertSame($user->id, $updatedWorkspace->owner_id);
        $this->assertSame('test-url', $updatedWorkspace->url);
        $this->assertSame('test-name', $updatedWorkspace->name);
        $this->assertSame('test-description', $updatedWorkspace->description);
        $this->assertTrue($updatedWorkspace->is_public);
    }

    public function testHandle_ValidData_UpdateWorkspaceEventDispatched(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::factory(['owner_id' => $user->id])->create();
        WorkspaceAccount::factory(['user_id' => $user->id, 'workspace_id' => $workspace->id])->create();

        $this->updateWorkspace
            ->handle(userId: $user->id, workspaceId: $workspace->id,  url: 'test-url', name: 'test-name', description: 'test-description', isPublic: true);

        \Event::assertDispatched(\App\Events\Workspace\UpdatedWorkspace::class);
    }

    public function testHandle_Administer_WorkspaceReturned(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory(['owner_id' => $owner->id])->create();
        WorkspaceAccount::factory(['user_id' => $owner->id, 'workspace_id' => $workspace->id])->create();

        $administer = User::factory()->create();
        WorkspaceAccount::factory(['user_id' => $administer->id, 'workspace_id' => $workspace->id])->create();

        $updatedWorkspace = $this->updateWorkspace
            ->handle(userId: $administer->id, workspaceId: $workspace->id,  url: 'test-url', name: 'test-name');

        $this->assertInstanceOf(Workspace::class, $updatedWorkspace);
    }

    public function testHandle_NotWorkspaceMember_UnauthorizedReturned(): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory(['owner_id' => $owner->id])->create();
        WorkspaceAccount::factory(['user_id' => $owner->id, 'workspace_id' => $workspace->id])->create();

        $user = User::factory()->create();

        $error = $this->updateWorkspace->handle(userId: $user->id, workspaceId: $workspace->id,  url: 'test-url', name: 'test-name');

        $this->assertSame(WorkspaceErrorCode::Unauthorized, $error);
    }

    /**
     * @dataProvider unauthorizedWorkspaceAccountRoleProvider
     */
    public function testHandle_WorkspaceMember_UnauthorizedReturned(WorkspaceAccountRole $role): void
    {
        $owner = User::factory()->create();
        $workspace = Workspace::factory(['owner_id' => $owner->id])->create();
        WorkspaceAccount::factory(['user_id' => $owner->id, 'workspace_id' => $workspace->id])->create();

        $user = User::factory()->create();
        WorkspaceAccount::factory(['user_id' => $user->id, 'workspace_id' => $workspace->id, 'role' => $role->value])->create();

        $error = $this->updateWorkspace->handle(userId: $user->id, workspaceId: $workspace->id,  url: 'test-url', name: 'test-name');

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

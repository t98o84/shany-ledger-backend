<?php

namespace Tests\Feature\Actions\Workspace;

use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Actions\Workspace\CreateWorkspace;
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

class CreateWorkspaceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly CreateWorkspace $createWorkspace;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake();
        $this->createWorkspace = new CreateWorkspace();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_WorkspaceReturned(): void
    {
        $user = User::factory()->create();

        ['workspace' => $workspace, 'workspace_account' => $workspaceAccount] = $this->createWorkspace
            ->handle(ownerId: $user->id, url: 'test-url', name: 'test-name', description: 'test-description', isPublic: true);

        $this->assertInstanceOf(Workspace::class, $workspace);
        $this->assertTrue(\Str::isUuid($workspace->id));
        $this->assertSame($user->id, $workspace->owner_id);
        $this->assertSame('test-url', $workspace->url);
        $this->assertSame('test-name', $workspace->name);
        $this->assertSame('test-description', $workspace->description);
        $this->assertTrue($workspace->is_public);

        $this->assertInstanceOf(WorkspaceAccount::class, $workspaceAccount);
        $this->assertTrue(\Str::isUuid($workspaceAccount->id));
        $this->assertSame($user->id, $workspaceAccount->user_id);
        $this->assertSame($workspace->id, $workspaceAccount->workspace_id);
        $this->assertSame(WorkspaceAccountRole::Administrator->value, $workspaceAccount->role);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_CreateTowWorkspace_TooManyWorkspacesReturned(): void
    {
        $user = User::factory()->create();

        Workspace::factory(['owner_id' => $user->id])->create();
        $error = $this->createWorkspace->handle(ownerId: $user->id, url: 'test-url-2', name: 'test-name-2');

        $this->assertSame(WorkspaceErrorCode::TooManyWorkspaces, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_InvalidUserId_InvalidUserIdReturned(): void
    {
        $error = $this->createWorkspace->handle(ownerId: $this->faker->uuid(), url: 'test-url', name: 'test-name');

        $this->assertSame(WorkspaceErrorCode::InvalidUserId, $error);
    }
}

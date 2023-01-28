<?php

namespace Tests\Feature\Actions\Workspace;

use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Actions\Workspace\CreateWorkspace;
use App\Actions\Workspace\WorkspaceErrorCode;
use App\Models\Shared\File;
use App\Models\User;
use App\Models\Workspace\Workspace;
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
        $this->createWorkspace = new CreateWorkspace(
            new UploadFile(),
            new RemoveFile(),
        );
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_WorkspaceReturned(): void
    {
        $user = User::factory()->create();
        $icon = UploadedFile::fake()->image('test-icon');

        $workspace = $this->createWorkspace->handle(ownerId: $user->id, url: 'test-url', name: 'test-name', icon: $icon, description: 'test-description');

        $this->assertInstanceOf(Workspace::class, $workspace);
        $this->assertTrue(\Str::isUuid($workspace->id));
        $this->assertSame($user->id, $workspace->owner_id);
        $this->assertSame('test-url', $workspace->url);
        $this->assertSame('test-name', $workspace->name);
        $this->assertSame('test-description', $workspace->description);
        $this->assertInstanceOf(File::class, File::find($workspace->icon_id));
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_WorkspaceParticipationSettingCreated(): void
    {
        $user = User::factory()->create();
        $icon = UploadedFile::fake()->image('test-icon');

        $workspace = $this->createWorkspace->handle(ownerId: $user->id, url: 'test-url', name: 'test-name', icon: $icon, description: 'test-description');

        $setting = WorkspaceParticipationSetting::find($workspace->id);

        $this->assertInstanceOf(WorkspaceParticipationSetting::class, $setting);
        $this->assertSame(WorkspaceParticipationSettingMethod::default()->value, $setting->method);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_WorkspacePublicationSettingCreated(): void
    {
        $user = User::factory()->create();
        $icon = UploadedFile::fake()->image('test-icon');

        $workspace = $this->createWorkspace->handle(ownerId: $user->id, url: 'test-url', name: 'test-name', icon: $icon, description: 'test-description');

        $setting = WorkspacePublicationSetting::find($workspace->id);

        $this->assertInstanceOf(WorkspacePublicationSetting::class, $setting);
        $this->assertSame(WorkspacePublicationSettingState::default()->value, $setting->state);
    }


    /**
     * @throws \Throwable
     */
    public function testHandle_UserNotExists_UserNotExistsCodeReturned(): void
    {
        $icon = UploadedFile::fake()->image('test-icon');

        $error = $this->createWorkspace->handle(ownerId: $this->faker->uuid(), url: 'test-url', name: 'test-name', icon: $icon, description: 'test-description');

        $this->assertSame(WorkspaceErrorCode::UserNotExists, $error);
    }
}

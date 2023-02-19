<?php

namespace Tests\Feature\Actions\Workspace;

use App\Actions\Workspace\UpdateIcon;
use App\Actions\Shared\UploadFile;
use App\Models\Shared\File;
use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceIcon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateIconTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly UpdateIcon $action;

    private readonly User $owner;

    private readonly Workspace $workspace;

    private readonly WorkspaceAccount $workspaceAccount;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Storage::fake();
        $this->action = new UpdateIcon(new UploadFile());
        $this->owner = User::factory()->create();
        $this->workspace = Workspace::factory(['owner_id' => $this->owner->id])->create();
        $this->workspaceAccount = WorkspaceAccount::factory(['user_id' => $this->owner->id, 'workspace_id' => $this->workspace->id])->create();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_IconNotRegistered_IconRegistered(): void
    {
        $newIcon = UploadedFile::fake()->create('new_icon');
        Sanctum::actingAs($this->owner);

        $icon = $this->action->handle(userId: $this->owner->id, workspaceId: $this->workspace->id, uploadedIcon: $newIcon);

        $this->assertInstanceOf(WorkspaceIcon::class, $icon);
        $this->assertInstanceOf(File::class, $icon->file);
        $this->assertSame('new_icon', $icon->file->original_name);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidIcon_UpdateIconEventDispatched(): void
    {
        $newIcon = UploadedFile::fake()->create('new_icon');
        Sanctum::actingAs($this->owner);

        $this->action->handle(userId: $this->owner->id, workspaceId: $this->workspace->id, uploadedIcon: $newIcon);

       \Event::assertDispatched(\App\Events\Workspace\UpdateIcon::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_OverrideIcon_IconOverwritten(): void
    {
        Sanctum::actingAs($this->owner);

        $oldIcon = $this->action->handle(userId: $this->owner->id, workspaceId: $this->workspace->id, uploadedIcon: UploadedFile::fake()->create('old_icon'));

        $this->assertInstanceOf(File::class, $oldIcon->file);

        $uploadIcon = UploadedFile::fake()->create('new_icon');

        $newIcon = $this->action->handle(userId: $this->owner->id, workspaceId: $this->workspace->id, uploadedIcon: $uploadIcon);

        $this->assertInstanceOf(WorkspaceIcon::class, $newIcon);
        $this->assertInstanceOf(File::class, $newIcon->file);
        $this->assertSame('new_icon', $newIcon->file->original_name);
        $this->assertDatabaseCount(WorkspaceIcon::class, 1);
        $this->assertDatabaseCount(File::class, 1);
    }
}

<?php

namespace Tests\Feature\Controllers\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class IconControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $owner;

    private readonly Workspace $workspace;

    private readonly WorkspaceAccount $workspaceAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create();
        $this->workspace = Workspace::factory(['owner_id' => $this->owner->id])->create();
        $this->workspaceAccount = WorkspaceAccount::factory(['user_id' => $this->owner->id, 'workspace_id' => $this->workspace->id])->create();
    }

    public function testUpdate_ValidIcon_IconUrlResponse(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->putJson(route('workspace.update-icon', ['workspace' => $this->workspace->id]), [
            'icon' => UploadedFile::fake()->image('new-icon.jpg', 128, 128),
        ]);

        $response
            ->assertOK()
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('icon', 'string')
            );
    }

    public function testUpdate_NotImageFile_BadRequestResponse(): void
    {
        Sanctum::actingAs($this->owner);

        $response = $this->putJson(route('workspace.update-icon', ['workspace' => $this->workspace->id]));

        $response->assertStatus(400);
    }

    public function testUpdate_UnauthorizedUser_UnauthorizedResponse(): void
    {
        $response = $this->putJson(route('workspace.update-icon', ['workspace' => $this->workspace->id]), [
            'icon' => UploadedFile::fake()->image('new-icon.jpg'),
        ]);

        $response->assertUnauthorized();
    }
}

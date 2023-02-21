<?php

namespace Tests\Feature\Controllers\Workspace;

use App\Models\User;
use App\Models\Workspace\Workspace;
use App\Models\Workspace\WorkspaceAccount;
use App\Models\Workspace\WorkspaceAccountRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkspaceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly Workspace $workspace;

    private readonly WorkspaceAccount $workspaceAccount;

    protected function setUp(): void
    {
        parent::setUp();

        $this->workspace = Workspace::factory()->hasOwner()->create();
        $this->workspaceAccount = WorkspaceAccount::factory(['user_id' => $this->workspace->owner->id, 'workspace_id' => $this->workspace->id])->create();
    }

    public function testCreate_ValidData_StoredWorkspaceResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('workspace.store'), [
            'url' => 'test-url',
            'name' => 'test name',
            'description' => 'test description',
            'is_public' => false,
        ]);

        $response
            ->assertCreated()
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('workspace', fn(AssertableJson $json) => $json
                    ->whereType('id', 'string')
                    ->where('owner_id', $user->id)
                    ->where('url', 'test-url')
                    ->where('name', 'test name')
                    ->where('description', 'test description')
                    ->where('is_public', false)
                    ->etc()
                )->has('workspace_account', fn(AssertableJson $json) => $json
                    ->whereType('id', 'string')
                    ->whereType('workspace_id', 'string')
                    ->where('role', WorkspaceAccountRole::Administrator->value)
                    ->whereType('created_at', 'string')
                    ->whereType('updated_at', 'string')
                    ->has('user', fn(AssertableJson $json) => $json
                        ->where('id', $user->id)
                        ->where('name', $user->name)
                        ->where('avatar', null)
                    )->etc()
                )
            );
    }

    public function testCreate_InvalidUrl_BadRequestResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('workspace.store'), [
            'url' => 'invalid url',
            'name' => 'test name',
            'description' => 'test description',
            'is_public' => false,
        ]);

        $response
            ->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('errors', fn(AssertableJson $json) => $json
                    ->has('0', fn(AssertableJson $json) => $json
                        ->where('field', 'url')
                        ->whereType('title', 'string')
                        ->whereType('detail', 'null')
                    )
                )
                ->etc()
            );
    }

    public function testUpdate_ValidData_NoContentResponse(): void
    {
        Sanctum::actingAs($this->workspace->owner);

        $response = $this->patchJson(route('workspace.update', ['workspace' => $this->workspace->id]), [
            'url' => 'test-url',
            'name' => 'test name',
            'description' => 'test description',
            'is_public' => false,
        ]);

        $response->assertNoContent();
    }

    public function testUpdate_UnauthorizedUser_UnauthorizedResponse(): void
    {
        $response = $this->patchJson(route('workspace.update', ['workspace' => $this->workspace->id]), [
            'url' => 'invalid url',
            'name' => 'test name',
            'description' => 'test description',
            'is_public' => false,
        ]);

        $response->assertUnauthorized();
    }

    public function testDelete_ValidData_NoContentResponse(): void
    {
        Sanctum::actingAs($this->workspace->owner);

        $response = $this->deleteJson(route('workspace.delete-icon', ['workspace' => $this->workspace->id]));

        $response->assertNoContent();
    }

    public function testDelete_UnauthorizedUse_UnauthorizedResponse(): void
    {
        $response = $this->deleteJson(route('workspace.delete-icon', ['workspace' => $this->workspace->id]));

        $response->assertUnauthorized();
    }
}

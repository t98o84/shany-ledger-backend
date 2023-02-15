<?php

namespace Tests\Feature\Controllers\Workspace;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WorkspaceControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
                ->whereType('id', 'string')
                ->where('owner_id', $user->id)
                ->where('url', 'test-url')
                ->where('name', 'test name')
                ->where('description', 'test description')
                ->where('is_public', false)
                ->etc()
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

    public function testCreate_UnauthorizedUser_UnauthorizedResponse(): void
    {
        User::factory()->create();

        $response = $this->postJson(route('workspace.store'), [
            'url' => 'invalid url',
            'name' => 'test name',
            'description' => 'test description',
            'is_public' => false,
        ]);

        $response->assertUnauthorized();
    }
}

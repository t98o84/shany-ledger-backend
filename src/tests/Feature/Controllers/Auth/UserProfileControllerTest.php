<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testUpdate_ValidData_UpdatedUserResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('auth.user.update-profile', ['user' => $user->id]), [
            'email' => 'update-email@example.com',
            'name' => 'update name',
        ]);

        $response
            ->assertOK()
            ->assertJson(fn(AssertableJson $json) => $json
                ->where('id', $user->id)
                ->where('email', 'update-email@example.com')
                ->where('name', 'update name')
                ->where('avatar', null)
                ->missing('password')
            );
    }

    public function testUpdate_InvalidEmail_BadRequestResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->patchJson(route('auth.user.update-profile', ['user' => $user->id]), [
            'email' => 'invelid-email',
            'name' => $user->name,
        ]);

        $response
            ->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('errors', fn(AssertableJson $json) => $json
                    ->has('0', fn(AssertableJson $json) => $json
                        ->where('field', 'email')
                        ->whereType('title', 'string')
                        ->whereType('detail', 'null')
                    )
                )
                ->etc()
            );
    }

    public function testUpdate_UnauthorizedUser_UnauthorizedResponse(): void
    {
        $user = User::factory()->create();

        $response = $this->patchJson(route('auth.user.update-profile', ['user' => $user->id]), [
            'email' => $user->email,
            'name' => $user->name,
        ]);

        $response->assertUnauthorized();
    }

    public function testUpdate_OtherUser_ForbiddenResponse(): void
    {
        $authenticatedUser = User::factory()->create();
        $otherUser = User::factory()->create();
        Sanctum::actingAs($authenticatedUser);

        $response = $this->patchJson(route('auth.user.update-profile', ['user' => $otherUser->id]), [
            'email' => $authenticatedUser->email,
            'name' => $authenticatedUser->name,
        ]);

        $response->assertForbidden();
    }
}

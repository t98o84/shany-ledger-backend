<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
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

    public function testUpdateAvatar_ValidData_AvatarUrlResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(route('auth.user.update-avatar', ['user' => $user->id]), [
            'avatar' => UploadedFile::fake()->image('new-avatar.jpg'),
        ]);

        $response
            ->assertOK()
            ->assertJson(fn(AssertableJson $json) => $json
                ->whereType('avatar', 'string')
            );
    }

    public function testUpdateAvatar_NotImageFile_BadRequestResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(route('auth.user.update-avatar', ['user' => $user->id]), [
            'avatar' => UploadedFile::fake()->create('new-avatar.txt'),
        ]);

        $response
            ->assertStatus(400)
            ->assertJson(fn(AssertableJson $json) => $json
                ->has('errors', fn(AssertableJson $json) => $json
                    ->has('0', fn(AssertableJson $json) => $json
                        ->where('field', 'avatar')
                        ->whereType('title', 'string')
                        ->whereType('detail', 'null')
                    )
                )
                ->etc()
            );
    }

    public function testUpdateAvatar_AnotherUser_ForbiddenResponse(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(route('auth.user.update-avatar', ['user' => $anotherUser->id]), [
            'avatar' => UploadedFile::fake()->image('new-avatar.jpg'),
        ]);

        $response->assertForbidden();
    }

    public function testDeleteAvatar_ValidData_NoContentResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson(route('auth.user.delete-avatar', ['user' => $user->id]));

        $response->assertNoContent();
    }

    public function testDeleteAvatar_UnauthorizedUse_rUnauthorizedResponse(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson(route('auth.user.delete-avatar', ['user' => $user->id]));

        $response->assertUnauthorized();
    }
}

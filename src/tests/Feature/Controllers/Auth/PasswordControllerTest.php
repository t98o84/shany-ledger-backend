<?php

namespace Tests\Feature\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Models\Auth\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PasswordControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testSendPasswordResetLink_ValidData_NoContentResponse(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.send-password-reset-link', ['email' => $user->email]));

        $response->assertNoContent();
    }

    public function testSendPasswordResetLink_UserNotExists_BadRequestErrorExceptionThrown(): void
    {
        $this->expectException(BadRequestsErrorException::class);
        $this->withoutExceptionHandling()->postJson(route('auth.send-password-reset-link', ['email' => $this->faker->safeEmail()]));
    }

    public function testResetPassword_ValidData_NoContentResponse(): void
    {
        $user = User::factory()->create();
        $token = PasswordReset::createToken();
        PasswordReset::factory()->create([
            'email' => $user->email,
            'token' => PasswordReset::hashToken($token),
        ]);

        $response = $this->putJson(route('auth.reset-password', ['email' => $user->email, 'password' => 'password', 'token' => $token]));

        $response->assertNoContent();
    }

    public function testResetPassword_ExpiredToken_NoContentResponse(): void
    {
        $user = User::factory()->create();
        $token = PasswordReset::createToken();
        PasswordReset::factory()->expired()->create([
            'email' => $user->email,
            'token' => PasswordReset::hashToken($token),
        ]);

        $response = $this->putJson(route('auth.reset-password', ['email' => $user->email, 'password' => 'password', 'token' => $token]));

        $errorMessage = __("error/auth/index." . AuthErrorCode::ResetPasswordTokenExpired->value);
        $response->assertStatus(400)
            ->assertJson([
                'title' => $errorMessage
            ]);
    }

    public function testUpdate_ValidData_NoContentResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(
            route('auth.user.update-password', ['user' => $user->id]),
            ['old_password' => 'password', 'new_password' => 'new-password']
        );

        $response->assertNoContent();
    }

    public function testUpdate_InValidOldPassword_BadRequestResponse(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(
            route('auth.user.update-password', ['user' => $user->id]),
            ['old_password' => 'invalid-password', 'new_password' => 'new-password']
        );

        $response->assertStatus(400);
    }

    public function testUpdate_AnotherUser_ForbiddenResponse(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson(
            route('auth.user.update-password', ['user' => $anotherUser->id]),
            ['old_password' => 'invalid-password', 'new_password' => 'new-password']
        );

        $response->assertForbidden();
    }
}

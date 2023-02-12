<?php

namespace Tests\Feature\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function PHPUnit\Framework\assertJson;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testSignInWithEmailAndPassword_ValidData_OKStatusResponse(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.sign-in-with-email-and-password'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();
    }

    public function testSignInWithEmailAndPassword_ValidData_ResponseDataWasInTheFormatSpecified(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('auth.sign-in-with-email-and-password'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertJson(fn(AssertableJson $json) => $json
            ->whereType('access_token', 'string')
        );
    }

    public function testSignInWithEmailAndPassword_UserDoesNotExists_BadRequestResponse(): void
    {
        $response = $this->postJson(route('auth.sign-in-with-email-and-password'), [
            'email' => 'failed@example.com',
            'password' => 'password',
        ]);

        $response->assertStatus(400);
    }

    public function testSignOut_Authenticated_TokenDeleted(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('auth.sign-out'));

        $response->assertNoContent();
    }

    public function testSignOut_Unauthorized_UnauthorizedResponse(): void
    {
        $response = $this->postJson(route('auth.sign-out'));

        $response->assertUnauthorized();
    }
}

<?php

namespace Tests\Feature\Controllers\Auth;

use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSendEmailVerificationNotification_ValidData_NoContentResponse(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('auth.user.send-email-verification-notification', ['user' => $user->id]));

        $response->assertNoContent();
    }

    public function testSendEmailVerificationNotification_Unauthorized_UnauthorizedResponse(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->postJson(route('auth.user.send-email-verification-notification', ['user' => $user->id]));

        $response->assertUnauthorized();
    }

    public function testSendEmailVerificationNotification_EmailVerified_BadRequestErrorExceptionThrown(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->expectException(BadRequestsErrorException::class);
        $this->withoutExceptionHandling()->postJson(route('auth.user.send-email-verification-notification', ['user' => $user->id]));
    }
}

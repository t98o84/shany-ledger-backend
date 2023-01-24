<?php

namespace Tests\Feature\Controllers\Auth;

use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
}

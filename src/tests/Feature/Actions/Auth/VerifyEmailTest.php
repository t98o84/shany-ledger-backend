<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\VerifyEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testHandle_ValidData_NullReturned(): void
    {
        $user = User::factory()->unverified()->create();
        $verifyEmail = new VerifyEmail();

        $null = $verifyEmail->handle($user->id);

        $this->assertNull($null);
    }

    public function testHandle_UserNotExists_VerifyEmailUserNotExistsCodeReturned(): void
    {
        $verifyEmail = new VerifyEmail();

        $error = $verifyEmail->handle($this->faker->uuid());

        $this->assertSame(AuthErrorCode::VerifyEmailUserNotExists, $error);
    }

    public function testHandle_EmailVerified_VerifyEmailEmailVerifiedCodeReturned(): void
    {
        $user = User::factory()->create();
        $verifyEmail = new VerifyEmail();

        $error = $verifyEmail->handle($user->id);

        $this->assertSame(AuthErrorCode::VerifyEmailEmailVerified, $error);
    }
}

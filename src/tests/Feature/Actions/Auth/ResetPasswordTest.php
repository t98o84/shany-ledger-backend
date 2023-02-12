<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\ResetPassword;
use App\Models\Auth\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_TrueReturned(): void
    {
        $user = User::factory()->create();
        $token = PasswordReset::createToken();
        PasswordReset::create(['email' => $user->email, 'token' => PasswordReset::hashToken($token)]);
        $resetPassword = new ResetPassword();

        $true = $resetPassword->handle('password', $user->email, $token);

        $this->assertTrue($true);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_PasswordResetEventDispatched(): void
    {
        $user = User::factory()->create();
        $token = PasswordReset::createToken();
        PasswordReset::create(['email' => $user->email, 'token' => PasswordReset::hashToken($token)]);
        $resetPassword = new ResetPassword();

        $resetPassword->handle('password', $user->email, $token);

        \Event::assertDispatched(\App\Events\Auth\PasswordReset::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_PasswordResetEmailNotExists_InvalidEmailCodeReturned(): void
    {
        $user = User::factory()->make();
        $token = PasswordReset::createToken();
        PasswordReset::create(['email' => $user->email, 'token' => PasswordReset::hashToken($token)]);
        $resetPassword = new ResetPassword();

        $error = $resetPassword->handle('password', $user->email, $token);

        $this->assertSame(AuthErrorCode::InvalidEmail, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_InvalidToken_InvalidEmailCodeReturned(): void
    {
        $user = User::factory()->create();
        $token = PasswordReset::createToken();
        PasswordReset::create(['email' => $user->email, 'token' => PasswordReset::hashToken($token)]);
        $resetPassword = new ResetPassword();

        $error = $resetPassword->handle('password', $user->email, "invalid-$token");

        $this->assertSame(AuthErrorCode::InvalidEmail, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ExpiredToken_TokenExpiredCodeReturned(): void
    {
        $user = User::factory()->create();
        $rawToken = PasswordReset::createToken();
        $token = PasswordReset::create(['email' => $user->email, 'token' => PasswordReset::hashToken($rawToken)]);
        $token->created_at = Carbon::now()->subMinutes(PasswordReset::minutesToExpiration());
        $token->save();
        $resetPassword = new ResetPassword();

        $error = $resetPassword->handle('password', $user->email, $rawToken);

        $this->assertSame(AuthErrorCode::TokenExpired, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_UserNotExists_InvalidEmailCodeReturned(): void
    {
        $user = User::factory()->create();
        $token = PasswordReset::createToken();
        PasswordReset::create(['email' => $user->email, 'token' => PasswordReset::hashToken($token)]);
        $user->delete();
        $resetPassword = new ResetPassword();

        $error = $resetPassword->handle('password', $user->email, $token);

        $this->assertSame(AuthErrorCode::InvalidEmail, $error);
    }
}

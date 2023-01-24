<?php

namespace Tests\Unit\Models\Auth;

use App\Models\Auth\PasswordReset;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;

class PasswordResetTest extends TestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

        $this->createApplication();
    }

    public function testEqualsToken_ValidToken_TrueReturned(): void
    {
        $token = PasswordReset::createToken();
        $model = new PasswordReset(['email' => fake()->safeEmail(), 'token' => PasswordReset::hashToken($token)]);

        $true = $model->equalsToken($token);

        $this->assertTrue($true);
    }

    public function testEqualsToken_InvalidToken_FalseReturned(): void
    {
        $token = PasswordReset::createToken();
        $model = new PasswordReset(['email' => fake()->safeEmail(), 'token' => PasswordReset::hashToken($token)]);

        $true = $model->equalsToken("$token-tampered");

        $this->assertFalse($true);
    }

    public function testExpired_ValidData_FalseReturned(): void
    {
        $model = new PasswordReset(['email' => fake()->safeEmail(), 'token' => PasswordReset::hashToken(PasswordReset::createToken())]);
        $model->created_at = Carbon::now();

        $false = $model->expired();

        $this->assertFalse($false);
    }

    public function testExpired_InvalidData_TrueReturned(): void
    {
        $model = new PasswordReset(['email' => fake()->safeEmail(), 'token' => PasswordReset::hashToken(PasswordReset::createToken())]);
        $model->created_at = Carbon::now()->subMinutes(PasswordReset::minutesToExpiration() + 1);

        $true = $model->expired();

        $this->assertTrue($true);
    }
}

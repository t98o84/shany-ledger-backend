<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\VerifyEmail;
use App\Models\Shared\Signature;
use App\Models\User;
use Carbon\Carbon;
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
        $expiration = Carbon::now()->addMinutes(5);
        $hash = sha1($user->email);
        $signature = Signature::make([
            'user' => $user->id,
            'hash' => $hash,
        ], $expiration);

        $true = $verifyEmail->handle($user->id, $hash, $expiration->getTimestamp(), $signature->signature);

        $this->assertTrue($true);
    }

    public function testHandle_UserNotExists_InvalidUserIdCodeReturned(): void
    {
        $user = User::factory()->unverified()->make();
        $verifyEmail = new VerifyEmail();
        $expiration = Carbon::now()->addMinutes(5);
        $hash = sha1($user->email);
        $signature = Signature::make([
            'user' => $user->id,
            'hash' => $hash,
        ], $expiration);

        $error = $verifyEmail->handle($user->id, $hash, $expiration->getTimestamp(), $signature->signature);

        $this->assertSame(AuthErrorCode::InvalidUserId, $error);
    }

    public function testHandle_EmailVerified_EmailVerifiedCodeReturned(): void
    {
        $user = User::factory()->create();
        $verifyEmail = new VerifyEmail();
        $expiration = Carbon::now()->addMinutes(5);
        $hash = sha1($user->email);
        $signature = Signature::make([
            'user' => $user->id,
            'hash' => $hash,
        ], $expiration);


        $error = $verifyEmail->handle($user->id, $hash, $expiration->getTimestamp(), $signature->signature);

        $this->assertSame(AuthErrorCode::EmailVerified, $error);
    }

    public function testHandle_InvalidSignature_VerifyEmailInvalidSignatureCodeReturned(): void
    {
        $user = User::factory()->unverified()->create();
        $verifyEmail = new VerifyEmail();
        $expiration = Carbon::now()->addMinutes(5);
        $hash = sha1($user->email);
        $signature = Signature::make([
            'user' => $user->id,
            'hash' => $hash,
        ], $expiration);

        $error = $verifyEmail->handle($user->id, $hash, $expiration->clone()->addMinute()->getTimestamp(), $signature->signature);

        $this->assertSame(AuthErrorCode::InvalidSignature, $error);
    }

    public function testHandle_SignatureExpired_SignatureExpiredCodeReturned(): void
    {
        $user = User::factory()->unverified()->create();
        $verifyEmail = new VerifyEmail();
        $expiration = Carbon::now()->subMinute();
        $hash = sha1($user->email);
        $signature = Signature::make([
            'user' => $user->id,
            'hash' => $hash,
        ], $expiration);

        $error = $verifyEmail->handle($user->id, $hash, $expiration->getTimestamp(), $signature->signature);

        $this->assertSame(AuthErrorCode::SignatureExpired, $error);
    }
}

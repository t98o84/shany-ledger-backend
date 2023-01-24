<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SendPasswordResetLink;
use App\Mail\Auth\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendPasswordResetLinkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Queue::fake();
        \Mail::fake();
    }

    public function testHandle_ValidData_TrueReturned(): void
    {
        $user = User::factory()->create();
        $sendPasswordResetLink = new SendPasswordResetLink();

        $true = $sendPasswordResetLink->handle($user->email);

        $this->assertTrue($true);
    }

    public function testHandle_ValidData_MailQueued(): void
    {
        $user = User::factory()->create();
        $sendPasswordResetLink = new SendPasswordResetLink();

        $sendPasswordResetLink->handle($user->email);

        \Mail::assertQueued(PasswordReset::class);
    }

    public function testHandle_EmailNotExists_PasswordResetUserNotExistsCodeReturned(): void
    {
        $user = User::factory()->make();
        $sendPasswordResetLink = new SendPasswordResetLink();

        $error = $sendPasswordResetLink->handle($user->email);

        $this->assertSame(AuthErrorCode::PasswordResetUserNotExists, $error);
    }
}

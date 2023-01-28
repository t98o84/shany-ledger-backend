<?php

namespace Tests\Feature\Mail\Auth;

use App\Mail\Auth\PasswordResetNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PasswordResetNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        \Queue::fake();
        \Queue::assertNothingPushed();
        \Mail::fake();
        \Mail::assertNothingSent();
    }

    public function test_MailSent_MailQueued(): void
    {
        $user = User::factory()->make();

        \Mail::send(new PasswordResetNotification($user));

        \Mail::assertQueued(PasswordResetNotification::class);
    }

    public function test_MailSent_UserEmailSent(): void
    {
        $user = User::factory()->unverified()->make();

        \Mail::send(new PasswordResetNotification($user));

        \Mail::assertQueued(PasswordResetNotification::class, static function (PasswordResetNotification $mail) use ($user) {
            $mail->assertTo($user->email);
            return true;
        });
    }
}

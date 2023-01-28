<?php

namespace Tests\Feature\Mail\Auth;

use App\Mail\Auth\EmailVerification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
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
        $user = User::factory()->unverified()->make();

        \Mail::send(new EmailVerification($user, $this->faker->url()));

        \Mail::assertQueued(EmailVerification::class);
    }

    public function test_MailSent_UserEmailSent(): void
    {
        $user = User::factory()->unverified()->make();

        \Mail::send(new EmailVerification($user, $this->faker->url()));

        \Mail::assertQueued(EmailVerification::class, static function (EmailVerification $mail) use ($user) {
            $mail->assertTo($user->email);
            return true;
        });
    }
}

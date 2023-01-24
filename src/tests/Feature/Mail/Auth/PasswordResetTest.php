<?php

namespace Tests\Feature\Mail\Auth;

use App\Mail\Auth\PasswordReset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        \Queue::fake();
        \Mail::fake();
        \Mail::assertNothingSent();
    }

    public function test_MailSent_MailQueued(): void
    {
        $user = User::factory()->make();

        \Mail::send(new PasswordReset($user, $this->faker->url()));

        \Mail::assertQueued(PasswordReset::class);
    }

    public function test_MailSent_UserEmailSent(): void
    {
        $user = User::factory()->unverified()->make();

        \Mail::send(new PasswordReset($user, $this->faker->url()));

        \Mail::assertQueued(PasswordReset::class, static function (PasswordReset $mail) use ($user) {
            $mail->assertTo($user->email);
            return true;
        });
    }
}

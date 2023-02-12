<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SendEmailVerificationNotification;
use App\Mail\Auth\EmailVerification;
use App\Models\Shared\Signature;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendEmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly SendEmailVerificationNotification $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Queue::fake();
        \Mail::fake();
        $this->action = new SendEmailVerificationNotification;
    }

    public function testHandle_ValidData_FormatAsSpecified(): void
    {
        $user = User::factory()->unverified()->create();
        $verificationUrl = $this->action->handle($user->id);

        $queryKeys = array_map(
            static function (string $query) {
                return explode('=', $query)[0];
            },
            explode('&', parse_url($verificationUrl, PHP_URL_QUERY))
        );

        $this->assertStringStartsWith(route('auth.user.verify-email', ['user' => $user->id]), $verificationUrl);
        $this->assertEqualsCanonicalizing(['expiration', 'hash', 'signature'], $queryKeys);
    }

    public function testHandle_ValidData_MailQueued(): void
    {

        $user = User::factory()->unverified()->create();
        $this->action->handle($user->id);

        \Mail::assertQueued(EmailVerification::class);
    }

    public function testHandle_ValidData_SignaturesWereValid(): void
    {
        $user = User::factory()->unverified()->create();
        $verificationUrl = $this->action->handle($user->id);

        $queries = array_reduce(
            explode('&', parse_url($verificationUrl, PHP_URL_QUERY)),
            static function (array $carry, string $query) {
                [$key, $value] = explode('=', $query);
                $carry[$key] = $value;
                return $carry;
            },
            []
        );

        $signature = new Signature($queries['signature'], [
            'user' => $user->id,
            'hash' => urldecode($queries['hash']),
        ], new Carbon((int)$queries['expiration']));

        $this->assertTrue($signature->valid());
    }

    public function testHandle_UserNotExists_InvalidUserIdCodeReturned(): void
    {
        $error = $this->action->handle($this->faker->uuid());

        $this->assertSame(AuthErrorCode::InvalidUserId, $error);
    }

    public function testHandle_EmailVerified_EmailVerifiedCodeReturned(): void
    {
        $user = User::factory()->create();
        $error = $this->action->handle($user->id);

        $this->assertSame(AuthErrorCode::EmailVerified, $error);
    }
}

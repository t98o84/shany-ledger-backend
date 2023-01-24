<?php

namespace Tests\Feature\Controllers\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SendEmailVerificationNotification;
use App\Exceptions\ProblemDetails\BadRequestsErrorException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Carbon\Carbon;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EmailVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSendEmailVerificationNotification_ValidData_NoContentResponse(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson(route('auth.user.send-email-verification-notification', ['user' => $user->id]));

        $response->assertNoContent();
    }

    public function testSendEmailVerificationNotification_Unauthorized_UnauthorizedResponse(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->postJson(route('auth.user.send-email-verification-notification', ['user' => $user->id]));

        $response->assertUnauthorized();
    }

    public function testSendEmailVerificationNotification_EmailVerified_BadRequestErrorExceptionThrown(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->expectException(BadRequestsErrorException::class);
        $this->withoutExceptionHandling()->postJson(route('auth.user.send-email-verification-notification', ['user' => $user->id]));
    }

    public function testVerify_ValidData_RedirectResponse(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        \Event::fake();
        \Mail::fake();
        $createVerificationUrl = new SendEmailVerificationNotification();
        $verificationUrl = $createVerificationUrl->handle($user->id);
        $user->tokens()->delete();

        $response = $this->getJson($verificationUrl);

        $response->assertRedirect(config('app.front_url') . "/email-verified?user=$user->id");
    }

    public function testVerify_UserNotExists_RedirectResponse(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        \Event::fake();
        \Mail::fake();
        $createVerificationUrl = new SendEmailVerificationNotification();
        $verificationUrl = $createVerificationUrl->handle($user->id);
        $user->delete();

        $response = $this->getJson($verificationUrl);

        $errorMessage = __("error/auth/index." . AuthErrorCode::VerifyEmailUserNotExists->value);
        $response->assertRedirect(config('app.front_url') . "/email-verification?user=$user->id&error=$errorMessage");
    }

    public function testVerify_EmailVerified_RedirectResponse(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        \Event::fake();
        \Mail::fake();
        $createVerificationUrl = new SendEmailVerificationNotification();
        $verificationUrl = $createVerificationUrl->handle($user->id);

        $this->getJson($verificationUrl);
        $response = $this->getJson($verificationUrl);

        $errorMessage = __("error/auth/index." . AuthErrorCode::VerifyEmailEmailVerified->value);
        $response->assertRedirect(config('app.front_url') . "/email-verification?user=$user->id&error=$errorMessage");
    }

    public function testVerify_FalsifiedSignature_RedirectResponse(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);

        \Event::fake();
        \Mail::fake();
        $createVerificationUrl = new SendEmailVerificationNotification();
        $verificationUrl = $createVerificationUrl->handle($user->id);

        $response = $this->getJson("$verificationUrl-falsified");

        $errorMessage = __("error/auth/index." . AuthErrorCode::VerifyEmailInvalidSignature->value);
        $response->assertRedirect(config('app.front_url') . "/email-verification?user=$user->id&error=$errorMessage");
    }

    public function testVerify_ExpiredSignature_RedirectResponse(): void
    {
        $user = User::factory()->unverified()->create();
        Sanctum::actingAs($user);
        \Event::fake();
        \Mail::fake();

        $createVerificationUrl = new SendEmailVerificationNotification();
        $verificationUrl = $createVerificationUrl->handle($user->id);

        Carbon::setTestNow(Carbon::now()->addDay());

        $response = $this->getJson($verificationUrl);

        $errorMessage = __("error/auth/index." . AuthErrorCode::VerifyEmailSignatureExpired->value);
        $response->assertRedirect(config('app.front_url') . "/email-verification?user=$user->id&error=$errorMessage");
    }
}

<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SignInWithEmailAndPassword;
use App\Events\Auth\SignedIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignInWithEmailAndPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly SignInWithEmailAndPassword $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        $this->action = new SignInWithEmailAndPassword;
    }

    public function testHandle_ValidData_AccessTokenReturned(): void
    {
        $user = User::factory()->create();
        $token = $this->action->handle($user->email, 'password');

        $this->assertIsString($token);
    }

    public function testHandle_ValidData_SignedInEventDispached(): void
    {
        $user = User::factory()->create();
        $this->action->handle($user->email, 'password');

        \Event::assertDispatched(SignedIn::class);
    }

    public function testHandle_EmailNotExists_SignInFailedErrorCodeReturned(): void
    {
        $errorCode = $this->action->handle('test@example.com', 'password');

        $this->assertInstanceOf(AuthErrorCode::class, $errorCode);
        $this->assertSame(AuthErrorCode::SignInFailed, $errorCode);
    }

    public function testHandle_WrongPassword_SignInFailedErrorCodeReturned(): void
    {
        $user = User::factory()->create();
        $errorCode = $this->action->handle($user->email, 'wrong-password');

        $this->assertInstanceOf(AuthErrorCode::class, $errorCode);
        $this->assertSame(AuthErrorCode::SignInFailed, $errorCode);
    }
}

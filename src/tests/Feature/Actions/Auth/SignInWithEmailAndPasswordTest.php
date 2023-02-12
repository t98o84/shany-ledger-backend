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

    public function testHandle_ValidData_SignedInEventDispatched(): void
    {
        $user = User::factory()->create();
        $this->action->handle($user->email, 'password');

        \Event::assertDispatched(SignedIn::class);
    }

    public function testHandle_EmailNotExists_InvalidEmailOrPasswordCodeReturned(): void
    {
        $error = $this->action->handle('test@example.com', 'password');

        $this->assertSame(AuthErrorCode::InvalidEmailOrPassword, $error);
    }

    public function testHandle_WrongPassword_InvalidEmailOrPasswordCodeReturned(): void
    {
        $user = User::factory()->create();
        $error = $this->action->handle($user->email, 'wrong-password');

        $this->assertSame(AuthErrorCode::InvalidEmailOrPassword, $error);
    }
}

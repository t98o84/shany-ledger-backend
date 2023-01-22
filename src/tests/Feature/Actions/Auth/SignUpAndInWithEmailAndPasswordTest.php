<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SignInWithEmailAndPassword;
use App\Actions\Auth\SignUpAndInWithEmailAndPassword;
use App\Actions\Auth\SignUpWithEmailAndPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignUpAndInWithEmailAndPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly SignUpAndInWithEmailAndPassword $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        $this->action = new SignUpAndInWithEmailAndPassword(
            new SignUpWithEmailAndPassword(),
            new SignInWithEmailAndPassword()
        );
    }

    public function testHandle_ValidData_FormatAsSpecified(): void
    {
        $userAndToken = $this->action->handle('test@example.com', 'password');

        $this->assertInstanceOf(User::class, $userAndToken['user']);
        $this->assertIsString($userAndToken['token']);
    }


    public function testHandle_EmailExists_SignUpEmailExistsCodeReturned(): void
    {
        $user = User::factory()->create();
        $errorCode = $this->action->handle($user->email, 'password');

        $this->assertInstanceOf(AuthErrorCode::class, $errorCode);
        $this->assertSame(AuthErrorCode::SignUpEmailExists, $errorCode);
    }
}

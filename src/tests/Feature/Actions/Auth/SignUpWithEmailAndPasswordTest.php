<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\SignUpWithEmailAndPassword;
use App\Events\Auth\SignedUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SignUpWithEmailAndPasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly SignUpWithEmailAndPassword $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        $this->action = new SignUpWithEmailAndPassword;
    }

    public function testHandle_ValidData_FormatAsSpecified(): void
    {
        $user = $this->action->handle('test@example.com', 'password');

        $this->assertTrue(\Str::isUuid($user->id));
        $this->assertSame('test@example.com', $user->email);
        $this->assertSame('test', $user->name);
        $this->assertTrue($user->equalPassword('password'));
    }

    public function testHandle_ValidData_SignedUpEventDispatched(): void
    {
        $this->action->handle('test@example.com', 'password');
        \Event::assertDispatched(SignedUp::class);
    }

    public function testHandle_EmailExists_AlreadyRegisteredEmailCodeReturned(): void
    {
        $user = User::factory()->create();
        $error = $this->action->handle($user->email, 'password');

        $this->assertSame(AuthErrorCode::AlreadyRegisteredEmail, $error);
    }
}

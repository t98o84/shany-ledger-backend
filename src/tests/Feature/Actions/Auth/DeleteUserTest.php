<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\DeleteUser;
use App\Actions\Auth\SignInWithEmailAndPassword;
use App\Actions\Auth\SignOut;
use App\Actions\Auth\SignUpWithEmailAndPassword;
use App\Events\Auth\SignedOut;
use App\Models\Auth\PersonalAccessToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly SignInWithEmailAndPassword $signIn;

    private readonly SignUpWithEmailAndPassword $signUp;

    private readonly DeleteUser $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        $this->signIn = new SignInWithEmailAndPassword();
        $this->signUp = new SignUpWithEmailAndPassword();
        $this->action = new DeleteUser();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_SoftDeletedUser(): void
    {
        $user = User::factory()->create();
        $token = $this->signIn->handle($user->email, 'password');
        Sanctum::actingAs($user);

        $true = $this->action->handle($user->id, $token);

        $this->assertTrue($true);
        $this->assertSoftDeleted($user);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_CanBeRegisteredWithTheSameEmailAddressAsTheDeletedUser(): void
    {
        $user = User::factory()->create();
        $token = $this->signIn->handle($user->email, 'password');
        Sanctum::actingAs($user);

        $this->action->handle($user->id, $token);
        $newUser = $this->signUp->handle($user->email, 'password');

        $this->assertSame($user->email, $newUser->email);
    }
}

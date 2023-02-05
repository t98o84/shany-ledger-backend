<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\DeleteUser;
use App\Actions\Auth\SignInWithEmailAndPassword;
use App\Actions\Auth\SignOut;
use App\Actions\Auth\SignUpWithEmailAndPassword;
use App\Actions\Auth\UpdatePassword;
use App\Actions\Auth\UpdateUserProfile;
use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Events\Auth\SignedOut;
use App\Events\Auth\UnverifiedEmail;
use App\Events\Auth\UpdatedPassword;
use App\Events\Auth\UpdatedUserProfile;
use App\Models\Auth\PersonalAccessToken;
use App\Models\Shared\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly UpdatePassword $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        $this->action = new UpdatePassword();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_ProfileUpdated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updatedUser = $this->action->handle($user->id, 'password', 'new-password');

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertTrue($updatedUser->equalPassword('new-password'));
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_UpdatedUserProfileEventDispatched(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->action->handle($user->id, 'password', 'new-password');

        \Event::assertDispatched(UpdatedPassword::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_UserNotExists_UserNotExistsCodeReturned(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $error = $this->action->handle("invalid-$user->id", 'password', 'new-password');

        $this->assertSame(AuthErrorCode::UserNotExists, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AnotherUser_ForbiddenCodeReturned(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $error = $this->action->handle($anotherUser->id, 'password', 'new-password');

        $this->assertSame(AuthErrorCode::Forbidden, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_Unauthenticated_ForbiddenCodeReturned(): void
    {
        $user = User::factory()->create();

        $error = $this->action->handle($user->id, 'password', 'new-password');

        $this->assertSame(AuthErrorCode::Forbidden, $error);
    }
}

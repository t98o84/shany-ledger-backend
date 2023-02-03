<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\DeleteUser;
use App\Actions\Auth\SignInWithEmailAndPassword;
use App\Actions\Auth\SignOut;
use App\Actions\Auth\SignUpWithEmailAndPassword;
use App\Actions\Auth\UpdateUserProfile;
use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Events\Auth\SignedOut;
use App\Events\Auth\UnverifiedEmail;
use App\Events\Auth\UpdatedUserProfile;
use App\Models\Auth\PersonalAccessToken;
use App\Models\Shared\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UpdateUserProfileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly UpdateUserProfile $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Storage::fake();
        $this->action = new UpdateUserProfile();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_ProfileUpdated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updatedUser = $this->action->handle($user->id, "update $user->name", "update-$user->email");

        $this->assertInstanceOf(User::class, $updatedUser);
        $this->assertSame("update $user->name", $updatedUser->name);
        $this->assertSame("update-$user->email", $updatedUser->email);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_UpdatedUserProfileEventDispatched(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->action->handle($user->id, "update $user->name", $user->email);

        \Event::assertDispatched(UpdatedUserProfile::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_UpdateEmail_EmailUnverified(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $updatedUser = $this->action->handle($user->id,  $user->name, "update-$user->email");

        $this->assertNull($updatedUser->email_verified_at);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_UpdateEmail_UnverifiedEmailEventDispatched(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->action->handle($user->id,  $user->name, "update-$user->email");

        \Event::assertDispatched(UnverifiedEmail::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_UserNotExists_UserNotExistsCodeReturned(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        Sanctum::actingAs($user);

        $error = $this->action->handle($user->id . 'failed',  $user->name, 'update@example.com');

        $this->assertSame(AuthErrorCode::UserNotExists, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AnotherUser_ForbiddenCodeReturned(): void
    {
        $user = User::factory()->create();
        $anotherUse = User::factory()->create();
        Sanctum::actingAs($user);

        $error = $this->action->handle($anotherUse->id,  $anotherUse->name, $anotherUse->email);

        $this->assertSame(AuthErrorCode::Forbidden, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_Unauthenticated_ForbiddenCodeReturned(): void
    {
        $user = User::factory()->create();

        $error = $this->action->handle($user->id,  $user->name, $user->email);

        $this->assertSame(AuthErrorCode::Forbidden, $error);
    }
}

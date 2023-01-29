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
        \Event::assertNothingDispatched();
        \Storage::fake();
        $this->action = new UpdateUserProfile(
            new UploadFile(),
            new RemoveFile(),
        );
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_ProfileUpdated(): void
    {
        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar');
        Sanctum::actingAs($user);

        $true = $this->action->handle($user->id, "update $user->name", "update-$user->email");
        $updatedUser = User::find($user->id);

        $this->assertTrue($true);
        $this->assertSame("update $user->name", $updatedUser->name);
        $this->assertSame("update-$user->email", $updatedUser->email);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_UploadAvatar_AvatarUpdated(): void
    {
        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar');
        Sanctum::actingAs($user);

        $this->action->handle($user->id, $user->name, $user->email, false, $avatar);
        $updatedUser = User::find($user->id);

        $this->assertTrue(\Str::isUuid($updatedUser->avatar_id));
        \Storage::assertExists(User::buildFilePath($updatedUser->id) . "/{$updatedUser->avatar->name}");
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_RemoveAvatar_AvatarRemoved(): void
    {
        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar');
        Sanctum::actingAs($user);

        $this->action->handle($user->id, $user->name, $user->email, false, $avatar);
        $avatarName = User::find($user->id)->avatar->name;

        $this->action->handle($user->id, $user->name, $user->email, true);
        $updatedUser = User::find($user->id);

        $this->assertNull($updatedUser->avatar_id);
        \Storage::assertMissing(User::buildFilePath($updatedUser->id) . "/{$avatarName}");
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_RemoveAndUploadAvatar_AvatarUpdated(): void
    {
        $user = User::factory()->create();
        $avatar = UploadedFile::fake()->image('avatar');
        Sanctum::actingAs($user);

        $this->action->handle($user->id, $user->name, $user->email, false, $avatar);
        $oldAvatarId = User::find($user->id)->avatar_id;

        $newAvatar = UploadedFile::fake()->image('avatar');
        $this->action->handle($user->id, $user->name, $user->email, true, $newAvatar);
        $updatedUser = User::find($user->id);

        $this->assertNotSame($oldAvatarId, $updatedUser->avatar_id);
        $this->assertTrue(\Str::isUuid($updatedUser->avatar_id));
        \Storage::assertExists(User::buildFilePath($updatedUser->id) . "/{$updatedUser->avatar->name}");
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
    public function testHandle_UpdateTheProfileOfANonAuthenticatingUser_InvalidRequestCodeReturned(): void
    {
        $user = User::factory()->create();
        $unauthorizedUser = User::factory()->create();
        Sanctum::actingAs($user);

        $error = $this->action->handle($unauthorizedUser->id,  $unauthorizedUser->name, $unauthorizedUser->email);

        $this->assertSame(AuthErrorCode::InvalidRequest, $error);
    }
}

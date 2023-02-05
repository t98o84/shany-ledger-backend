<?php

namespace Tests\Feature\Actions\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Actions\Auth\DeleteUserAvatar;
use App\Actions\Auth\UpdateUserAvatar;
use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Models\Shared\File;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeleteUserAvatarTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly DeleteUserAvatar $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Storage::fake();
        $this->action = new DeleteUserAvatar(new RemoveFile());
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AvatarRegistered_AvatarDeleted(): void
    {
        $user = User::factory()->has(UserAvatar::factory()->hasFile(), 'avatar')->create();
        Sanctum::actingAs($user);

        $this->assertInstanceOf(File::class, $user->avatar->file);

        $true = $this->action->handle($user->id);

        $this->assertTrue($true);
        $this->assertDatabaseCount(UserAvatar::class, 0);
        $this->assertDatabaseCount(File::class, 0);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AvatarNotRegistered_TrueReturned(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $true = $this->action->handle($user->id);

        $this->assertTrue($true);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AnotherUser_ForbiddenCodeReturned(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        Sanctum::actingAs($user);

        $error = $this->action->handle($anotherUser->id);

        $this->assertSame(AuthErrorCode::Forbidden, $error);
    }


    /**
     * @throws \Throwable
     */
    public function testHandle_NotExistsUser_UserNotExistsCodeCodeReturned(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $error = $this->action->handle("invalid-$user->id");

        $this->assertSame(AuthErrorCode::UserNotExists, $error);
    }
}

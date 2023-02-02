<?php

namespace Tests\Feature\Actions\Auth;

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

class UpdateUserAvatarTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private readonly UpdateUserAvatar $action;

    protected function setUp(): void
    {
        parent::setUp();

        \Event::fake();
        \Storage::fake();
        $this->action = new UpdateUserAvatar(
            new UploadFile(),
            new RemoveFile(),
        );
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AvatarNotRegistered_AvatarRegistered(): void
    {
        $user = User::factory()->create();
        $newAvatar = UploadedFile::fake()->create('new_avatar');
        Sanctum::actingAs($user);

        $avatar = $this->action->handle($user->id, $newAvatar);

        $this->assertInstanceOf(UserAvatar::class, $avatar);
        $this->assertInstanceOf(File::class, $avatar->file);
        $this->assertSame('new_avatar', $avatar->file->original_name);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_AvatarRegistered_AvatarOverwritten(): void
    {
        $user = User::factory()->has(UserAvatar::factory()->hasFile(), 'avatar')->create();
        $newAvatar = UploadedFile::fake()->create('new_avatar');
        Sanctum::actingAs($user);

        $this->assertInstanceOf(File::class, $user->avatar->file);

        $avatar = $this->action->handle($user->id, $newAvatar);

        $this->assertInstanceOf(UserAvatar::class, $avatar);
        $this->assertInstanceOf(File::class, $avatar->file);
        $this->assertSame('new_avatar', $avatar->file->original_name);
        $this->assertDatabaseCount(UserAvatar::class, 1);
        $this->assertDatabaseCount(File::class, 1);
    }
}

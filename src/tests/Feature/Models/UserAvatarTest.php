<?php

namespace Tests\Feature\Models;

use App\Models\Shared\File;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAvatarTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testFile_CreateFile_FileReturned(): void
    {
        $avatar = UserAvatar::factory()->hasFile()->create();

        $this->assertInstanceOf(File::class, $avatar->file);
    }

    public function testUser_CreateUser_UserReturned(): void
    {
        $avatar = UserAvatar::factory()->create();

        $this->assertInstanceOf(User::class, $avatar->user);
    }

    public function testUrl_CreateUserAvatar_UrlReturned(): void
    {
        $avatar = UserAvatar::factory()->hasFile()->create();

        $this->assertSame($avatar->file->url, $avatar->url());
    }
}

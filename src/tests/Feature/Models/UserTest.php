<?php

namespace Tests\Feature\Models;

use App\Mail\Auth\EmailVerification;
use App\Models\Shared\File;
use App\Models\User;
use App\Models\UserAvatar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testAvatar_CreateAvatar_AvatarReturned(): void
    {
        $user = User::factory()->hasAvatar()->create();

        $this->assertInstanceOf(UserAvatar::class, $user->avatar);
    }

    public function testAvatarFile_CreateAvatarFile_AvatarFileReturned(): void
    {
        $user = User::factory()->has(UserAvatar::factory()->hasFile(), 'avatar')->create();

        $this->assertInstanceOf(File::class, $user->avatar->file);
    }
}

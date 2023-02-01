<?php

namespace Tests\Feature\Models\Shared;

use App\Models\Shared\File;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
    }

    public function testName_DeepPath_OnlyThaFileNameIsReturned(): void
    {
        $file = File::factory()->make(['path' => 'dir1/dir2/name.jpg']);

        $this->assertSame('name.jpg', $file->name);
    }

    public function testUrl_LocalDisk_UrlReturned(): void
    {
        $file = File::factory()->make(['disk' => 'local', 'path' => 'dir1/dir2/name.jpg']);

        $this->assertSame("/storage/dir1/dir2/$file->name", $file->url);
    }

    public function testFileable_User_UserReturned(): void
    {
        $file = File::factory()->for(User::factory(), 'fileable')->make(['disk' => 'local', 'path' => 'dir1/dir2/name.jpg']);

        $this->assertInstanceOf(User::class, $file->fileable);
    }
}

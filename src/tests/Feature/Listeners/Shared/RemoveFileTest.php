<?php

namespace Tests\Feature\Listeners\Shared;

use App\Events\Shared\OverwrittenFile;
use App\Listeners\Auth\SendPasswordResetNotification;
use App\Listeners\Shared\RemoveFile;
use App\Models\Shared\File;
use App\Models\User;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RemoveFileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        \Queue::fake();
        \Storage::fake();
    }

    public function test_DispatchOverwriteFileEvent_RemoveFileListenerQueued(): void
    {
       \Queue::assertNotPushed(CallQueuedListener::class, static fn($listener) => $listener->class === SendPasswordResetNotification::class);

        $file = File::factory()->for(User::factory(), 'fileable')->create();
        OverwrittenFile::dispatch($file);

        \Queue::assertPushed(
            CallQueuedListener::class,
            static fn($listener) => $listener->class === RemoveFile::class
        );
    }

    public function test_OverwriteFileEvent_FileRemoved(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');
        $path = Storage::putFile('/', $uploadedFile);
        $file = File::factory()->for(User::factory(), 'fileable')->create([
            'path' => $path,
            'original_name' => $uploadedFile->getClientOriginalName()
        ]);
        $event = new OverwrittenFile($file);

        \Storage::assertExists($path);

        (new RemoveFile())->handle($event);

        \Storage::assertMissing($path);
    }
}

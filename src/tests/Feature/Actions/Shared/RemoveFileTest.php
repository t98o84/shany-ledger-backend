<?php

namespace Tests\Feature\Actions\Shared;

use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Events\Shared\RemovedFile;
use \App\Events\Shared\UploadedFile as UploadedFileEvent;
use App\Models\Shared\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RemoveFileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private UploadFile $uploadFile;

    private RemoveFile $removeFile;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake();
        $this->uploadFile = new UploadFile();
        $this->removeFile = new RemoveFile();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_TrueReturned(): void
    {
        $file = File::factory()->for(User::factory(), 'fileable')->create();
        $true = $this->removeFile->handle($file->id);

        $this->assertTrue($true);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_RemovedFileEventDispatched(): void
    {
        $file = File::factory()->for(User::factory(), 'fileable')->create();

        $this->removeFile->handle($file->id);

        \Event::assertDispatched(RemovedFile::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_FileMissing(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');
        $path = \Storage::putFile('/', $uploadedFile);
        $file = File::factory()->for(User::factory(), 'fileable')->create(['path' => $path]);

        \Storage::assertExists($file->path);

        $this->removeFile->handle($file->id);

        \Storage::assertMissing($file->path);
        $this->assertDatabaseEmpty(File::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_WithDirectory_DirectoryRemoved(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');
        $path = \Storage::putFile('/dir1', $uploadedFile);
        $file = File::factory()->for(User::factory(), 'fileable')->create(['path' => $path]);

        \Storage::assertExists($file->path);

        $this->removeFile->handle($file->id, true);

        \Storage::assertMissing("/dir1");
        $this->assertDatabaseEmpty(File::class);
    }
}

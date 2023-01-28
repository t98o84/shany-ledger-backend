<?php

namespace Tests\Feature\Actions\Shared;

use App\Actions\Shared\RemoveFile;
use App\Actions\Shared\UploadFile;
use App\Events\Shared\RemovedFile;
use \App\Events\Shared\UploadedFile as UploadedFileEvent;
use App\Models\Shared\File;
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
        $uploadedFile = UploadedFile::fake()->create('test');
        $file = $this->uploadFile->handle($uploadedFile);

        $true = $this->removeFile->handle($file->id);

        $this->assertTrue($true);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_RemovedFileEventDispatched(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');
        $file = $this->uploadFile->handle($uploadedFile);

        $this->removeFile->handle($file->id);

        \Event::assertDispatched(RemovedFile::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_FileMissing(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');
        $file = $this->uploadFile->handle($uploadedFile);

        $this->removeFile->handle($file->id);

        \Storage::assertMissing("/$file->name");
        $this->assertNull(File::find($file->id));
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_WithDirectory_DirectoryRemoved(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');
        $file = $this->uploadFile->handle($uploadedFile, '/dir1');

        $this->removeFile->handle($file->id, '/dir1', null, true);

        \Storage::assertMissing("/dir1");
        $this->assertNull(File::find($file->id));
    }
}

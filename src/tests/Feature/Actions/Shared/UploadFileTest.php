<?php

namespace Tests\Feature\Actions\Shared;

use App\Actions\Shared\FileErrorCode;
use App\Actions\Shared\UploadFile;
use App\Events\Shared\OverwrittenFile;
use \App\Events\Shared\UploadedFile as UploadedFileEvent;
use App\Models\Shared\File;
use App\Models\Shared\FileVisibility;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadFileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private UploadFile $uploadFile;

    private Model $fileable;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake();
        $this->uploadFile = new UploadFile();
        $this->fileable = User::factory()->create();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_FileReturned(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');

        $file = $this->uploadFile->handle($this->fileable, $uploadedFile, '/dir1/dir2', 'local', FileVisibility::Public);

        $this->assertInstanceOf(File::class, $file);
        $this->assertTrue(\Str::isUuid($file->id));
        $this->assertSame(User::class, $file->fileable_type);
        $this->assertSame($this->fileable->getKey(), $file->fileable_id);
        $this->assertSame('local', $file->disk);
        $this->assertSame("dir1/dir2/$file->name", $file->path);
        $this->assertSame($uploadedFile->getClientOriginalName(), $file->original_name);
        $this->assertSame($uploadedFile->getMimeType(), $file->mime_type);
        $this->assertSame($uploadedFile->getSize(), $file->size);
        $this->assertSame(FileVisibility::Public, $file->visibility);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_DefaultVisibility_Private(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');

        $file = $this->uploadFile->handle($this->fileable, $uploadedFile);

        $this->assertSame(FileVisibility::Private, $file->visibility);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_UploadedFileEventDispatched(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');

        $this->uploadFile->handle($this->fileable, $uploadedFile);

        \Event::assertDispatched(UploadedFileEvent::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_FileExists(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');

        $file = $this->uploadFile->handle($this->fileable, $uploadedFile);

        \Storage::assertExists("/$file->name");
        $this->assertInstanceOf(File::class, File::find($file->id));
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ShouldOverwrite_FileOverwritten(): void
    {
        $oldUploadedFile = UploadedFile::fake()->create('old_file');

        $oldFile = $this->uploadFile->handle($this->fileable, $oldUploadedFile);

        $newUploadedFile = UploadedFile::fake()->create('new_file');
        $newFile = $this->uploadFile->handle($this->fileable, $newUploadedFile, overwriteFileId: $oldFile->id);

        $this->assertSame($oldFile->id, $newFile->id);
        $this->assertSame('new_file', $newFile->original_name);
        $this->assertDatabaseCount(File::class, 1);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ShouldOverwrite_OverwrittenFileEventDispatched(): void
    {
        $oldFile = File::factory()->for(User::factory(), 'fileable')->create();
        $newUploadedFile = UploadedFile::fake()->create('new_file');
        $this->uploadFile->handle($oldFile->fileable, $newUploadedFile, overwriteFileId: $oldFile->id);

        \Event::assertDispatched(OverwrittenFile::class, function (OverwrittenFile $event) use ($oldFile) {
            $this->assertInstanceOf(File::class, $event->file);
            $this->assertSame($oldFile->id, $event->file->id);
            $this->assertSame($oldFile->disk, $event->file->disk);
            $this->assertSame($oldFile->path, $event->file->path);
            $this->assertSame($oldFile->original_name, $event->file->original_name);
            $this->assertSame($oldFile->size, $event->file->size);
            $this->assertSame($oldFile->mime_type, $event->file->mime_type);
            $this->assertSame($oldFile->visibility, $event->file->visibility);
            $this->assertSame($oldFile->fileable_type, $event->file->fileable_type);
            $this->assertSame($oldFile->fileable_id, $event->file->fileable_id);

            return true;
        });
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_OverrideButFileableIsInvalid_FileNotExistsCodeReturned(): void
    {
        $oldFile = File::factory()->for(User::factory(), 'fileable')->create();
        $newUploadedFile = UploadedFile::fake()->create('new_file');
        $error = $this->uploadFile->handle($this->fileable, $newUploadedFile, overwriteFileId: $oldFile->id);

        $this->assertSame(FileErrorCode::FileNotExists, $error);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_NoOverwrite_FileHasNotBeenOverwritten(): void
    {
        $oldUploadedFile = UploadedFile::fake()->create('old_file');

        $oldFile = $this->uploadFile->handle($this->fileable, $oldUploadedFile);

        $newUploadedFile = UploadedFile::fake()->create('new_file');
        $newFile = $this->uploadFile->handle($this->fileable, $newUploadedFile);

        $this->assertNotSame($oldFile->id, $newFile->id);
        $this->assertDatabaseHas(File::class, $oldFile->toArray());
        $this->assertDatabaseHas(File::class, $newFile->toArray());
        $this->assertDatabaseCount(File::class, 2);
    }
}

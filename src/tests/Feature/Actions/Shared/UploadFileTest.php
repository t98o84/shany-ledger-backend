<?php

namespace Tests\Feature\Actions\Shared;

use App\Actions\Shared\UploadFile;
use \App\Events\Shared\UploadedFile as UploadedFileEvent;
use App\Models\Shared\File;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadFileTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private UploadFile $uploadFile;

    protected function setUp(): void
    {
        parent::setUp();

        \Storage::fake();
        \Event::fake();
        $this->uploadFile = new UploadFile();
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_FileReturned(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');

        $file = $this->uploadFile->handle($uploadedFile);

        $this->assertInstanceOf(File::class, $file);
        $this->assertTrue(\Str::isUuid($file->id));
        $this->assertIsString($file->name);
        $this->assertSame($uploadedFile->getClientOriginalName(), $file->original_name);
        $this->assertSame($uploadedFile->getMimeType(), $file->mime_type);
        $this->assertSame($uploadedFile->getSize(), $file->size);
    }

    /**
 * @throws \Throwable
 */
    public function testHandle_ValidData_UploadedFileEventDispatched(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');

        $this->uploadFile->handle($uploadedFile);

        \Event::assertDispatched(UploadedFileEvent::class);
    }

    /**
     * @throws \Throwable
     */
    public function testHandle_ValidData_FileExists(): void
    {
        $uploadedFile = UploadedFile::fake()->create('test');

        $file = $this->uploadFile->handle($uploadedFile);

        \Storage::assertExists("/$file->name");
        $this->assertInstanceOf(File::class, File::find($file->id));
    }
}

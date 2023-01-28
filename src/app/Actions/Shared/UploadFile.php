<?php

namespace App\Actions\Shared;

use App\Models\Shared\File;
use App\Events\Shared\UploadedFile as UploadedFileEvent;
use Illuminate\Http\UploadedFile;

class UploadFile
{
    /**
     * @throws \Throwable
     */
    public function handle(UploadedFile $file, string $path = '/', string $disk = null): File|FileErrorCode
    {
        $storageDisk = $disk ?? config('filesystems.default');

        if (!is_string($storageDisk)) {
            throw new \LogicException('デフォルトディスクの取得に失敗しました。');
        }

        $storagePath = \Storage::disk($storageDisk)->putFile($path, $file);

        if ($storagePath === false) {
            return FileErrorCode::FailedToUploadFileToStorage;
        }

        if (($size = $file->getSize()) === false) {
            return FileErrorCode::FailedToGetFileSize;
        }

        $name = basename($storagePath);

        try {
            $file = File::create([
                'id' => (string) \Str::orderedUuid(),
                'name' => $name,
                'original_name' => $file->getClientOriginalName(),
                'size' => $size ?? 0,
                'mime_type' => $file->getMimeType() ?? 'text/plain',
            ]);

            UploadedFileEvent::dispatch($file, $path, $storageDisk);

            return $file;
        } catch (\Throwable $throwable) {
           \Storage::disk($storageDisk)->delete($storagePath);
           throw $throwable;
        }
    }
}

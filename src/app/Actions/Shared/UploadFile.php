<?php

namespace App\Actions\Shared;

use App\Events\Shared\OverwrittenFile;
use App\Models\Shared\File;
use App\Events\Shared\UploadedFile as UploadedFileEvent;
use App\Models\Shared\FileVisibility;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class UploadFile
{
    /**
     * TODO: EXIFの削除処理追加
     * @throws \Throwable
     */
    public function handle(Model $fileable, UploadedFile $uploadedFile, string $path = '/', string $disk = null, FileVisibility $visibility = FileVisibility::Private, ?string $overwriteFileId = null, array $options = []): File|FileErrorCode
    {
        $storageDisk = $disk ?? config('filesystems.default');

        if (!is_string($storageDisk)) {
            throw new \LogicException('デフォルトディスクの取得に失敗しました。');
        }

        if (($size = $uploadedFile->getSize()) === false) {
            return FileErrorCode::FailedToGetFileSize;
        }

        $file = $this->findOrMakeModel($fileable, $overwriteFileId);

        if ($file instanceof FileErrorCode) {
            return $file;
        }

        $storagePath = \Storage::disk($storageDisk)->putFile($path, $uploadedFile, [...$options, 'visibility' => $visibility->value]);

        if ($storagePath === false) {
            return FileErrorCode::FailedToUploadFileToStorage;
        }

        $overwrittenFile = $this->shouldOverwrite($overwriteFileId) ? clone $file : null;

        $file->fill([
            'disk' => $storageDisk,
            'path' => $storagePath,
            'original_name' => $uploadedFile->getClientOriginalName(),
            'size' => $size ?? 0,
            'mime_type' => $uploadedFile->getMimeType() ?? 'text/plain',
            'visibility' => $visibility->value,
        ]);

        try {
            $file->save();
        } catch (\Throwable $throwable) {
            \Storage::disk($storageDisk)->delete($storagePath);
            throw $throwable;
        }

        UploadedFileEvent::dispatch($file, $path, $storageDisk);

        if ($overwrittenFile instanceof File) {
            OverwrittenFile::dispatch($overwrittenFile);
        }

        return $file;
    }

    private function findOrMakeModel(Model $fileable, ?string $overwriteFileId): File|FileErrorCode
    {

        $file = $this->shouldOverwrite($overwriteFileId)
            ? File::query()->where('fileable_type', $fileable::class)->where('fileable_id', $fileable->getKey())->find($overwriteFileId)
            : new File([
                'id' => (string)\Str::orderedUuid(),
                'fileable_type' => $fileable::class,
                'fileable_id' => $fileable->getKey(),
            ]);

        return $file ?? FileErrorCode::FileNotExists;
    }

    private function shouldOverwrite(?string $overwriteFileId): bool
    {
        return is_string($overwriteFileId);
    }
}

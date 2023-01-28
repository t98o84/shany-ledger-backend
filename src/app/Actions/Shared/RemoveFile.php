<?php

namespace App\Actions\Shared;

use App\Models\Shared\File;
use App\Events\Shared\RemovedFile;

class RemoveFile
{
    public function handle(string $id, string $path = '/', string $disk = null, bool $withDirectory = false): true|FileErrorCode
    {
        $file = File::find($id);

        if (is_null($file)) {
            return FileErrorCode::FileNotExists;
        }

        $storageDisk = $disk ?? config('filesystems.default');

        if (!is_string($storageDisk)) {
            throw new \LogicException('デフォルトディスクの取得に失敗しました。');
        }

        $result = $withDirectory
            ? \Storage::disk($storageDisk)->deleteDirectory($path)
            : \Storage::disk($storageDisk)->delete("$path/$file->name");

        if (!$result) {
            return FileErrorCode::RemoveFileFailed;
        }

        $file->delete();

        RemovedFile::dispatch($file, $path, $storageDisk, $withDirectory);

        return true;
    }
}

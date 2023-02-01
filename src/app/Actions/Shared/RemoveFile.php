<?php

namespace App\Actions\Shared;

use App\Models\Shared\File;
use App\Events\Shared\RemovedFile;

class RemoveFile
{
    public function handle(string $id, bool $withDirectory = false): true|FileErrorCode
    {
        $file = File::find($id);

        if (is_null($file)) {
            return FileErrorCode::FileNotExists;
        }

        $result = $withDirectory
            ? \Storage::disk($file->disk)->deleteDirectory(dirname($file->path))
            : \Storage::disk($file->disk)->delete($file->path);

        if (!$result) {
            return FileErrorCode::RemoveFileFailed;
        }

        $file->delete();

        RemovedFile::dispatch($file, $file->path, $file->disk, $withDirectory);

        return true;
    }
}

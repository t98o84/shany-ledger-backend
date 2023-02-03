<?php

namespace App\Listeners\Shared;

use App\Events\Shared\OverwrittenFile;
use App\Jobs\ExponentialBackoff;
use App\Jobs\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RemoveFile implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = Queue::Files->value;

    public int $tries = 5;

    public function handle(OverwrittenFile $event): void
    {
        if (!\Storage::disk($event->file->disk)->exists($event->file->path)) {
            return;
        }

        if(!\Storage::disk($event->file->disk)->delete($event->file->path)) {
            throw new \RuntimeException('ファイルの削除に失敗しました。');
        }
    }

    public function failed(OverwrittenFile $event, \Throwable $exception): void
    {
        \Log::error($exception->getMessage(), [
            'file_id' => $event->file->id,
            'file_disk' => $event->file->disk,
            'file_path' => $event->file->path,
            'exception' => $exception
        ]);
    }

    public function backoff(): array
    {
        return ExponentialBackoff::generateDelayRetryList($this->tries);
    }
}

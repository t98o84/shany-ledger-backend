<?php

namespace App\Listeners\Auth;

use App\Events\Auth\PasswordReset;
use App\Jobs\ExponentialBackoff;
use App\Mail\Auth\PasswordResetNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPasswordResetNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 5;


    public function handle(PasswordReset $event): void
    {
        \Mail::send(new PasswordResetNotification($event->user));
    }

    public function failed(PasswordReset $event, \Throwable $exception): void
    {
        \Log::error($exception->getMessage(), [
            'user_id' => $event->user->id,
            'exception' => $exception
        ]);
    }

    public function backoff(): array
    {
        return ExponentialBackoff::generateDelayRetryList($this->tries);
    }
}

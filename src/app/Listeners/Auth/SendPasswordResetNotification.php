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

    public function __construct(private readonly PasswordReset $event)
    {
    }

    public function handle(): void
    {
        \Mail::send(PasswordResetNotification::class);
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error($exception);
    }

    public function backoff(): array
    {
        return ExponentialBackoff::generateDelayRetryList($this->tries);
    }
}

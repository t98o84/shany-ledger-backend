<?php

namespace App\Listeners\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Events\Auth\SignedUp;
use App\Events\Auth\UnverifiedEmail;
use App\Jobs\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use \App\Actions\Auth\SendEmailVerificationNotification as SendEmailVerificationNotificationAction;

class SendEmailVerificationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = Queue::Notifications->value;

    public int $tries = 3;

    public function __construct(private readonly SendEmailVerificationNotificationAction $sendEmailVerificationNotification)
    {
    }

    public function handle(SignedUp|UnverifiedEmail $event): void
    {
        $verificationUrl = $this->sendEmailVerificationNotification->handle($event->user->id);

        if (is_a($verificationUrl, AuthErrorCode::class)) {
            $this->delete();
            throw new \LogicException(__("error/auth/index.$verificationUrl->value") . ": {$event->user->id}");
        }
    }

    public function failed(SignedUp|UnverifiedEmail $event, $exception): void
    {
        // 失敗したとしてもユーザーが手動で再送信を行うようにするため
        // ここでは特に何もしない
    }
}

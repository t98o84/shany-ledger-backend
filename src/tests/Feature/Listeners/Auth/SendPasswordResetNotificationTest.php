<?php

namespace Tests\Feature\Listeners\Auth;

use App\Events\Auth\PasswordReset;
use App\Listeners\Auth\SendPasswordResetNotification;
use App\Models\User;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendPasswordResetNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \Queue::fake();
    }

    public function test_DispatchPasswordResetEvent_SendPasswordResetNotificationListenerQueued(): void
    {
       \Queue::assertNotPushed(CallQueuedListener::class, static fn($listener) => $listener->class === SendPasswordResetNotification::class);

        $user = User::factory()->create();
        PasswordReset::dispatch($user);

        \Queue::assertPushed(
            CallQueuedListener::class,
            static fn($listener) => $listener->class === SendPasswordResetNotification::class
        );
    }
}

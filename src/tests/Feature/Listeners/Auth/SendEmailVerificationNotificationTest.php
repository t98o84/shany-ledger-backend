<?php

namespace Tests\Feature\Listeners\Auth;

use App\Actions\Auth\AuthErrorCode;
use App\Events\Auth\SignedUp;
use App\Listeners\Auth\SendEmailVerificationNotification;
use App\Actions\Auth\SendEmailVerificationNotification as SendEmailVerificationNotificationAction;
use App\Models\User;
use Illuminate\Events\CallQueuedListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendEmailVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    private SendEmailVerificationNotificationAction $sendEmailVerificationNotificationAction;

    protected function setUp(): void
    {
        parent::setUp();

        \Mail::fake();
        \Queue::fake();
        $this->sendEmailVerificationNotificationAction = \Mockery::mock(SendEmailVerificationNotificationAction::class);
    }

    public function test_DispatchSignedUpEvent_SendEmailVerificationNotificationListenerQueued(): void
    {
       \Queue::assertNotPushed(CallQueuedListener::class, static fn($listener) => $listener->class === SendEmailVerificationNotification::class);

        $user = User::factory()->create();
        SignedUp::dispatch($user);

        \Queue::assertPushed(
            CallQueuedListener::class,
            static fn($listener) => $listener->class === SendEmailVerificationNotification::class
        );
    }

    public function testHandle_UserExists_HandleMethodCalled(): void
    {
        $user = User::factory()->create();
        $event = new SignedUp($user);

        $this->sendEmailVerificationNotificationAction->expects('handle')->once()->with($user->id);

        $listener = new SendEmailVerificationNotification($this->sendEmailVerificationNotificationAction);

        $listener->handle($event);
    }

    public function testHandle_UserNotExists_LogicExceptionThrown(): void
    {
        $user = User::factory()->unverified()->make();
        $event = new SignedUp($user);

        $this->sendEmailVerificationNotificationAction->expects('handle')->once()->with($user->id)->andReturn(AuthErrorCode::InvalidUserId);

        $listener = new SendEmailVerificationNotification($this->sendEmailVerificationNotificationAction);

        $this->expectException(\LogicException::class);
        $listener->handle($event);
    }
}

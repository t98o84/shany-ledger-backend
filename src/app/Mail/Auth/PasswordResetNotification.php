<?php

namespace App\Mail\Auth;

use App\Jobs\ExponentialBackoff;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 5;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->user->getEmailForPasswordReset(),
            subject: __('mail/auth/password-reset-notification.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.auth.password-reset-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function backoff(): array
    {
        return ExponentialBackoff::generateDelayRetryList($this->tries);
    }
}

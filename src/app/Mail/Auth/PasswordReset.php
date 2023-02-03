<?php

namespace App\Mail\Auth;

use App\Jobs\Queue;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordReset extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $resetUrl)
    {
        $this->onQueue(Queue::Notifications->value);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->user->getEmailForPasswordReset(),
            subject: __('mail/auth/password-reset.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.auth.password-reset',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

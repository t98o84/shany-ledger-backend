<?php

namespace App\Mail\Auth;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailVerification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $verificationUrl)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: $this->user->getEmailForVerification(),
            subject: __('mail/auth/email-verification.subject'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.auth.email-verification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}

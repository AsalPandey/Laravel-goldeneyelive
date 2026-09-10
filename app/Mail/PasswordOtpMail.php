<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class PasswordOtpMail extends Mailable
{
    public function __construct(public string $otp) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('goldeneye.security_email'), config('goldeneye.security_email_name')),
            subject: 'Your password verification code',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.password-otp');
    }
}

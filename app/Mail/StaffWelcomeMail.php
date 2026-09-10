<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StaffWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('goldeneye.security_email'), config('goldeneye.security_email_name')),
            subject: 'Your Golden Eye Academy CMS Account Is Ready',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.staff-welcome');
    }
}

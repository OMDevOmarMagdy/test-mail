<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'My First Laravel Email',
        );
    }

    public function content(): Content
    {
        return new Content(
            // htmlString: '
            //     <h1>Hello from Laravel!</h1>
            //     <p>This is my first email using SMTP + Queue.</p>
            // ',

            view: 'emails.test-mail',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
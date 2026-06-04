<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public \App\Models\Quote $quote) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Quote Request Received — #{$this->quote->quote_number} | pcfy.in",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.quote-received');
    }

    public function attachments(): array { return []; }
}

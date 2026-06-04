<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class QuoteResponseMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public \App\Models\Quote         $quote,
        public \App\Models\QuoteResponse $response,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->response->subject ?: "Re: Quote #{$this->quote->quote_number} | pcfy.in",
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.quote-response');
    }

    public function attachments(): array
    {
        if ($this->response->attachment_path && \Storage::exists($this->response->attachment_path)) {
            return [
                Attachment::fromStorageDisk('local', $this->response->attachment_path)
                    ->as($this->response->attachment_name)
                    ->withMime(\Storage::mimeType($this->response->attachment_path)),
            ];
        }
        return [];
    }
}

<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** Notifies the NGO inbox (Website Settings → Contact email) about a new website submission. */
class SubmissionReceived extends Mailable
{
    public function __construct(
        public string $heading,
        public array $fields,
        public ?string $adminUrl = null,
        public ?string $replyToEmail = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->heading.' — '.setting('site_name', config('app.name')),
            replyTo: $this->replyToEmail ? [$this->replyToEmail] : [],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.submission');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BankingUpdateRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $driverName;

    /**
     * Create a new message instance.
     * * @param array $details The validated banking data
     * @param string $driverName The name of the driver requesting the update
     */
    public function __construct($details, $driverName)
    {
       $this->details = $details;
        $this->driverName = $driverName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Banking Update Request: {$this->driverName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.banking-request',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

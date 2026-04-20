<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HSTGSTUpdateRequest extends Mailable
{
    use Queueable, SerializesModels;

    public $details;
    public $driverName;

    /**
     * @param array $details The validated HST/GST data
     * @param string $driverName The name of the driver
     */
    public function __construct($details, $driverName)
    {
        $this->details = $details;
        $this->driverName = $driverName;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "HST/GST Update Request: {$this->driverName}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hst-gst-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
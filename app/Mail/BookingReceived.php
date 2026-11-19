<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Sent to the traveller (confirmation of the request) and to the team (new booking). */
class BookingReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking, public bool $forAdmin = false) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->forAdmin
            ? "New booking {$this->booking->reference}: {$this->booking->package_name}"
            : "Your booking request {$this->booking->reference} | ".setting('site_name'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.booking-received');
    }
}

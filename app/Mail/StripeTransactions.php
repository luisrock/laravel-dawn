<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;


class StripeTransactions extends Mailable
{
    use Queueable, SerializesModels;

    public $stripe_data;
    public $subject;

    /**
     * Create a new message instance.
     */
    public function __construct($stripe_data, $subject = null)
    {
        $this->stripe_data = $stripe_data;
        $this->subject = $subject ?? __('Stripe Transactions');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.stripe-transactions',
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

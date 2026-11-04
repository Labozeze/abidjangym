<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Message du formulaire de contact, adressé à l'équipe (CDC §4.1.2).
 *
 * Protection contre l'injection d'en-têtes (CDC §6.4) : l'adresse email est validée en
 * amont, et le sujet comme le nom affiché sont purgés de tout retour à la ligne avant
 * d'être placés dans un en-tête.
 */
final class ContactMessageMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nouveau message depuis le site — '.$this->sanitize($this->contactMessage->subject),
            replyTo: [
                new Address($this->contactMessage->email, $this->sanitize($this->contactMessage->fullName())),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-message',
        );
    }

    /** Neutralise les retours à la ligne (injection d'en-tête). */
    private function sanitize(string $value): string
    {
        return trim((string) preg_replace('/[\r\n]+/', ' ', $value));
    }
}

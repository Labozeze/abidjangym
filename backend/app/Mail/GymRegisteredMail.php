<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Gym;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email de confirmation après l'inscription d'une salle (CDC §10.1).
 *
 * Envoyé en différé (`defer()`), jamais dans le cycle de la requête : l'inscription doit
 * répondre vite, et un SMTP lent ne doit pas faire échouer la création.
 */
final class GymRegisteredMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Gym $gym,
        public User $admin,
        public string $administrationUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre salle AbidjanGym est créée',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.gym-registered',
        );
    }
}

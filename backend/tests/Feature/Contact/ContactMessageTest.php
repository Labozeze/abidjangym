<?php

declare(strict_types=1);

namespace Tests\Feature\Contact;

use App\Mail\ContactMessageMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * `POST /contact-messages` — formulaire de contact public (CDC §4.1.2, §6.4).
 */
final class ContactMessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_stores_the_message_and_mails_the_team(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/contact-messages', $this->payload())
            ->assertCreated()
            ->assertJsonPath('message', 'Merci, votre message a bien été envoyé. Nous répondons sous 24 h.');

        $this->assertDatabaseHas('contact_messages', [
            'email' => 'visiteur@example.com',
            'subject' => 'Demande d’information sur les formules',
        ]);

        Mail::assertSent(ContactMessageMail::class, fn (ContactMessageMail $mail): bool => $mail->hasTo((string) config('contact.email')));
        Mail::assertSent(ContactMessageMail::class, fn (ContactMessageMail $mail): bool => $mail->hasReplyTo('visiteur@example.com'));
    }

    public function test_it_rejects_header_injection_in_the_subject(): void
    {
        Mail::fake();

        $this->postJson('/api/v1/contact-messages', $this->payload([
            'subject' => "Demande\r\nBcc: pirate@example.com",
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['subject']);

        $this->assertDatabaseCount('contact_messages', 0);
        Mail::assertNothingSent();
    }

    public function test_it_validates_all_fields(): void
    {
        $this->withoutMiddleware(ThrottleRequests::class);

        $this->postJson('/api/v1/contact-messages', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'subject', 'message']);

        $this->postJson('/api/v1/contact-messages', $this->payload(['email' => 'pas-un-email']))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_is_rate_limited(): void
    {
        Mail::fake();

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/contact-messages', $this->payload())->assertCreated();
        }

        $this->postJson('/api/v1/contact-messages', $this->payload())->assertStatus(429);
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return array_replace([
            'first_name' => 'Visiteur',
            'last_name' => 'Test',
            'email' => 'visiteur@example.com',
            'phone' => '+2250700000009',
            'subject' => 'Demande d’information sur les formules',
            'message' => 'Bonjour, je gère une salle à Yopougon et je souhaite en savoir plus.',
        ], $overrides);
    }
}

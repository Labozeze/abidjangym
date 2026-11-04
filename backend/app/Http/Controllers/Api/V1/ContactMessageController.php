<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Mail\ContactMessageMail;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * `POST /contact-messages` — formulaire de contact public (CDC §4.1.2).
 *
 * Le message est enregistré immédiatement, puis l'email part en différé (`defer()`) :
 * un SMTP lent ou indisponible ne doit pas faire perdre un message ni retarder la réponse.
 */
final class ContactMessageController extends Controller
{
    public function __invoke(StoreContactMessageRequest $request): JsonResponse
    {
        /** @var array<string, mixed> $data */
        $data = $request->validated();

        $contactMessage = ContactMessage::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'ip_address' => $request->ip(),
        ]);

        $recipient = (string) config('contact.email');

        defer(function () use ($contactMessage, $recipient): void {
            try {
                Mail::to($recipient)->send(new ContactMessageMail($contactMessage));
            } catch (Throwable $exception) {
                // Le message reste en base : il pourra être renvoyé depuis le backoffice.
                report($exception);
            }
        });

        return response()->json([
            'message' => 'Merci, votre message a bien été envoyé. Nous répondons sous 24 h.',
        ], 201);
    }
}

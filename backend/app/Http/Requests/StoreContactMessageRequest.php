<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Formulaire de contact public (contrat `POST /contact-messages`, CDC §4.1.2).
 */
final class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:60'],
            'last_name' => ['required', 'string', 'max:60'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            // Le sujet finit dans un en-tête d'email : aucun retour à la ligne autorisé
            // (protection contre l'injection d'en-têtes, CDC §6.4).
            'subject' => ['required', 'string', 'max:150', 'not_regex:/[\r\n]/'],
            'message' => ['required', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required' => 'Le nom est obligatoire.',
            'email.required' => 'L’adresse email est obligatoire.',
            'email.email' => 'L’adresse email n’est pas valide.',
            'phone.regex' => 'Le téléphone doit être au format international, par exemple +2250700000001.',
            'subject.required' => 'L’objet du message est obligatoire.',
            'subject.not_regex' => 'L’objet du message ne doit pas contenir de retour à la ligne.',
            'message.required' => 'Le message est obligatoire.',
            'message.max' => 'Le message ne doit pas dépasser 5000 caractères.',
        ];
    }
}

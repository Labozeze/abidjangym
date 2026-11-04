<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Inscription d'une salle (contrat `POST /gyms`, CDC §4.1.4 et §10.1).
 */
final class StoreGymRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'gym' => ['required', 'array'],
            'gym.name' => ['required', 'string', 'max:80'],
            'gym.district' => ['required', 'string', 'max:80'],
            'gym.address' => ['required', 'string', 'max:255'],
            'gym.phone' => ['required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'gym.whatsapp' => ['required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],

            'admin' => ['required', 'array'],
            'admin.first_name' => ['required', 'string', 'max:60'],
            'admin.last_name' => ['required', 'string', 'max:60'],
            // Email unique sur toute la plateforme (décision du 01/11/2026) : la
            // connexion se fait par email seul.
            'admin.email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'admin.mobile_money_number' => ['required', 'string', 'regex:/^\+[1-9][0-9]{7,14}$/'],
            'admin.password' => ['required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[0-9]/'],

            'plan_code' => ['required', 'string', 'in:starter,essentiel,pro'],

            'terms_accepted' => ['required', 'accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'gym.name.required' => 'Le nom de la salle est obligatoire.',
            'gym.district.required' => 'La commune ou le quartier est obligatoire.',
            'gym.address.required' => 'L’adresse de la salle est obligatoire.',
            'gym.phone.required' => 'Le téléphone de la salle est obligatoire.',
            'gym.phone.regex' => 'Le téléphone doit être au format international, par exemple +2250700000001.',
            'gym.whatsapp.required' => 'Le numéro WhatsApp est obligatoire.',
            'gym.whatsapp.regex' => 'Le numéro WhatsApp doit être au format international.',
            'admin.first_name.required' => 'Le prénom du gérant est obligatoire.',
            'admin.last_name.required' => 'Le nom du gérant est obligatoire.',
            'admin.email.required' => 'L’adresse email est obligatoire.',
            'admin.email.email' => 'L’adresse email n’est pas valide.',
            'admin.email.unique' => 'Cette adresse email est déjà utilisée.',
            'admin.mobile_money_number.required' => 'Le numéro Mobile Money est obligatoire.',
            'admin.mobile_money_number.regex' => 'Le numéro Mobile Money doit être au format international.',
            'admin.password.required' => 'Le mot de passe est obligatoire.',
            'admin.password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'admin.password.regex' => 'Le mot de passe doit contenir au moins une majuscule et un chiffre.',
            'plan_code.required' => 'La formule choisie est obligatoire.',
            'plan_code.in' => 'La formule choisie est invalide.',
            'terms_accepted.required' => 'Vous devez accepter les conditions générales.',
            'terms_accepted.accepted' => 'Vous devez accepter les conditions générales.',
        ];
    }
}

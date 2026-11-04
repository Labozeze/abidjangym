Nouveau message depuis le formulaire de contact du site.

Nom      : {{ $contactMessage->fullName() }}
Email    : {{ $contactMessage->email }}
Téléphone: {{ $contactMessage->phone ?? 'non renseigné' }}
Sujet    : {{ $contactMessage->subject }}

Message :
{{ $contactMessage->message }}

--
Répondre à ce message écrit directement à {{ $contactMessage->email }}.

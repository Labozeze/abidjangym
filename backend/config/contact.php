<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Adresse de contact de l'équipe AbidjanGym
    |--------------------------------------------------------------------------
    |
    | Destinataire des messages du formulaire public (CDC §4.1.2).
    |
    | Adresse officielle confirmée par Rufin le 02/11/2026. Elle se surcharge dans le
    | `.env` du serveur (`CONTACT_EMAIL`) ; la valeur ci-dessous n'est qu'un repli.
    |
    */

    'email' => env('CONTACT_EMAIL', 'contact@abidjangym.com'),

];

<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Backoffice — liens sortants
    |--------------------------------------------------------------------------
    |
    | Les emails transactionnels (réinitialisation du mot de passe) doivent pointer
    | vers une page du backoffice, jamais vers une route de l'API : il n'existe pas
    | de route web `password.reset` côté backend.
    |
    | `reset_path` est marqué [À FOURNIR] : l'écran de réinitialisation du backoffice
    | n'existe pas encore (Entité A). La valeur ci-dessous est un emplacement
    | provisoire, à confirmer quand l'écran sera livré.
    |
    */

    'url' => env('BACKOFFICE_URL', 'http://localhost:3000'),

    'reset_path' => env('BACKOFFICE_RESET_PATH', '/reset-password'),

    /*
    | Tableau de bord du gérant, envoyé dans l'email de confirmation d'inscription
    | (CDC §10.1). Chemin interne au backoffice, lui aussi à confirmer par l'Entité A.
    */
    'dashboard_path' => env('BACKOFFICE_DASHBOARD_PATH', '/dashboard'),

];

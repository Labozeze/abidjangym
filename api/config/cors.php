<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Chemins soumis au CORS
    |--------------------------------------------------------------------------
    |
    | Seule l'API est consommée en cross-origin : le backoffice (app.) et
    | l'espace abonné (member.) sont des sous-domaines distincts (CDC §3.3).
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    /*
    | Les origines sont fournies par variable d'environnement, jamais en dur :
    | chaque environnement (local, staging, production) a ses sous-domaines.
    */
    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CORS_ALLOWED_ORIGINS', ''))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CORS_ALLOWED_HEADERS', 'Authorization,Content-Type'))
    ))),

    'exposed_headers' => [],

    'max_age' => (int) env('CORS_MAX_AGE', 86400),

    /*
    | Requis pour l'authentification par cookie httpOnly du backoffice (CDC §6.2).
    */
    'supports_credentials' => true,

];

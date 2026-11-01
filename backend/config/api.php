<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Version de l'API
    |--------------------------------------------------------------------------
    |
    | Exposée par GET /api/v1/health et destinée au monitoring. Elle suit la
    | version mineure du contrat docs/api/openapi.yaml.
    |
    */

    'version' => env('API_VERSION', '0.1.0'),

];

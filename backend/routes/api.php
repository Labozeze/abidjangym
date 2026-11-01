<?php

use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes de l'API (préfixe /api, version /v1)
|--------------------------------------------------------------------------
|
| Conventions du CDC §7.2 : JSON, préfixe versionné /api/v1/.
| Toute nouvelle route métier doit être protégée et limitée au tenant courant
| (CDC §6.3) ; seules les routes listées comme publiques dans
| docs/api/openapi.yaml peuvent rester ouvertes.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    // Supervision : public, ne dépend d'aucune session (backend/AGENTS.md §7).
    Route::get('health', HealthController::class)->name('health');
});

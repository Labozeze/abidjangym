<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\MeController;
use App\Http\Controllers\Api\V1\Auth\RefreshController;
use App\Http\Controllers\Api\V1\Auth\ResetPasswordController;
use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes de l'API (préfixe /api, version /v1)
|--------------------------------------------------------------------------
|
| Conventions du CDC §7.2 : JSON, préfixe versionné /api/v1/.
| Toute route métier doit être protégée et limitée au tenant courant (CDC §6.3) ;
| seules les routes listées comme publiques dans docs/api/openapi.yaml le restent.
|
*/

Route::prefix('v1')->name('api.v1.')->group(function (): void {
    // Supervision : public, ne dépend d'aucune session (backend/AGENTS.md §7).
    Route::get('health', HealthController::class)->name('health');

    /*
     * Routes publiques d'authentification.
     *
     * 5 tentatives par minute et par IP sur les surfaces de devinette (CDC §6.2, §6.4).
     * Le rafraîchissement est limité plus largement : ce n'est pas une surface de devinette,
     * et un bureau partageant une IP publique ne doit pas se bloquer lui-même.
     */
    Route::prefix('auth')->name('auth.')->group(function (): void {
        Route::post('login', LoginController::class)
            ->middleware('throttle:5,1')
            ->name('login');

        Route::post('refresh', RefreshController::class)
            ->middleware('throttle:30,1')
            ->name('refresh');

        Route::post('forgot-password', ForgotPasswordController::class)
            ->middleware('throttle:5,1')
            ->name('password.email');

        Route::post('reset-password', ResetPasswordController::class)
            ->middleware('throttle:5,1')
            ->name('password.store');
    });

    // Routes authentifiées : le middleware `tenant` remplit le contexte multi-tenant.
    Route::middleware(['auth:sanctum', 'tenant'])->prefix('auth')->name('auth.')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('me', MeController::class)->name('me');
    });
});

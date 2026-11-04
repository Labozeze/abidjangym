<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Endpoint de supervision (backend/AGENTS.md §7, docs/api/openapi.yaml → /health).
 *
 * Ne dépend d'aucune session ni d'aucun tenant : il doit répondre même si la base
 * est indisponible, avec le code 503 dans ce cas.
 */
final class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $database = $this->databaseStatus();

        return response()
            ->json([
                'status' => $database === 'ok' ? 'ok' : 'degraded',
                'version' => (string) config('api.version'),
                'time' => now()->toIso8601String(),
                'database' => $database,
            ], $database === 'ok' ? 200 : 503)
            ->header('Cache-Control', 'no-store');
    }

    private function databaseStatus(): string
    {
        try {
            DB::connection()->getPdo();
        } catch (Throwable) {
            return 'unavailable';
        }

        return 'ok';
    }
}

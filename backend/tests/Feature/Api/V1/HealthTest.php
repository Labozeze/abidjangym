<?php

declare(strict_types=1);

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

/**
 * L'endpoint de santé est public et ne dépend d'aucun tenant ni d'aucune session
 * (docs/api/openapi.yaml → GET /health).
 */
final class HealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('status', 'ok')
            ->assertJsonPath('database', 'ok')
            ->assertJsonStructure(['status', 'version', 'time', 'database']);
    }

    public function test_health_endpoint_exposes_api_version(): void
    {
        $this->getJson('/api/v1/health')
            ->assertOk()
            ->assertJsonPath('version', config('api.version'));
    }

    public function test_health_endpoint_is_not_cacheable(): void
    {
        $cacheControl = (string) $this->getJson('/api/v1/health')->headers->get('Cache-Control');

        // Symfony complète l'en-tête en « no-store, private » : on vérifie l'intention
        // (interdiction de mise en cache), pas la chaîne exacte.
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringNotContainsString('public', $cacheControl);
    }
}

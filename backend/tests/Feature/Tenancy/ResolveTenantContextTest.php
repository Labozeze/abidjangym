<?php

declare(strict_types=1);

namespace Tests\Feature\Tenancy;

use App\Http\Middleware\ResolveTenantContext;
use App\Models\Gym;
use App\Models\User;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Le middleware prépare le contexte multi-tenant pour le ticket d'authentification :
 * il le déduit de l'utilisateur authentifié et le vide toujours en sortie (CDC §6.3).
 */
final class ResolveTenantContextTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sets_the_context_from_the_authenticated_user(): void
    {
        $gym = Gym::factory()->create(['name' => 'FitZone Test']);
        $user = app(TenantContext::class)->run($gym->id, fn () => User::factory()->create());

        $request = Request::create('/api/v1/probe');
        $request->setUserResolver(fn (): User => $user);

        $contextDuringRequest = null;
        $usersVisibleDuringRequest = null;

        (new ResolveTenantContext)->handle($request, function () use (&$contextDuringRequest, &$usersVisibleDuringRequest) {
            $contextDuringRequest = app(TenantContext::class)->id();
            $usersVisibleDuringRequest = User::query()->count();

            return response()->noContent();
        });

        $this->assertSame($gym->id, $contextDuringRequest);
        $this->assertSame(1, $usersVisibleDuringRequest, 'La requête doit voir les utilisateurs de la salle.');
        $this->assertNull(
            app(TenantContext::class)->id(),
            'Le contexte doit être vidé après la requête.'
        );
    }

    public function test_it_forgets_any_previous_context_when_anonymous(): void
    {
        $gym = Gym::factory()->create(['name' => 'FitZone Test']);
        app(TenantContext::class)->set($gym->id);

        $request = Request::create('/api/v1/probe');
        $request->setUserResolver(fn () => null);

        $contextDuringRequest = 'non-initialisé';

        (new ResolveTenantContext)->handle($request, function () use (&$contextDuringRequest) {
            $contextDuringRequest = app(TenantContext::class)->id();

            return response()->noContent();
        });

        $this->assertNull($contextDuringRequest, 'Sans authentification, le contexte doit être vide.');
        $this->assertNull(app(TenantContext::class)->id());
    }
}

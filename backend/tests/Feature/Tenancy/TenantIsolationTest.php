<?php

declare(strict_types=1);

namespace Tests\Feature\Tenancy;

use App\Exceptions\Tenancy\MissingTenantContextException;
use App\Exceptions\Tenancy\TenantMismatchException;
use App\Models\ExampleRecord;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Prouve l'isolation multi-tenant exigée par le CDC §6.3 et backend/AGENTS.md §1 :
 * une salle ne voit ni ne modifie les données d'une autre.
 */
final class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    private TenantContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = app(TenantContext::class);
    }

    protected function tearDown(): void
    {
        $this->context->forget();

        parent::tearDown();
    }

    public function test_records_are_scoped_to_the_current_tenant(): void
    {
        $recordA = $this->context->run(1, fn () => ExampleRecord::create(['name' => 'Salle A']));
        $recordB = $this->context->run(2, fn () => ExampleRecord::create(['name' => 'Salle B']));

        $this->assertSame(1, $recordA->tenant_id);
        $this->assertSame(2, $recordB->tenant_id);

        $this->context->run(1, function () use ($recordB): void {
            $this->assertSame(['Salle A'], ExampleRecord::query()->pluck('name')->all());
            $this->assertNull(
                ExampleRecord::find($recordB->id),
                'La salle 1 ne doit pas voir l’enregistrement de la salle 2.'
            );
        });

        $this->context->run(2, function () use ($recordA): void {
            $this->assertSame(['Salle B'], ExampleRecord::query()->pluck('name')->all());
            $this->assertNull(ExampleRecord::find($recordA->id));
        });
    }

    public function test_tenant_id_is_filled_automatically_on_creation(): void
    {
        $record = $this->context->run(7, fn () => ExampleRecord::create(['name' => 'Salle 7']));

        $this->assertSame(7, $record->tenant_id);
        $this->assertSame(7, $record->tenantId());
    }

    public function test_without_tenant_context_no_row_is_visible(): void
    {
        $this->context->run(1, fn () => ExampleRecord::create(['name' => 'Salle A']));

        $this->assertSame(
            0,
            ExampleRecord::query()->count(),
            'Sans salle courante, le scope doit renvoyer zéro ligne (fail closed).'
        );
    }

    public function test_creating_without_tenant_context_is_refused(): void
    {
        $this->expectException(MissingTenantContextException::class);

        ExampleRecord::create(['name' => 'orphelin']);
    }

    public function test_writing_into_another_tenant_is_refused(): void
    {
        $this->expectException(TenantMismatchException::class);

        $this->context->run(1, fn () => ExampleRecord::create([
            'tenant_id' => 2,
            'name' => 'intrus',
        ]));
    }

    public function test_maintenance_can_bypass_the_scope_explicitly(): void
    {
        $this->context->run(1, fn () => ExampleRecord::create(['name' => 'Salle A']));
        $this->context->run(2, fn () => ExampleRecord::create(['name' => 'Salle B']));

        $this->assertSame(2, ExampleRecord::withoutTenantScope()->count());
    }

    public function test_for_tenant_scope_targets_a_single_tenant_explicitly(): void
    {
        $this->context->run(1, fn () => ExampleRecord::create(['name' => 'Salle A']));
        $this->context->run(2, fn () => ExampleRecord::create(['name' => 'Salle B']));

        $this->assertSame(1, ExampleRecord::query()->forTenant(2)->count());
    }

    public function test_context_is_restored_after_run(): void
    {
        $this->context->run(1, function (): void {
            $this->context->run(2, fn () => ExampleRecord::create(['name' => 'Salle B']));

            $this->assertSame(1, $this->context->id(), 'Le contexte précédent doit être restauré.');
        });
    }
}

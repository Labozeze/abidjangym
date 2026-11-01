<?php

declare(strict_types=1);

namespace Tests\Feature\Payments;

use App\Enums\PaymentMode;
use App\Models\Gym;
use App\Models\PaymentMethodSetting;
use App\Support\Tenancy\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Les identifiants des prestataires ne doivent jamais être stockés en clair ni sérialisés
 * (CDC §6.6, contrat v0.3.0 : champ `credentials` en écriture seule).
 */
final class PaymentMethodCredentialsTest extends TestCase
{
    use RefreshDatabase;

    private Gym $gym;

    private TenantContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gym = Gym::factory()->create(['name' => 'FitZone Test']);
        $this->context = app(TenantContext::class);
    }

    protected function tearDown(): void
    {
        $this->context->forget();

        parent::tearDown();
    }

    public function test_credentials_are_encrypted_at_rest(): void
    {
        $setting = $this->createOrangeMoneySetting();

        $raw = DB::table('payment_method_settings')
            ->where('id', $setting->id)
            ->value('credentials');

        $this->assertIsString($raw, 'La colonne doit contenir une chaîne chiffrée.');
        $this->assertStringNotContainsString('cle-fictive-de-test', $raw);
        $this->assertStringNotContainsString('secret-fictif-de-test', $raw);

        // Relecture dans la salle : hors contexte, le scope refuse par défaut (aucune ligne).
        $reloaded = $this->context->run(
            $this->gym->id,
            fn () => PaymentMethodSetting::query()->findOrFail($setting->id)
        );

        $this->assertSame(
            'cle-fictive-de-test',
            $reloaded->credentials['api_key'],
            'Le modèle doit déchiffrer à la lecture.'
        );
    }

    public function test_credentials_are_never_serialized(): void
    {
        $setting = $this->createOrangeMoneySetting();

        $this->assertArrayNotHasKey('credentials', $setting->toArray());
        $this->assertStringNotContainsString('cle-fictive-de-test', $setting->toJson());
        $this->assertStringNotContainsString('credentials', $setting->toJson());
    }

    public function test_is_configured_reflects_required_fields_per_method(): void
    {
        $cash = $this->makeSetting(PaymentMode::Cash);
        $this->assertTrue($cash->isConfigured(), 'Les espèces n’exigent aucune configuration.');

        $orange = $this->makeSetting(PaymentMode::OrangeMoney);
        $this->assertFalse($orange->isConfigured());
        $orange->update(['collection_number' => '+2250700000001']);
        $this->assertTrue($orange->fresh()->isConfigured());

        $cheque = $this->makeSetting(PaymentMode::Cheque, ['account_holder' => 'FitZone Test']);
        $this->assertFalse($cheque->isConfigured(), 'La banque manque encore.');
        $cheque->update(['bank_name' => 'Banque Fictive']);
        $this->assertTrue($cheque->fresh()->isConfigured());
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeSetting(PaymentMode $mode, array $attributes = []): PaymentMethodSetting
    {
        return $this->context->run($this->gym->id, fn () => PaymentMethodSetting::create([
            'method' => $mode,
            'enabled' => false,
            ...$attributes,
        ]));
    }

    private function createOrangeMoneySetting(): PaymentMethodSetting
    {
        return $this->context->run($this->gym->id, fn () => PaymentMethodSetting::create([
            'method' => PaymentMode::OrangeMoney,
            'enabled' => true,
            'collection_number' => '+2250700000001',
            'credentials' => [
                'api_key' => 'cle-fictive-de-test',
                'api_secret' => 'secret-fictif-de-test',
            ],
        ]));
    }
}

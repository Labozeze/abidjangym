<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * La salle de sport est le tenant : sa clé primaire est le `tenant_id` de toutes les
 * autres tables métier (CDC §6.3). Champs alignés sur le schéma `Gym` du contrat
 * (`docs/api/openapi.yaml`, CDC §4.2.3).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gyms', function (Blueprint $table): void {
            $table->id();

            // Identité (CDC §4.2.3)
            $table->string('name', 80);
            $table->string('logo_url')->nullable();
            $table->string('tagline', 120)->nullable();

            // Coordonnées
            $table->string('address');
            $table->string('district', 80);
            $table->string('city', 80)->default('Abidjan');
            $table->string('phone', 20);
            $table->string('whatsapp', 20);
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // Personnalisation
            $table->json('opening_hours')->nullable();
            $table->string('primary_color', 7)->default('#0F766E');
            $table->string('secondary_color', 7)->default('#F59E0B');
            $table->json('social_links')->nullable();
            $table->string('timezone', 64)->default('Africa/Abidjan');
            $table->string('currency', 3)->default('XOF');
            $table->string('welcome_message', 500)->nullable();

            // Reçu (CDC §4.2.8), configuré par le gérant
            $table->string('receipt_footer_message', 255)->nullable();
            $table->boolean('receipt_include_qr')->default(false);
            $table->string('receipt_signature_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gyms');
    }
};

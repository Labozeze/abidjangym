<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Réglages des moyens de paiement d'une salle (CDC §4.2.6, activation on/off).
 *
 * `credentials` stocke les identifiants du prestataire **chiffrés** (cast `encrypted`
 * du modèle, AES-256 via APP_KEY, CDC §6.6) : la colonne est en texte, jamais lisible
 * directement, et le champ est exclu de toute sérialisation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_method_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tenant_id')->constrained('gyms')->cascadeOnDelete();
            $table->string('method', 30);
            $table->boolean('enabled')->default(false);

            // Configuration non sensible, exposée par l'API
            $table->string('collection_number', 30)->nullable();
            $table->string('account_holder', 120)->nullable();
            $table->string('bank_name', 120)->nullable();
            $table->string('instructions', 500)->nullable();

            // Identifiants secrets du prestataire : chiffrés, jamais sérialisés
            $table->text('credentials')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_method_settings');
    }
};

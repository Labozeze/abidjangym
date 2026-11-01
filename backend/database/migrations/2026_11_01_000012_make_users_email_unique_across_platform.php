<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Décision de Rufin du 01/11/2026 : l'email d'un compte est **unique sur toute la
 * plateforme**, et non plus par salle.
 *
 * Motif : la connexion se fait par email + mot de passe (CDC §4.2.1), sans choisir sa
 * salle ; deux comptes portant le même email rendaient la connexion et la réinitialisation
 * ambiguës.
 *
 * La migration du ticket #39 n'est pas modifiée (le staging est déjà migré) : celle-ci
 * remplace l'index unique `(tenant_id, email)` par un index unique `email`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['tenant_id', 'email']);
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['email']);
            $table->unique(['tenant_id', 'email']);
        });
    }
};

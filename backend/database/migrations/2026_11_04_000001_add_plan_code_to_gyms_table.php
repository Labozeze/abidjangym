<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Formule SaaS choisie à l'inscription (CDC §4.1.4, étape 3).
 *
 * Le contrat (`POST /gyms`) reçoit `plan_code` mais le schéma `Gym` ne l'expose pas encore :
 * la colonne reste interne tant que le back-office SaaS n'est pas spécifié.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gyms', function (Blueprint $table): void {
            $table->string('plan_code', 20)->default('starter')->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('gyms', function (Blueprint $table): void {
            $table->dropColumn('plan_code');
        });
    }
};

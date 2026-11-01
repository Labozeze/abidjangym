<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Table d'exemple du socle multi-tenant.
 *
 * `tenant_id` désignera à terme la table `gyms`, créée par un ticket ultérieur : la
 * contrainte de clé étrangère sera ajoutée à ce moment-là, pas avant.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('example_records', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tenant_id')->index();
            $table->string('name', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('example_records');
    }
};

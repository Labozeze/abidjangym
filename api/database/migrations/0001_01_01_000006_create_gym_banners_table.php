<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bannières de la salle : trois maximum, affichées dans l'espace abonné (CDC §4.2.3).
 * Table séparée pour conserver l'`id` prévu par le schéma `GymBanner` du contrat.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gym_banners', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('gym_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['gym_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gym_banners');
    }
};

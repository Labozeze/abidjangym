<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Index sur `users.tenant_id`, requis par la clé étrangère vers `gyms`.
 *
 * ⚠️ Pourquoi cette migration existe : MySQL impose un index sur toute colonne de clé
 * étrangère. La clé `users.tenant_id` s'appuyait sur l'index composite
 * `(tenant_id, email)` créé par la migration de `users`. La migration suivante
 * (`2026_11_01_000012_…`) supprime cet index composite pour rendre l'email unique sur toute
 * la plateforme, ce qui échouait sur MySQL :
 *
 *     SQLSTATE[HY000]: General error: 1553 Cannot drop index 'users_tenant_id_email_unique':
 *     needed in a foreign key constraint
 *
 * SQLite ne connaît pas cette contrainte : les tests ne pouvaient pas le détecter, d'où ce
 * défaut trouvé seulement au déploiement du staging.
 *
 * Le fichier s'exécute **juste avant** `000012` (même préfixe de date, numéro inférieur) et
 * crée l'index autonome dont la clé étrangère a besoin. La migration `000012` n'est **pas**
 * modifiée : elle a déjà été exécutée sur le staging (AGENTS.md §6).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->index('tenant_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['tenant_id']);
        });
    }
};

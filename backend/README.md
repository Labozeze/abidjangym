# Backend AbidjanGym — Entité B, DeepSeek.

API REST et backoffice de la plateforme SaaS multi-tenant **AbidjanGym** (Laravel 13, PHP 8.3+).
Contrat d'API : [`../docs/api/openapi.yaml`](../docs/api/openapi.yaml).

| Élément | Version |
|---|---|
| Laravel | 13.39 |
| PHP | 8.3+ |
| Base de données | MySQL 8 (production) — SQLite en mémoire pour les tests |
| Lint | Laravel Pint 1.27 |
| Tests | PHPUnit 12 |

---

## 1. Prérequis

- **PHP 8.3 ou supérieur** (Laravel 13 exige `^8.3`), avec les extensions `pdo_mysql`,
  `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `curl`, `dom`, `xml`, `tokenizer`,
  `fileinfo`, `zip`.
- Composer 2.
- MySQL 8 pour le développement local (SQLite suffit pour les tests).

Sur ce poste, le binaire est `C:\wamp64\bin\php\php8.3.28\php.exe` : le `php` du `PATH` peut
pointer sur une autre version, donc vérifier `php -v` avant de lancer Composer.

Aucun Redis, aucun Docker, aucun worker de file d'attente : l'hébergement Hostinger est
mutualisé (CDC §3.2). Les tâches asynchrones passent par le planificateur Laravel, lancé par
un cron `php artisan schedule:run`.

## 2. Installation

```bash
cd backend

composer install      # crée aussi .env à partir de .env.example s'il manque
php artisan key:generate

# Renseigner les accès MySQL dans .env, puis :
php artisan migrate
php artisan serve
```

L'API répond alors sur `http://localhost:8000/api/v1`.

> **Laravel 13 et PHP 8.3.** La montée de version a été décidée après la détection de cinq
> avis de sécurité sur Laravel 11 (dont un critique sur l'authentification par jeton),
> corrigés à partir de 12.60–12.69 et absents de Laravel 13. Vérifier après toute mise à jour
> des dépendances : `composer audit` doit rester silencieux.

## 3. Commandes utiles

| Commande | Rôle |
|---|---|
| `php artisan serve` | serveur de développement |
| `php artisan test` | suite de tests (SQLite en mémoire) |
| `php artisan test --filter=TenantIsolation` | tests d'isolation multi-tenant uniquement |
| `./vendor/bin/pint` | formate le code |
| `./vendor/bin/pint --test` | vérifie le format sans modifier (utilisé en revue) |
| `php artisan migrate` | applique les migrations |
| `php artisan migrate:rollback` | annule la dernière série (migrations réversibles) |
| `php artisan route:list --path=api` | liste les routes de l'API |

## 4. Endpoint de santé

`GET /api/v1/health` — public, sans session ni tenant :

```json
{
  "status": "ok",
  "version": "0.1.0",
  "time": "2026-11-01T10:49:28+00:00",
  "database": "ok"
}
```

Répond `503` avec `"status": "degraded"` et `"database": "unavailable"` si la base ne répond
pas. La réponse est marquée `Cache-Control: no-store`.

## 5. Isolation multi-tenant

Le socle du CDC §6.3 est en place dans `app/Support/Tenancy` et `app/Models/Concerns`.

**Modèle multi-tenant** — il suffit d'utiliser le trait :

```php
use App\Models\Concerns\BelongsToTenant;

final class Member extends Model
{
    use BelongsToTenant;
}
```

Le trait apporte :

1. **lecture filtrée** — un scope global restreint toute requête à la salle courante ;
2. **écriture sûre** — `tenant_id` est renseigné automatiquement à la création ;
3. **refus par défaut** — sans salle courante, aucune ligne n'est renvoyée (jamais « toutes
   les salles » par accident) ;
4. **garde-fou à la création** — créer une ligne pour une autre salle que la salle courante
   lève `TenantMismatchException` ;
5. **rattachement immuable** — `tenant_id` n'est **pas** dans `$fillable` et son changement à
   la mise à jour lève `TenantMismatchException`. Un `$model->update($request->validated())`
   mal filtré ne peut donc pas déplacer une ligne d'une salle vers une autre (CDC §6.3).

**Poser le contexte** (le middleware d'authentification le fera au ticket suivant) :

```php
use App\Support\Tenancy\TenantContext;

$context = app(TenantContext::class);

// Ponctuel : le contexte précédent est restauré, même en cas d'exception.
$members = $context->run($gymId, fn () => Member::query()->count());

// Ou pour la durée de la requête.
$context->set($gymId);
```

**Dérogations explicites** — réservées aux commandes de maintenance et aux exports
d'administration, jamais dans le chemin d'une requête HTTP :

```php
Member::withoutTenantScope()->count();   // toutes les salles
Member::query()->forTenant($gymId);      // une salle précise, sans changer de contexte
```

`ExampleRecord` (`app/Models/ExampleRecord.php`, table `example_records`) est un **modèle
d'exemple** qui matérialise ce socle et sert aux tests d'isolation. Il ne représente aucune
entité métier et sera supprimé dès l'arrivée du premier vrai modèle multi-tenant
(`Gym`, `Member`, `Subscription`, `Payment`).

## 6. Configuration

Tout passe par des variables d'environnement, documentées dans `.env.example` avec des valeurs
fictives. Toute nouvelle variable doit être ajoutée à ce fichier dans la même PR
(`backend/AGENTS.md` §5).

| Variable | Rôle |
|---|---|
| `APP_URL` | URL publique du backend |
| `APP_TIMEZONE` | fuseau par défaut (`Africa/Abidjan`) |
| `DB_*` | accès MySQL 8 |
| `SESSION_DRIVER`, `CACHE_STORE` | `file` uniquement (pas de Redis) |
| `QUEUE_CONNECTION` | `sync` — aucun worker persistant |
| `MAIL_*` | SMTP sortant |
| `CORS_ALLOWED_ORIGINS` | sous-domaines autorisés du front, du backoffice et de l'espace abonné |
| `API_VERSION` | version exposée par `/api/v1/health` |

Le fichier `.env` n'est jamais versionné ; seul `.env.example` (valeurs fictives) l'est.

## 7. Tests

```bash
php artisan test
```

La suite tourne sur SQLite en mémoire (`phpunit.xml`), avec `BCRYPT_ROUNDS=4` et une clé
d'application de test. Aucune donnée réelle n'est utilisée : les numéros et emails des tests
sont fictifs (`+225 07 00 00 00 01`, `test@example.com`).

`composer install` crée un `.env` à partir de `.env.example` s'il n'en existe pas (script
`post-install-cmd`). Ce fichier n'est pas indispensable aux tests, mais sans lui PHPUnit
signale l'avertissement `file_get_contents(.env)` émis par phpdotenv : sur un clone propre,
la suite apparaît alors en « warnings » au lieu de « passed ».

Couverture actuelle :

| Test | Ce qu'il prouve |
|---|---|
| `Tests\Feature\Api\V1\HealthTest` | `/api/v1/health` répond 200, expose la version, n'est pas cachable |
| `Tests\Feature\Tenancy\TenantIsolationTest` | une salle ne voit ni ne modifie les données d'une autre ; sans contexte, aucune ligne ; `tenant_id` non assignable en masse et immuable ; contexte restauré après usage |

## 8. Reste à faire (tickets suivants)

- authentification Sanctum (cookie httpOnly pour le backoffice, Bearer pour l'API) et rôles
  `admin` / `cashier` ;
- middleware posant le `TenantContext` à partir du jeton authentifié ;
- modèles et migrations métier (`gyms`, `members`, `plans`, `subscriptions`, `payments`) ;
- implémentation des 66 opérations du contrat `docs/api/openapi.yaml`.

Valeurs en attente `[À FOURNIR]` : SMTP réel, identifiants Orange Money / MTN MoMo / Wave /
CinetPay, fournisseurs WhatsApp et SMS, URL des callbacks.

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

**Poser le contexte** — le middleware `tenant` le fait pour une requête authentifiée ; sinon,
explicitement :

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

**Modèles livrés (ticket #39)**

| Modèle | Table | Rôle |
|---|---|---|
| `Gym` | `gyms` | la salle, c'est-à-dire **le tenant** : ses `id` servent de `tenant_id` partout ailleurs |
| `GymBanner` | `gym_banners` | bannières de la salle, trois maximum (CDC §4.2.3) |
| `User` | `users` | gérant (`admin`) ou caissier (`cashier`), avec `is_active` (CDC §4.2.10) |
| `PaymentMethodSetting` | `payment_method_settings` | les sept moyens de paiement, leur activation on/off et leurs identifiants **chiffrés** (CDC §4.2.6, §6.6) |

`Gym` n'utilise pas `BelongsToTenant` : c'est le tenant lui-même, il n'a pas de `tenant_id`.

**Chiffrement des identifiants de prestataires.** `PaymentMethodSetting::$credentials` cumule deux
protections : le cast `encrypted:array` (chiffrement AES-256 avec `APP_KEY` avant écriture) et
`$hidden`, qui exclut le champ de `toArray()`, `toJson()` et donc de toute réponse d'API. Un test
vérifie la valeur brute en base *et* la sérialisation.

**Connexion et contexte.** Le même email peut exister dans deux salles : la connexion devra donc
chercher le compte **hors** du scope, puis poser le contexte, jamais l'inverse.

```php
$user = User::withoutTenantScope()->where('email', $email)->first();
// … vérification du mot de passe, puis :
app(TenantContext::class)->set($user->tenant_id);
```

**Middleware prêt pour l'authentification.** L'alias `tenant` (`ResolveTenantContext`) déduit la
salle de l'utilisateur authentifié et vide le contexte en sortie de requête. À appliquer après le
middleware d'authentification :

```php
Route::middleware(['auth:sanctum', 'tenant'])->group(function (): void {
    // routes métier
});
```

**Données de démonstration.** `php artisan db:seed` crée deux salles fictives — « FitZone Test »
et « Salle Témoin » — avec un gérant et un caissier chacune, et les sept moyens de paiement
(espèces seules activées). Aucune donnée réelle : emails en `@example.com`, téléphones en
`+225 07 00 00 00 XX` (docs/rapports/strategie-de-tests.md §5).

Le seeder refuse de s'exécuter hors `local` et `testing` : les comptes de démonstration
utilisent un mot de passe qui ne respecte pas la règle du CDC §6.2, ils n'ont donc rien à faire
sur le staging ou en production.

## 6. Authentification (Sanctum, contrat v0.3.0)

Six routes, conformes au contrat (`docs/api/openapi.yaml`, section Authentification) :

| Méthode | Route | Accès |
|---|---|---|
| POST | `/api/v1/auth/login` | public, 5 req/min par IP |
| POST | `/api/v1/auth/refresh` | public, 30 req/min par IP |
| POST | `/api/v1/auth/forgot-password` | public, 5 req/min par IP |
| POST | `/api/v1/auth/reset-password` | public, 5 req/min par IP |
| POST | `/api/v1/auth/logout` | authentifié |
| GET | `/api/v1/auth/me` | authentifié |

**Deux modes d'authentification, un seul compte.** La connexion renvoie un jeton Bearer
(`access_token`) pour l'API, et pose deux cookies `httpOnly` pour le backoffice :
`ag_session` (jeton d'accès, 1 h) et `ag_refresh` (rafraîchissement). Le middleware
`AcceptSessionCookie` traduit le cookie en en-tête `Authorization`, l'en-tête explicite restant
prioritaire.

**Politique de session** (décision de Rufin, PR #20) : jeton d'accès **1 h** ; rafraîchissement
tant qu'il y a de l'activité, session **plafonnée à 8 h** ; option « se souvenir de cet
appareil » → plafond porté à **30 jours**. Les jetons de rafraîchissement sont à usage unique
(rotation) et la déconnexion révoque la session entière.

**Mot de passe** : bcrypt, coût 12 en production (`BCRYPT_ROUNDS`), 8 caractères minimum avec
une majuscule et un chiffre ; un utilisateur désactivé (`is_active = false`) ne peut pas se
connecter. La réinitialisation envoie un lien valable **1 h**, et révoque toutes les sessions
du compte.

**Email unique sur toute la plateforme** (décision de Rufin du 01/11/2026) : la connexion et la
réinitialisation retrouvent le compte par le seul email. La contrainte est portée par une
migration dédiée (`2026_11_01_000012_…`), et les futurs `POST /users` et `POST /gyms` doivent
valider avec `Rule::unique('users', 'email')` (message générique, jamais le nom de la salle).

**Emails en développement.** Pour consulter un lien de réinitialisation sans SMTP, utiliser
`MAIL_MAILER=log` : le message est écrit dans `storage/logs/laravel.log`. Aucun mot de passe ni
jeton n'est journalisé par l'application.

**Lien de réinitialisation.** Il pointe vers la page du **backoffice**
(`BACKOFFICE_URL` + `BACKOFFICE_RESET_PATH`), jamais vers une route de l'API : le backend n'a
pas d'écran, et la notification standard de Laravel échouait en `500` faute de route
`password.reset`. Le chemin de l'écran est `[À FOURNIR]` tant que le backoffice ne l'a pas
livré. La réponse de `POST /auth/forgot-password` est `202` **dans tous les cas** — adresse
inconnue ou échec d'envoi du message — pour ne jamais révéler quels comptes existent.

## 7. Routes publiques du site vitrine

Deux routes, sans authentification, limitées à **5 requêtes par minute et par IP** :

| Méthode | Route | Effet |
|---|---|---|
| POST | `/api/v1/gyms` | inscription d'une salle : salle + gérant + formule + réglages de paiement |
| POST | `/api/v1/contact-messages` | enregistrement du message puis email à l'équipe |

**Onboarding (`POST /gyms`).** Tout est écrit dans **une transaction** : salle, compte gérant
(`admin`) et les sept réglages de moyens de paiement, dont **seules les espèces sont activées**
(décision du 01/11/2026). Un échec au milieu ne laisse donc jamais une salle sans gérant ni un
gérant sans salle. La formule choisie (`starter`, `essentiel`, `pro`) est stockée dans
`gyms.plan_code`, colonne interne non encore exposée par le contrat. L'email de confirmation
part en différé (`defer()`) avec le lien du backoffice ; un SMTP en panne ne fait pas échouer
l'inscription. L'email du gérant étant unique sur toute la plateforme, un doublon renvoie `422`
avec « Cette adresse email est déjà utilisée. », qui ne révèle pas la salle concernée.

**Contact (`POST /contact-messages`).** Le message est enregistré avant l'envoi, puis l'email
part en différé vers `CONTACT_EMAIL`. Le sujet est refusé s'il contient un retour à la ligne
(protection contre l'injection d'en-têtes, CDC §6.4) ; l'adresse de l'expéditeur est validée
avant d'être placée en `Reply-To`.

## 8. Configuration

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
| `BACKOFFICE_URL` | URL publique du backoffice, base des liens de réinitialisation et du tableau de bord |
| `BACKOFFICE_RESET_PATH` | chemin de l'écran de réinitialisation (`[À FOURNIR]` tant que l'écran n'existe pas) |
| `CONTACT_EMAIL` | destinataire des messages du formulaire de contact (`[À FOURNIR]`) |

Le fichier `.env` n'est jamais versionné ; seul `.env.example` (valeurs fictives) l'est.

## 9. Tests

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
| `Tests\Feature\Tenancy\TenantIsolationTest` | une salle ne voit ni ne modifie les données d'une autre ; sans contexte, aucune ligne ; `tenant_id` non assignable en masse et immuable ; email unique plateforme ; contexte restauré après usage |
| `Tests\Feature\Auth\LoginTest` | connexion OK (jetons + cookies `httpOnly`), cookies et Bearer utilisables, mauvais mot de passe, compte désactivé, email insensible à la casse, coût bcrypt |
| `Tests\Feature\Auth\RefreshTest` | rotation du jeton de rafraîchissement, plafond de 8 h, extension « se souvenir », jeton d'accès expiré refusé |
| `Tests\Feature\Auth\LogoutTest` | révocation de la session et expiration des cookies |
| `Tests\Feature\Auth\MeTest` | `GET /auth/me` : utilisateur, rôle, `gym_id`, jamais `password` ni `tenant_id` |
| `Tests\Feature\Auth\PasswordResetTest` | lien valable, mot de passe faible refusé, jeton invalide refusé, sessions révoquées |
| `Tests\Feature\Auth\RateLimitTest` | `429` après 5 tentatives (connexion, mot de passe oublié) |
| `Tests\Feature\Auth\ForgotPasswordDeferralTest` | l'email de réinitialisation est **différé** : rien n'est envoyé pendant la requête |
| `Tests\Feature\Onboarding\GymRegistrationTest` | `201` conforme, 7 moyens dont `cash` seul actif, connexion du gérant, email déjà utilisé (`422` sans révéler la salle), transaction annulée en cas d'échec, `429` |
| `Tests\Feature\Contact\ContactMessageTest` | enregistrement + email, refus de l'injection d'en-tête, validation, `429` |

## 10. Reste à faire (tickets suivants)

- `GET /users`, `POST /users` et la gestion des caissiers (avec `Rule::unique('users','email')`) ;
- modèles et migrations métier (`members`, `plans`, `subscriptions`, `payments`) ;
- implémentation des opérations restantes du contrat `docs/api/openapi.yaml` ;
- écrans du backoffice (`BACKOFFICE_RESET_PATH`, tableau de bord) côté Entité A.

Valeurs en attente `[À FOURNIR]` : SMTP réel, adresse `CONTACT_EMAIL`, identifiants Orange Money /
MTN MoMo / Wave / CinetPay, fournisseurs WhatsApp et SMS, URL des callbacks.

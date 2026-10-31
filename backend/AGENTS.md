# AGENTS.md — Entité B, Backend (DeepSeek)

> Lis d'abord le fichier [`AGENTS.md`](../AGENTS.md) à la racine : ses règles s'appliquent ici.

## Ta mission
Développer l'**API REST** et le **backoffice** d'AbidjanGym (CDC §2.3, §4.2, §7.2) :
- API JSON versionnée `/api/v1/` (consommée par le frontend et le backoffice) ;
- backoffice des gérants et caissiers (dashboard, abonnés, plans, paiements, scan QR, notifications, rapports, paramètres) en suivant les maquettes `demo/` ;
- multi-tenant, authentification et rôles, abonnements, paiements (cash, Orange Money, MTN MoMo, Wave, CinetPay), notifications (WhatsApp, SMS, Email), QR codes, reçus PDF ;
- le **contrat d'API** `docs/api/openapi.yaml`.

## Ton périmètre d'écriture
`backend/`, `docs/api/`, `docs/adr/`. Branches : `feature/b-<n° ticket>-<sujet>`.

Ton dossier de travail local : `C:\Users\HP\Documents\deepseek-harness\default-workspace\abidjangym-back` (clone dédié, dans ton espace de travail).

## Stack imposée
| Élément | Choix |
|---|---|
| Framework | **Laravel 11**, PHP 8.2+ |
| Base de données | MySQL 8 (Hostinger). SQLite autorisé **uniquement** pour les tests |
| ORM | Eloquent, aucune requête SQL construite par concaténation |
| Authentification | Laravel Sanctum. Jetons en cookie `httpOnly` pour le backoffice, Bearer pour l'API (CDC §6.2) |
| PDF | Dompdf |
| QR codes | BaconQrCode / simple-qrcode (UUID v4, PNG 800×800 + SVG) |
| Cache et sessions | Fichiers ou base de données (**pas** de Redis) |
| Tâches asynchrones | **Laravel Scheduler lancé par cron** (`php artisan schedule:run` chaque minute). **Pas** de `queue:work` persistant |
| Emails | Laravel Mail (SMTP, configuré par variables d'environnement) |
| Lint | Laravel Pint |
| Tests | PHPUnit, couverture visée ≥ 70 % |

## Règles d'architecture
1. **Multi-tenant strict (CDC §6.3).** Chaque table métier a un `tenant_id`. Utilise un *global scope* Eloquent appliqué automatiquement, et vérifie l'appartenance de chaque ressource au tenant courant avant toute lecture ou écriture. Chaque fonctionnalité a au moins un test qui prouve qu'une salle ne voit pas les données d'une autre.
2. **Conventions d'API (CDC §7.2) :** JSON, préfixe `/api/v1/`, pagination `?page=&per_page=`, codes HTTP standards (200, 201, 400, 401, 403, 404, 422, 500), erreurs au format `{ "message": "...", "errors": {} }`.
3. **Le contrat d'abord.** `docs/api/openapi.yaml` (OpenAPI 3.0) est rédigé **avant** l'implémentation et validé par Rufin. Ensuite, le code respecte le contrat. La CI vérifiera la conformité (tests de contrat de Valère).
4. **Sécurité (CDC §6) :** mots de passe hachés en bcrypt (cost ≥ 12), limitation des tentatives de connexion (5 par minute et par IP), CSRF, validation de toutes les entrées, identifiants d'API des salles chiffrés en base (AES-256), journal d'audit des actions sensibles, aucun mot de passe ni jeton dans les logs.
5. **Configuration :** tout passe par des variables d'environnement documentées dans `backend/.env.example` (valeurs fictives). Ajoute toute nouvelle variable à ce fichier dans la même PR.
6. **Migrations réversibles** (méthode `down` implémentée).
7. **Endpoint de santé** `GET /api/v1/health` (utilisé par le déploiement et le monitoring).
8. **Déploiement :** la CI installe `vendor/` et Hostinger exécute `php artisan migrate --force`. Le code ne doit donc dépendre d'aucune commande manuelle sur le serveur. La racine web est `backend/public/`.

## Paiements et notifications
Les identifiants réels (Orange Money, MTN, Wave, CinetPay, Twilio/360dialog, SMS) **ne sont pas disponibles**. Développe derrière des interfaces (*drivers*) avec un driver fictif pour les tests et le développement. Marque `[À FOURNIR]` partout où une valeur réelle sera nécessaire.

## Ton outil (DeepSeek harness)
- Tu ne peux écrire que dans ton espace de travail : c'est normal, ton clone y est.
- `gh` n'est pas installé : c'est prévu. Pousse ta branche avec `git push -u origin <branche>`, la PR s'ouvre automatiquement.

## À ne pas faire
- Modifier `frontend/`, `.github/`, `tests/` ou tout fichier hors de ton périmètre
- Lancer un worker persistant, utiliser Redis ou Docker
- Inventer un identifiant, une URL de callback ou un numéro réel
- Modifier le contrat d'API figé sans PR étiquetée `contrat-api`

# Serveur fictif de l'API (Prism)

**Maintenu par :** Eric, DevOps (Entité C)
**Pour :** l'Entité A (Codex), qui peut développer le front sans attendre le backend (plan de collaboration §8)
**Testé le :** 01/11/2026, avec `@stoplight/prism-cli` 5.16.0 et le contrat v0.2.0

---

## Lancer le serveur fictif

Depuis la racine du dépôt :

```bash
npx --yes @stoplight/prism-cli@5.16.0 mock docs/api/openapi.yaml -p 4010 -h 127.0.0.1
```

Le serveur répond sur **http://127.0.0.1:4010**. Il génère les réponses à partir des schémas et des exemples de `docs/api/openapi.yaml`. Arrêt : `Ctrl+C`.

Pour obtenir des données variées à chaque appel au lieu des exemples fixes, ajoutez `-d` (mode dynamique).

## ⚠️ Pas de préfixe `/api/v1` sur le serveur fictif

Prism sert les routes telles qu'elles sont écrites dans le contrat (`/health`, `/plans`…), **sans** le préfixe `/api/v1` des vrais serveurs.

| Environnement | `NUXT_PUBLIC_API_BASE` |
|---|---|
| Serveur fictif (Prism) | `http://127.0.0.1:4010` |
| Backend local (`php artisan serve`) | `http://localhost:8000/api/v1` |
| Staging | `https://staging.abidjangym.com/api/v1` |
| Production | `https://api.abidjangym.com/api/v1` |

Dans `frontend/.env` (non versionné) :

```
NUXT_PUBLIC_API_BASE=http://127.0.0.1:4010
```

Le code du front ne doit **jamais** écrire `/api/v1` en dur : il préfixe toujours les routes avec `NUXT_PUBLIC_API_BASE`.

## Ce que le serveur fictif vérifie

Résultats du test du 01/11/2026 :

| Requête | Réponse |
|---|---|
| `GET /health` | `200` avec l'exemple du contrat |
| `GET /plans` sans jeton | `401` (route protégée) |
| `GET /plans` avec `Authorization: Bearer <n'importe quelle valeur>` | `200` avec une liste paginée (`data`, `meta`, `links`) |
| `POST /contact-messages` avec un corps vide | `422` (validation selon le schéma) |

Prism **valide les requêtes** du front contre le contrat. Un `422` ou un `400` inattendu signale donc souvent un écart entre le front et le contrat. Si le contrat semble faux ou incomplet, **ne pas le contourner** : le signaler dans la PR. Benjamin le remontera à l'Entité B (PR `contrat-api`).

## Limites

- Les données sont fictives et **sans état** : une création (`POST`) n'apparaît pas dans la liste suivante (`GET`).
- L'authentification n'est pas réelle : n'importe quel jeton est accepté. Les règles de rôle (caissier ou gérant) et d'isolation entre salles ne sont **pas** simulées. Elles seront testées sur le vrai backend (stratégie de tests de Valère).
- Le serveur reflète la version du contrat présente sur votre branche. Pensez à `git pull` après chaque évolution du contrat.

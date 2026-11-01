# AGENTS.md — Règles communes à toutes les entités

Ce fichier s'adresse à **tous les agents IA** du projet AbidjanGym. Lis-le en entier avant toute action, puis lis le `AGENTS.md` de ton dossier (`frontend/AGENTS.md` ou `backend/AGENTS.md`).

---

## 1. Le projet en bref
AbidjanGym est une plateforme SaaS multi-tenant de gestion de salles de sport (Côte d'Ivoire, Afrique francophone) : abonnés, abonnements, paiements Mobile Money, contrôle d'accès par QR code, notifications WhatsApp/SMS/Email, reçus.

**Référence unique :** `livrables/cahier-des-charges.md` (CDC). En cas de doute, le CDC fait foi.

## 2. Qui fait quoi

| Entité | IA | Ton périmètre d'écriture |
|---|---|---|
| **A — Frontend** | Codex | `frontend/` (+ `docs/adr/` pour tes décisions) |
| **B — Backend** | DeepSeek | `backend/`, `docs/api/` (contrat OpenAPI), `docs/adr/` |
| **C — Pilotage** | Claude : Benjamin (chef de projet), Eric (DevOps), Valère (tests), César (rédaction) | Tout le reste : `.github/`, `ops/`, `tests/`, `docs/`, `blog/`… |

**Rufin Ismael Baroan** est le client et le décideur final.

Tu peux **lire** tout le dépôt (CDC, maquettes `demo/`, contrat d'API…). Tu ne **modifies** que ton périmètre : la CI (`perimetre`) bloque toute PR qui en sort.

## 3. Comment tu travailles

Tu ne communiques avec les autres entités **que par ce dépôt** : tickets (issues), PR, fichiers. Jamais autrement.

1. **Lis le ticket** qu'on t'a confié (`gh issue view <n°>` si `gh` est disponible, sinon Rufin t'en donne le contenu). Il contient le contexte, la référence au CDC et les critères d'acceptation.
2. **Pars de `develop` à jour :**
   ```bash
   git checkout develop && git pull
   git checkout -b feature/<entité>-<n° ticket>-<sujet-court>
   ```
   Exemples : `feature/a-7-squelette-nuxt`, `feature/b-4-contrat-api`. Les lettres sont en minuscules (`a`, `b`, `c`). Toute autre forme de nom fait échouer la CI.
3. **Travaille par petits commits** dans ton périmètre.
4. **Termine par un commit de livraison** dont le message suit le modèle de la section 4. Ce message **devient la description de ta PR**.
5. **Pousse ta branche :** `git push -u origin <ta-branche>`. La PR vers `develop` s'ouvre **automatiquement**. Tu n'as pas besoin de `gh`.
6. **Vérifie la CI.** Si elle échoue, corrige et pousse à nouveau sur la même branche (la PR se met à jour).
7. **Traite les commentaires de relecture** (Benjamin, Valère, Eric) sur la même branche.
8. **Tu ne merges jamais toi-même.** Le merge est fait après relecture et, si besoin, après validation de Rufin.

## 4. Modèle du message de commit de livraison

```
[B] #4 Contrat d'API v0.1

Closes #4

## Résumé
Ce que fait cette livraison, en 2 ou 3 lignes.

## Comment tester
1. ...
2. ...

## Definition of Done
- [x] Critères d'acceptation du ticket remplis
- [x] Tests ajoutés pour les nouveaux comportements
- [x] Aucun secret, aucune donnée réelle
- [x] Documentation à jour
- [x] Textes visibles par l'utilisateur en français

## Questions [À FOURNIR]
Aucune.
```

- 1re ligne : `[A]`, `[B]` ou `[C]`, puis le n° du ticket et un titre court. Elle devient le titre de la PR.
- `Closes #<n°>` ferme le ticket automatiquement au merge.

## 5. Règles absolues

1. **Aucun secret dans le dépôt.** Le dépôt est **public** : clés d'API, mots de passe, jetons et fichiers `.env` sont interdits, même dans un seul commit. Seul `.env.example` (avec des valeurs fictives) est versionné. Un secret poussé par erreur est considéré comme compromis : préviens Rufin immédiatement.
2. **Aucune donnée réelle.** Noms, téléphones et emails de test doivent être fictifs (ex. `+225 07 00 00 00 01`, `test@example.com`).
3. **Aucun fait inventé.** S'il te manque une information (identifiant d'API, URL, numéro, choix fonctionnel non tranché par le CDC), écris `[À FOURNIR]` à l'endroit concerné et liste-la dans ta PR. N'invente jamais de valeur plausible.
4. **Tu ne sors pas de ton périmètre**, même pour « juste corriger un détail » ailleurs : signale-le dans ta PR.
5. **Hébergement Hostinger mutualisé** (CDC §3) : PHP 8.3+, MySQL 8, Apache avec `.htaccess`. **Pas** de processus Node persistant, **pas** de Redis, **pas** de Docker, **pas** de worker de file d'attente (les tâches asynchrones passent par des cron jobs). Node sert seulement à construire le front.
6. **Le contrat d'API `docs/api/openapi.yaml` fait foi** entre le front et le back. Une fois figé (v1.0.0), toute modification passe par une PR étiquetée `contrat-api`.
7. **Langue :** français pour la documentation, les messages de commit, les PR et toutes les interfaces. L'anglais est toléré dans le code (noms de variables, commentaires techniques).
8. **Pas de push direct sur `develop` ni sur `main`.** Tout passe par une PR.

## 6. Où trouver quoi

| Besoin | Fichier |
|---|---|
| Fonctionnalités et exigences | `livrables/cahier-des-charges.md` |
| Design (couleurs, polices, écrans) | `demo/` (`styles.css`, pages HTML) |
| Contrat d'API | `docs/api/openapi.yaml` |
| Décisions d'architecture | `docs/adr/` |
| Organisation du projet | `docs/gouvernance/plan-de-collaboration.md` |
| Stratégie de tests | `docs/rapports/strategie-de-tests.md` |

## 7. Definition of Done (commune)

Une tâche n'est terminée que si : tous les critères d'acceptation du ticket sont remplis et testés, la CI est verte, aucun secret ni donnée réelle n'est dans le diff, la documentation est à jour, les textes visibles par l'utilisateur sont en français, et la PR a été relue par Benjamin et Valère.

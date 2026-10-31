# Plan de collaboration multi-entités — AbidjanGym

**Rédigé par :** Benjamin — Chef de projet (Entité C)
**Validé par :** Rufin Ismael Baroan (client), le 30 octobre 2026
**Version :** 1.0 — validée. Lancement du sprint 0 : 31 octobre 2026
**Date :** 30 octobre 2026
**Référence :** [Cahier des charges v2.0](../../livrables/cahier-des-charges.md), section 2

---

## 1. Qui fait quoi

| Entité | Fournisseur IA | Outil | Périmètre | Dossier du dépôt |
|---|---|---|---|---|
| **A — Frontend** | Codex (OpenAI) | Codex desktop | Landing, contact, onboarding, espace abonné, pages légales | `frontend/` |
| **B — Backend** | DeepSeek | DeepSeek harness | API REST `/api/v1`, backoffice, base de données, paiements, notifications | `backend/` |
| **C — Pilotage, DevOps, Tests, Rédaction** | Claude | Claude Code desktop | Coordination, CI/CD, tests, sécurité, documentation, blog | `.github/`, `ops/`, `tests/`, `docs/`, `blog/` |

L'Entité C regroupe quatre agents :

| Agent | Rôle | Fiche de rôle |
|---|---|---|
| **Benjamin** | Chef de projet | [benjamin-chef-de-projet.md](roles/benjamin-chef-de-projet.md) |
| **Eric** | DevOps : dépôt, CI/CD, Hostinger, sauvegardes, monitoring | [eric-devops.md](roles/eric-devops.md) |
| **Valère** | Tests et assurance qualité : stratégie, E2E, API, contrat, sécurité, performance, recette | [valere-tests-qa.md](roles/valere-tests-qa.md) |
| **César** | Rédaction : journal de bord, notes de version, article de blog | [cesar-redaction.md](roles/cesar-redaction.md) |

**Eric et Valère :** Valère écrit **ce qui est vérifié**, Eric construit **la chaîne qui l'exécute et qui déploie**.

**Rufin** est le décideur final. Il valide aux points de contrôle (section 7) et il est le seul à lancer les agents A et B.

---

## 2. Le problème à résoudre

Codex, DeepSeek et Claude **ne peuvent pas se parler entre eux**, et la règle 2.5.1 du cahier des charges interdit de toute façon toute communication directe. Sans organisation, Rufin devient un « facteur » qui copie-colle du contexte d'un outil à l'autre. C'est lent, on perd de l'information et le projet ne passe pas à l'échelle.

**Objectif :** que Rufin n'ait plus qu'à **lancer** chaque agent et à **décider**. Il ne doit jamais avoir à transporter de l'information à la main.

---

## 3. La solution retenue : « le dépôt Git est la seule salle de réunion »

Tout ce qu'une entité doit savoir se trouve **dans le dépôt GitHub `Labozeze/abidjangym`**. Tout ce qu'elle produit **y retourne**. Aucune information ne circule ailleurs : ni chat, ni copier-coller.

| Besoin | Outil | Pourquoi |
|---|---|---|
| Savoir quoi faire | **GitHub Issues** (tickets) | Un ticket = une tâche précise, assignée à une entité, avec des critères d'acceptation |
| Voir l'avancement | **GitHub Projects** (tableau kanban) | Rufin voit tout en un coup d'œil, sans rien demander à personne |
| Livrer | **Pull Requests** | Chaque livraison est relue, testée automatiquement, puis validée |
| Connaître les règles | **`AGENTS.md`** à la racine + un `AGENTS.md` par dossier d'entité | Codex lit ce fichier automatiquement, et la plupart des agents de code aussi |
| Partager le contrat front ↔ back | **`docs/api/openapi.yaml`** | Une seule source de vérité pour l'API |
| Garder la trace des décisions | **`docs/adr/`** (Architecture Decision Records) | Évite de rediscuter ce qui est déjà tranché |
| Contrôler automatiquement | **GitHub Actions** (CI) | Lint, tests, contrat d'API et périmètre sont vérifiés sans intervention humaine |

### Pourquoi ce choix plutôt qu'un autre

| Alternative | Pourquoi elle est écartée |
|---|---|
| Notion / Trello + dépôt Git | Deux sources de vérité : les agents de code ne lisent pas Notion, donc Rufin recopierait |
| Un dépôt par entité | Le contrat d'API se désynchronise, la CI est triplée, impossible de livrer front + back ensemble |
| Échanges par chat avec copier-coller | Aucune traçabilité, perte de contexte, Rufin devient le goulot d'étranglement |
| Un seul fournisseur IA pour tout | Contraire au principe multi-entités du projet |

---

## 4. Outillage et postes de travail

Pour être efficace, chaque agent doit **lire et écrire directement dans le dépôt**.

| Entité | Outil retenu | Fonctionnement |
|---|---|---|
| A — Codex | **Codex desktop** | Travaille dans son propre clone local, lit `AGENTS.md`, pousse sa branche et ouvre sa PR |
| B — DeepSeek | **DeepSeek harness (DSH)** | Travaille dans son propre clone, **placé dans son espace de travail** (le bac à sable de DSH lui interdit d'écrire ailleurs). Lit `AGENTS.md` automatiquement. Utilise `git` pour pousser sa branche. **Pas de `gh`** : sa PR est ouverte automatiquement par GitHub (voir ci-dessous) |
| C — Claude | **Claude Code** desktop | Agents Benjamin, Eric, Valère et César (`.claude/agents/`), `git`, et `gh` une fois installé |

### Capacités de DeepSeek harness (testées par DSH le 30/10/2026)

| Capacité | Résultat | Conséquence |
|---|---|---|
| Modifier des fichiers | ✅ uniquement dans l'espace de travail de la session | Le clone `abidjangym-back` doit être **à l'intérieur** de cet espace |
| Écrire ailleurs sur le disque | ❌ refusé par le bac à sable | Bonne isolation : DSH ne peut pas toucher aux dossiers de Codex ni de Claude |
| Lire `AGENTS.md` | ✅ chargé automatiquement | Le brief `backend/AGENTS.md` suffit, sans copier-coller |
| `git` | ✅ version 2.54, clone réseau fonctionnel | Il peut cloner, committer et pousser |
| `gh` | ⚠️ non installé | Il ne peut pas ouvrir de PR lui-même : un workflow GitHub le fait pour lui |

### PR ouverte automatiquement (pour toutes les entités)

Pour ne dépendre d'aucun outil local, Eric met en place un workflow GitHub Actions : **dès qu'une branche `feature/*` est poussée, GitHub ouvre la PR tout seul**, en prenant le message du dernier commit comme description et en ajoutant l'étiquette de l'entité d'après le nom de la branche (`feature/b-*` donne `entite-b`). Une entité n'a donc besoin que de `git push`. Ce workflow sert à DeepSeek, et aussi à Codex et Claude si `gh` leur manque.

> **Message de commit final attendu** (il devient la description de la PR) : le modèle sera donné dans `AGENTS.md`. Il reprend le résumé, « Closes #n° », comment tester et la checklist de la Definition of Done.

### Un clone par entité (obligatoire)

Les trois outils tournent sur le même ordinateur. S'ils travaillaient dans le même dossier, l'un changerait de branche ou écraserait des fichiers sous les pieds d'un autre. Chaque entité a donc **son propre dossier**, relié au même dépôt GitHub :

| Dossier | Utilisé par | Branches |
|---|---|---|
| `C:\Users\HP\Documents\dev\abidjangym` | Claude (Entité C) | `feature/c-*`, relectures |
| `C:\Users\HP\Documents\dev\abidjangym-front` | Codex desktop (Entité A) | `feature/a-*` |
| `C:\Users\HP\Documents\deepseek-harness\default-workspace\abidjangym-back` | DeepSeek harness (Entité B) | `feature/b-*` |

Les entités ne s'échangent rien par le disque : uniquement par GitHub (push, PR, merge). Eric documente l'installation dans `docs/ops/postes-de-travail.md`.

> **Espace de travail de DeepSeek harness :** `C:\Users\HP\Documents\deepseek-harness\default-workspace`. Le clone de l'Entité B y est placé.
>
> **À tester au sprint 0 :** `git push` depuis DSH vers `Labozeze/abidjangym`. Le clone d'un dépôt public marche sans identifiant, mais le push en demande un. Si le bac à sable bloque le gestionnaire d'identifiants Windows, on utilisera un jeton GitHub limité à ce seul dépôt (*fine-grained token*).

---

## 5. Organisation du dépôt (monorepo)

```
abidjangym/
├── AGENTS.md                 ← règles communes à toutes les entités
├── CLAUDE.md                 ← consignes propres à l'Entité C
├── .claude/agents/           ← Benjamin, Eric, Valère, César
├── .github/
│   ├── workflows/            ← CI/CD (Eric)
│   ├── ISSUE_TEMPLATE/       ← modèles de tickets (Benjamin)
│   └── PULL_REQUEST_TEMPLATE.md
├── frontend/                 ← Entité A (Codex) + frontend/AGENTS.md
├── backend/                  ← Entité B (DeepSeek) + backend/AGENTS.md
├── docs/
│   ├── api/openapi.yaml      ← contrat d'API (rédigé par B, figé par C)
│   ├── adr/                  ← décisions d'architecture
│   ├── gouvernance/          ← ce plan et les fiches de rôle
│   ├── ops/                  ← documentation d'exploitation (Eric)
│   └── rapports/             ← rapports de sprint et de tests
├── tests/                    ← E2E Playwright, Postman/Newman, contrat (Valère)
├── ops/                      ← .htaccess, scripts de déploiement et de sauvegarde (Eric)
├── blog/                     ← journal de bord et article (César)
├── demo/                     ← maquettes de référence (design system)
└── livrables/                ← cahier des charges
```

**Garde-fou de périmètre :** une vérification de CI (mise en place par Eric) bloque toute PR d'une entité qui modifie des fichiers hors de son dossier. Exemple : une PR étiquetée `entite-a` qui touche `backend/` est refusée. C'est ce qui rend le monorepo sûr avec trois IA différentes.

### Déploiement : Git Hostinger relié à des branches de déploiement

L'hébergement Hostinger (avec accès SSH) est relié directement au dépôt GitHub grâce à la fonction **Git de l'hPanel** et à une clé de déploiement. Hostinger ne compile rien : il ne suit donc **jamais** `main` ni `develop`, mais des branches qui contiennent un site **déjà testé et construit** par la CI.

```
develop ──► CI : tests (Valère) + construction (Eric) ──► deploy-staging    ──► staging.abidjangym.com
main    ──► CI : tests + construction + feu vert Rufin ──► deploy-production ──► abidjangym.com, app., api., member.
```

| Partie | Contenu de la branche de déploiement | Mise à jour sur Hostinger |
|---|---|---|
| Frontend (site statique) | Résultat de `nuxt generate` | **Déploiement automatique** de Git Hostinger (webhook) : aucune étape supplémentaire |
| Backend (Laravel) | Code + `vendor/` (installé par la CI) | La CI se connecte en **SSH** et enchaîne dans l'ordre : `git pull`, `php artisan migrate --force`, mise en cache de la configuration. On garantit ainsi que les migrations passent juste après le code, jamais avant |

- La racine web des sous-domaines `api.` et `app.` pointe sur le dossier `public/` de Laravel.
- GitHub ne contient aucun mot de passe Hostinger : seulement une **clé SSH dédiée au déploiement**, saisie par Rufin dans les GitHub Secrets.
- Les branches `deploy-*` sont écrites uniquement par la CI. Personne n'y pousse à la main.

---

## 6. Le cycle de vie d'une tâche

```
 Benjamin              Rufin              Entité A/B            CI (Eric)              Benjamin + Valère       Rufin
 rédige le ticket ──►  lance l'agent ──►  branche + code  ──►  lint, tests de   ──►   relecture         ──►  validation
 (#12, critères)       « traite #12 »     + PR                 Valère, contrat,        conformité CDC        (si point de
                                                               périmètre               + tests               contrôle)
                                                                   │                                              │
                                                                   ▼                                              ▼
                                                          échec → l'entité corrige                    merge develop → staging
                                                                                                      César consigne au journal
```

1. **Ticket.** Benjamin rédige l'issue à partir du modèle : contexte, référence au cahier des charges (§), critères d'acceptation vérifiables, fichiers concernés, dépendances, étiquettes.
2. **Lancement.** Rufin ouvre l'outil de l'entité dans son dossier et lui donne une consigne standard et courte :
   > *« Lis AGENTS.md, puis traite l'issue #12. Ouvre une PR quand c'est terminé. »*
3. **Réalisation.** L'entité travaille sur une branche `feature/<entité>-<n°>-<sujet>` (ex. `feature/a-12-landing`) et ouvre une PR en remplissant le modèle : résumé, comment tester, checklist de la Definition of Done, questions `[À FOURNIR]`.
4. **Contrôle automatique.** La CI (construite par Eric) exécute le lint, les suites de tests de Valère, la vérification du contrat d'API et le garde-fou de périmètre. En cas d'échec, l'entité corrige elle-même.
5. **Relecture.** Benjamin vérifie la conformité au cahier des charges, Valère les tests et la qualité, Eric les aspects d'infrastructure si la PR en touche. Leurs retours sont des commentaires sur la PR : Rufin relance simplement l'agent avec *« traite les commentaires de la PR #15 »*.
6. **Validation client** si le ticket porte l'étiquette `validation-client`.
7. **Intégration.** Merge dans `develop` : la CI construit la branche `deploy-staging` et Hostinger met à jour `staging.abidjangym.com` (voir section 5). La production (`main` puis `deploy-production`) n'est jamais déployée sans le feu vert explicite de Rufin.
8. **Trace.** César consigne les faits marquants dans le journal de bord.

### Tableau kanban (GitHub Projects)

`Backlog` → `Prêt` → `En cours` → `En revue` → `Validation client` → `Terminé`

### Étiquettes

| Famille | Valeurs |
|---|---|
| Entité | `entite-a`, `entite-b`, `entite-c` |
| Type | `feature`, `bug`, `doc`, `ops`, `test`, `securite` |
| Priorité | `P0` (bloquant), `P1` (sprint en cours), `P2` (plus tard) |
| Statut spécial | `a-fournir` (attend une info de Rufin), `validation-client`, `contrat-api`, `bloque` |

### Definition of Done (commune)

Une tâche n'est terminée que si :
- les critères d'acceptation du ticket sont tous remplis et couverts par un test ;
- la CI est verte : lint, tests, contrat, périmètre ;
- la couverture des tests ne baisse pas (backend : ≥ 70 % visé) ;
- aucun secret ni identifiant n'est dans le code ;
- la documentation est à jour (OpenAPI si un endpoint change, README si l'installation change) ;
- les textes visibles par l'utilisateur sont en français ;
- la PR est relue par Benjamin et Valère.

---

## 7. Points de validation de Rufin

| # | Point de contrôle | Moment | Ce que Rufin reçoit |
|---|---|---|---|
| 1 | **Maquettes** (CDC 2.5.4.a) | Avant tout développement front | Lien vers `demo/` + liste des écarts avec le cahier des charges |
| 2 | **Contrat d'API** (CDC 2.5.4.b) | Fin du sprint 0, avant le développement back | `openapi.yaml` + résumé lisible des endpoints |
| 3 | **Chaque mise en staging** (CDC 2.5.4.c) | Fin de chaque sprint | Démo sur staging + rapport de sprint + rapport de tests de Valère |
| 4 | **Mise en production** | Après le dernier sprint | Procès-verbal de recette de Valère + checklist de production d'Eric |
| 5 | **Article de blog** | Plan, puis brouillon | Plan détaillé, puis brouillon (CDC 9.4) |

**Temps demandé à Rufin par semaine :** environ 30 min le lundi (valider le contenu du sprint), 45 min le vendredi (démo et validation) et quelques minutes pour lancer les agents.

---

## 8. Le contrat d'API d'abord

C'est la clé pour que Codex et DeepSeek travaillent **en parallèle** sans se bloquer.

1. **Sprint 0 :** DeepSeek rédige `docs/api/openapi.yaml` à partir des sections 4 et 10 du cahier des charges.
2. Benjamin vérifie la couverture fonctionnelle, Valère la testabilité (codes d'erreur, cas limites), Eric la cohérence technique (pagination, sécurité, CORS).
3. **Rufin valide**, puis le contrat est **figé en v1.0.0**.
4. Codex développe contre un **serveur fictif** généré depuis le contrat (Prism, installé par Eric, utilisé seulement en développement et en CI). Il n'attend donc jamais le backend.
5. Les tests de contrat de Valère, exécutés en CI, vérifient que le backend respecte le contrat. Tout écart bloque la PR.
6. Toute modification passe par une PR étiquetée `contrat-api`, approuvée par Benjamin et inscrite au changelog du contrat (CDC 2.5.2).

---

## 9. Cadence

**Sprints d'une semaine.**

| Jour | Rituel | Responsable | Production |
|---|---|---|---|
| Lundi | Planification | Benjamin | Issue « Sprint N » avec le périmètre, tickets passés en `Prêt` |
| Du mardi au jeudi | Suivi asynchrone | Benjamin | Mise à jour du tableau, relance des tickets bloqués |
| Jeudi soir | Recette sur staging | Valère | Avis « prêt / pas prêt » pour la démo |
| Vendredi | Démo + clôture | Benjamin, Valère, Eric, César | `docs/rapports/sprint-NN.md` (avancement + qualité), entrée de journal |

### Feuille de route proposée (à valider)

| Sprint | Objectif | A — Codex | B — DeepSeek | C — Claude |
|---|---|---|---|---|
| **S0** | Fondations | Lecture des maquettes, squelette Nuxt | Rédaction d'`openapi.yaml`, squelette Laravel | Eric : dépôt, clones, CI. Benjamin : `AGENTS.md`, tickets. Valère : stratégie de tests. César : journal |
| **S1** | Inscription des salles | Landing, contact, onboarding | Authentification, `POST /gyms`, multi-tenant | Eric : staging, `.htaccess`. Valère : tests de contrat, E2E de l'onboarding |
| **S2** | Abonnés et plans | Pages légales | CRUD membres, plans, backoffice | Valère : tests API, isolation multi-tenant, OWASP |
| **S3** | Paiements cash, reçus, scan | Espace abonné (début) | Paiements, reçus PDF, QR, `/scan` | Valère : E2E du scan et des reçus |
| **S4** | Notifications et Mobile Money | Espace abonné (fin) | WhatsApp/SMS/Email, Orange/MTN/Wave | Eric : cron Hostinger. Valère : tests des webhooks |
| **S5** | Rapports et finitions | SEO, performance, accessibilité | Rapports, export CSV/PDF | Eric : sauvegardes. Valère : audits performance et accessibilité. César : plan de l'article |
| **S6** | Mise en production | Corrections | Corrections | Valère : recette finale. Eric : production, monitoring. César : article |

---

## 10. Règles transverses

1. **Aucun fait inventé.** Une information manquante est marquée `[À FOURNIR]`, le ticket reçoit l'étiquette `a-fournir` et Benjamin la remonte à Rufin.
2. **Aucun secret dans le dépôt.** Les clés (Orange Money, MTN, Twilio, FTP Hostinger…) vont dans les **GitHub Secrets**. Seul `.env.example` est versionné.
3. **Une décision importante = un ADR** dans `docs/adr/`, rédigé par l'entité concernée et validé par Benjamin.
4. **Langue :** français pour la documentation, les tickets, les PR et les interfaces. L'anglais est toléré dans le code.
5. **Conflit entre entités :** Benjamin tranche en s'appuyant sur le cahier des charges. Si le cahier des charges ne dit rien, c'est Rufin qui décide.
6. **Aucun push direct sur `main` ni sur `develop`.** Tout passe par une PR.
7. **Chaque entité reste dans son clone et son dossier.**

### Dépôt public : règles supplémentaires

Le dépôt `Labozeze/abidjangym` est **public**. Tout ce qui y est poussé est visible par n'importe qui, y compris l'historique des commits.

- **Avantages :** GitHub Actions est illimité et gratuit, et la protection des branches est disponible sur l'offre gratuite. C'est aussi une vitrine cohérente avec l'article de blog.
- **Interdit dans le dépôt, même pour un seul commit :** les clés d'API, mots de passe, jetons, fichiers `.env`, sauvegardes de base de données, données réelles d'abonnés ou de salles, numéros de téléphone réels. Un secret poussé par erreur est considéré comme **compromis** : il faut le révoquer, pas seulement le supprimer.
- **Garde-fous d'Eric :** détection de secrets en CI (gitleaks) et *Secret scanning / Push protection* de GitHub activés. `.gitignore` strict dès le premier commit.
- **Contributions externes :** les PR venant de l'extérieur (forks) n'ont pas accès aux GitHub Secrets et ne déclenchent aucun déploiement.
- **Données de test :** fictives uniquement (règle de Valère).

---

## 11. Risques identifiés

| Risque | Impact | Parade |
|---|---|---|
| DeepSeek harness sans accès complet au dépôt | Lenteur, Rufin doit faire le facteur | Vérifier ses capacités dès le sprint 0 (section 4) |
| Deux outils dans le même dossier | Fichiers écrasés, branches mélangées | Un clone par entité (section 4) |
| Divergence entre le front et le back | Intégration cassée | Contrat figé, serveur fictif, tests de contrat en CI |
| Une IA modifie le code d'une autre entité | Régressions, conflits | Garde-fou de périmètre en CI |
| Secret poussé dans le dépôt public | Compromission des comptes de paiement ou de l'hébergement | Push protection GitHub + gitleaks en CI, révocation immédiate si cela arrive |
| Bascule DNS vers Cloudflare mal réglée | Site injoignable ou boucle de redirection HTTPS | Bascule faite par Eric avec Rufin, chiffrement SSL en mode **Full (strict)**, test sur `staging.` avant les domaines de production |
| `git push` bloqué par le bac à sable de DSH | DeepSeek ne peut pas livrer | Test au sprint 0, jeton limité au dépôt en solution de repli |
| Code non testé ou non construit envoyé sur le serveur | Site cassé | Hostinger ne suit que les branches `deploy-*`, écrites uniquement par la CI après les tests |
| Migrations lancées avant l'arrivée du nouveau code | Erreurs en production | Backend déployé par la CI en SSH, dans un ordre garanti (pull, migrate, cache) |
| Identifiants des API de paiement indisponibles | Blocage au sprint 4 | Bacs à sable (sandbox) et serveurs fictifs, demande des accès dès le sprint 1 |
| Article de blog sans matière | Faits inventés ou article vague | César tient un journal de bord dès le sprint 0 |

---

## 12. Informations à fournir par Rufin

- [x] Agent séparé pour les tests : **Valère**
- [x] DeepSeek : **DeepSeek harness**
- [x] Codex : **version desktop**
- [x] Organisation GitHub : **`Labozeze` existe**
- [x] Capacités de DeepSeek harness : testées (section 4). Fichiers uniquement dans son espace de travail, `AGENTS.md` lu, `git` disponible, pas de `gh`
- [x] Dépôt `Labozeze/abidjangym` : **public** (règles en section 10)
- [x] Organisation GitHub : **`Labozeze`** (et non `lelabodeze`). Dépôt : `Labozeze/abidjangym`, compte administrateur `yesdrivemedev`. *Proposition de Benjamin : corriger le §7.3 du cahier des charges (v2.1) après validation de Rufin*
- [x] Cloudflare : **compte créé**. Il servira de CDN et de pare-feu devant Hostinger, et R2 recevra la copie hebdomadaire des sauvegardes (CDC §3.2, §5.3, §6.7)
- [ ] **[À FOURNIR]** Où le domaine `abidjangym.com` est-il enregistré (Hostinger ou autre registraire) ? Il faudra y remplacer les serveurs DNS par ceux de Cloudflare
- [ ] **[À FOURNIR]** Pour R2 (sauvegardes) : Cloudflare demande d'enregistrer un moyen de paiement, même pour l'offre gratuite. C'est à faire par Rufin, au sprint 5
- [x] Espace de travail de DeepSeek harness : `C:\Users\HP\Documents\deepseek-harness\default-workspace`
- [x] GitHub CLI installé et connecté (compte `yesdrivemedev`, HTTPS)
- [x] Autorisation `project` ajoutée à gh (vérifié le 30/10/2026)
- [x] Hostinger : **accès SSH disponible**. Déploiement par Git Hostinger relié aux branches `deploy-*` (section 5)
- [ ] **[À FOURNIR]** Le moment venu, Rufin ajoute la clé de déploiement dans l'hPanel et la clé SSH dans les GitHub Secrets (Eric fournira la marche à suivre)
- [x] **Plan, feuille de route et quatre fiches de rôle validés par Rufin le 30/10/2026**

---

*Sprint 0 (à partir du 31/10/2026) : Eric crée le dépôt, les clones, le tableau, les modèles et la CI de base. Benjamin rédige `AGENTS.md`, les briefs `frontend/AGENTS.md` et `backend/AGENTS.md`, puis les tickets du sprint 0. Valère rédige la stratégie de tests. César ouvre le journal de bord.*

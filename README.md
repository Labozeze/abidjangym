# AbidjanGym

Plateforme SaaS de gestion de salles de sport pour la Côte d'Ivoire et l'Afrique francophone : abonnés, abonnements, paiements Mobile Money, contrôle d'accès par QR code, notifications WhatsApp/SMS/Email.

Ce projet est construit par **trois entités IA** qui collaborent uniquement à travers ce dépôt :

| Entité | IA | Dossier |
|---|---|---|
| A — Frontend | Codex | `frontend/` |
| B — Backend | DeepSeek | `backend/` |
| C — Pilotage, DevOps, Tests, Rédaction | Claude | `.github/`, `ops/`, `tests/`, `docs/`, `blog/` |

## Par où commencer
- **Agents IA :** lire [`AGENTS.md`](AGENTS.md), puis le `AGENTS.md` de votre dossier.
- **Cahier des charges :** [`livrables/cahier-des-charges.md`](livrables/cahier-des-charges.md)
- **Fonctionnement du projet :** [`docs/gouvernance/plan-de-collaboration.md`](docs/gouvernance/plan-de-collaboration.md)
- **Maquettes de référence :** [`demo/`](demo/)

## Branches
- `develop` : intégration (branche par défaut), déployée sur staging
- `main` : production
- `feature/<entité>-<n° ticket>-<sujet>` : travail en cours, par exemple `feature/b-12-auth`

Projet piloté par Rufin Ismael Baroan — Le Labo de Zézé.

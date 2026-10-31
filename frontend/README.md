Frontend AbidjanGym — Entité A, Codex.

## Prérequis

- Node.js 20.19+ ou 22.12+
- npm

## Installation

Depuis le dossier `frontend/` :

```sh
npm ci
```

Copiez `.env.example` vers `.env` pour configurer l’URL de l’API :

```sh
cp .env.example .env
```

`NUXT_PUBLIC_API_BASE` est l’URL de base de l’API publique. Nuxt l’expose dans `runtimeConfig.public.apiBase`.

## Commandes

À lancer depuis `frontend/` :

```sh
npm run dev       # serveur de développement local
npm run generate  # génération statique dans .output/public
npm run lint      # ESLint et vérification Prettier
npm test          # tests Vitest, lancés une fois
```

Le dossier `.output/public/` contient les fichiers statiques à déployer. Aucun serveur Node.js n’est nécessaire en production.

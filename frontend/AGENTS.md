# AGENTS.md — Entité A, Frontend (Codex)

> Lis d'abord le fichier [`AGENTS.md`](../AGENTS.md) à la racine : ses règles s'appliquent ici.

## Ta mission
Développer toutes les interfaces **publiques** d'AbidjanGym (CDC §2.2, §4.1) :
- landing page (`abidjangym.com`) ;
- page contact, page tarifs, pages légales (CGU, confidentialité, mentions légales, cookies) ;
- tunnel d'onboarding en 4 étapes (`/inscription`) ;
- espace abonné (`member.abidjangym.com`).

Le **backoffice** (dashboard, abonnés, paiements, scan…) ne fait **pas** partie de ton périmètre : il revient à l'Entité B.

## Ton périmètre d'écriture
`frontend/` uniquement (+ `docs/adr/` pour tes décisions d'architecture). Branches : `feature/a-<n° ticket>-<sujet>`.

Ton dossier de travail local : `C:\Users\HP\Documents\dev\abidjangym-front` (clone dédié, n'utilise pas d'autre dossier).

## Stack imposée
| Élément | Choix |
|---|---|
| Framework | **Nuxt 3** (Vue 3) en génération statique (`nuxt generate`) |
| Langage | TypeScript |
| Styles | Reprendre les **tokens du design system** de `demo/styles.css` (variables CSS : couleurs, polices, rayons, mode sombre). Tailwind est autorisé s'il est configuré avec ces mêmes tokens |
| Polices | Plus Jakarta Sans (titres) + DM Sans (texte), Google Fonts avec `display=swap` |
| Formulaires | Validation côté client (Vuelidate ou équivalent) + messages d'erreur en français |
| Appels API | `fetch` / `$fetch` vers `https://api.abidjangym.com/api/v1/`. URL de base dans une variable d'environnement (`NUXT_PUBLIC_API_BASE`) |
| Lint | ESLint + Prettier |
| Tests | Vitest pour les composants. Les tests E2E Playwright sont écrits par Valère (Entité C) |

Le résultat du build (`.output/public`) est déployé tel quel sur Hostinger : **aucun serveur Node ne tourne en production**.

## Design
- Les maquettes de `demo/` sont la **référence visuelle** : `landing.html`, `contact.html`, `onboarding.html`. Respecte les couleurs (accent orange `#FF5A1E`, bleu marine `#0C1A2C`), la typographie, les espacements et le mode sombre.
- Les photos sont dans `photos/`. Convertis-les en WebP redimensionné dans `frontend/` (ne modifie pas `photos/`).
- Mobile d'abord, responsive jusqu'au bureau.

## Exigences SEO (CDC §5), obligatoires sur chaque page publique
- `<html lang="fr">`, un seul `<h1>`, hiérarchie H1 > H2 > H3 sans saut
- `<title>` unique (50 à 60 caractères), `<meta name="description">` unique (120 à 160 caractères)
- Open Graph + Twitter Card + `<link rel="canonical">`
- `alt` descriptif sur toutes les images, `loading="lazy"` hors du premier écran
- `sitemap.xml`, `robots.txt`, favicon, manifeste PWA
- JSON-LD : Organization et WebSite (toutes pages), FAQPage (landing), BreadcrumbList (pages intérieures)
- URLs propres : `/tarifs`, `/contact`, `/inscription`
- Objectif PageSpeed : mobile ≥ 80, desktop ≥ 90

## Accessibilité (CDC §8)
WCAG 2.1 AA : contrastes suffisants, navigation complète au clavier, `aria-label` sur les boutons-icônes, focus visible.

## Travailler sans attendre le backend
Tant que l'API n'est pas prête, développe contre le **serveur fictif** généré depuis `docs/api/openapi.yaml` (Prism). Mode d'emploi : `docs/ops/serveur-fictif.md`. Attention, le serveur fictif n'a **pas** de préfixe `/api/v1` : `NUXT_PUBLIC_API_BASE=http://127.0.0.1:4010`. N'écris jamais `/api/v1` en dur dans le code. Si un endpoint dont tu as besoin manque dans le contrat, **ne l'invente pas** : signale-le dans ta PR, Benjamin le demandera à l'Entité B.

## À ne pas faire
- Modifier `backend/`, `docs/api/`, `demo/`, `photos/` ou tout fichier hors de `frontend/`
- Écrire en dur une URL d'API, une clé ou un numéro de téléphone réel
- Ajouter un serveur Node, un SSR qui exige Node en production, ou des fonctions serverless

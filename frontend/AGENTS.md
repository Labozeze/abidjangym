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
| Styles | Reprendre les styles de la maquette de chaque page (`demo/landing.html`, `demo/contact.html`, `demo/onboarding.html`). `demo/styles.css` est le design system du **backoffice** : il ne s'applique pas au site public. Tailwind est autorisé s'il reproduit exactement ces styles |
| Thème | **Clair uniquement**, comme les maquettes publiques. Aucune règle `prefers-color-scheme: dark`. `color-scheme: only light` et `<meta name="color-scheme" content="only light">` obligatoires (#56) |
| Polices | Plus Jakarta Sans (titres) + DM Sans (texte), Google Fonts avec `display=swap` |
| Formulaires | Validation côté client (Vuelidate ou équivalent) + messages d'erreur en français |
| Appels API | `fetch` / `$fetch` vers `https://api.abidjangym.com/api/v1/`. URL de base dans une variable d'environnement (`NUXT_PUBLIC_API_BASE`) |
| Lint | ESLint + Prettier |
| Tests | Vitest pour les composants. Les tests E2E Playwright sont écrits par Valère (Entité C) |

Le résultat du build (`.output/public`) est déployé tel quel sur Hostinger : **aucun serveur Node ne tourne en production**.

## Design : reproduire, ne pas réinterpréter
- **Les maquettes de `demo/` sont le design final validé par Rufin** (`landing.html`, `contact.html`, `onboarding.html`). Elles se **reproduisent à l'identique** : même structure, mêmes classes et styles (repris de la maquette, pas réécrits), **mêmes textes, mêmes photos, mêmes animations**, et le **même thème clair** : le site doit rester clair même sur un téléphone réglé en mode sombre, accès principal des visiteurs (#56).
- Tu ne réécris pas les titres ni les textes, tu ne changes pas les photos et tu ne supprimes pas d'animation « pour simplifier ». La landing livrée en #36 a été refusée pour cette raison (voir #51).
- **Seuls écarts autorisés :** ceux qu'imposent le ticket, le CDC ou une décision de Rufin (ex. 2 mois d'essai au lieu de 14 jours, aucun chiffre ni témoignage inventé #7, liens réels, accessibilité). **Chaque écart est listé dans la PR**, avec sa justification.
- **Preuve obligatoire dans chaque PR d'écran :** captures côte à côte maquette / Nuxt, à 1280 px et à 375 px, section par section.
- Les photos sont dans `photos/`. Convertis-les en WebP redimensionné dans `frontend/`, avec le **même cadrage** que la maquette (ne modifie pas `photos/`).
- Animations : conserver celles de la maquette, et les désactiver uniquement si l'utilisateur a activé `prefers-reduced-motion`.
- Mobile d'abord, responsive jusqu'au bureau, comme la maquette.

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

# Contrat d'API AbidjanGym — v0.1

**Statut : brouillon soumis à la validation de Rufin** (CDC §2.5.4.b, point de contrôle 2).
Aucune implémentation backend ne démarre avant cette validation.

| Élément | Valeur |
|---|---|
| Fichier de référence | [`openapi.yaml`](openapi.yaml) — OpenAPI 3.0.3 |
| Version du contrat | 0.1.0 |
| Périmètre | `docs/api/` (Entité B) |
| Ticket | #9 — Contrat d'API v0.1 |
| Références | CDC §4.1.4, §4.1.5, §4.2, §6.2, §6.3, §6.4, §7.2, §10 |

Ce README est la **synthèse lisible** du contrat. En cas de divergence, `openapi.yaml` fait foi.

---

## 1. Ce que couvre le contrat

**62 opérations** réparties en onze domaines. Tous les flux du CDC §10 sont réalisables
(voir le tableau de correspondance en §4).

| Domaine | Opérations | Contenu |
|---|---:|---|
| Santé | 1 | `GET /health` pour le déploiement et le monitoring |
| Authentification | 6 | connexion, rafraîchissement, déconnexion, mot de passe oublié, réinitialisation, utilisateur courant |
| Salles | 6 | onboarding `POST /gyms`, profil de la salle, logo, bannières |
| Plans | 6 | CRUD complet, archivage, réordonnancement |
| Abonnés | 14 | CRUD, recherche, filtres, export CSV, QR code (téléchargement, régénération, renvoi), historiques |
| Abonnements | 6 | création, détail, renouvellement, suspension, réactivation |
| Paiements | 11 | enregistrement, liste avec totaux, reçu PDF, lien 72 h, justificatif, confirmation, annulation, callbacks Mobile Money |
| Contrôle d'accès | 2 | scan `GET /access/{uuid}`, historique des entrées |
| Tableau de bord | 1 | les huit widgets du CDC §4.2.2 en un appel |
| Espace abonné | 8 | lien magique, profil, abonnement, historiques, QR code |
| Contact | 1 | formulaire public de `abidjangym.com/contact` |

---

## 2. Conventions communes

**Base URL :** `https://api.abidjangym.com/api/v1` (CDC §7.2).

**Authentification** — deux mécanismes, décrits dans le contrat :

| Schéma | Usage | Transport |
|---|---|---|
| `SessionCookie` | backoffice gérant / caissier | cookie `ag_session`, `httpOnly`, `Secure`, `SameSite=Lax` |
| `BearerAuth` | API, clients tiers, intégrations | `Authorization: Bearer <jeton>` |
| `MemberBearer` | espace abonné | `Authorization: Bearer <jeton>` (portée limitée au membre) |

Jeton d'accès **1 h**, jeton de rafraîchissement **30 jours** (CDC §6.2). Les routes publiques
sont explicitement marquées `security: []` : santé, connexion, rafraîchissement, mot de passe
oublié, réinitialisation, onboarding, callbacks de paiement, lien magique, contact.

**Multi-tenant (CDC §6.3).** Le tenant est déduit du jeton, jamais d'un paramètre. Toute
ressource d'une autre salle répond **`404`** et non `403` : un gérant ne doit pas pouvoir
déduire l'existence d'un abonné d'une autre salle.

**Montants.** Entiers en francs CFA, suffixe `_xof`, jamais de décimales (CDC §4.2.4).

**Dates.** ISO 8601 avec fuseau ; fuseau par défaut `Africa/Abidjan` (GMT+0). Les dates
« métier » (début/fin d'abonnement) sont des `date` sans heure.

**Pagination.** `?page=&per_page=` (défaut 20, maximum 100), enveloppe
`{ data, meta, links }`. Le CDC §7.2 évoque du *cursor-based* pour les très grandes listes :
l'offset est retenu en v0.1, à revoir si les volumes l'exigent.

**Erreurs.** Format unique `{ "message": "...", "errors": {} }`, messages en français.
Codes employés : `400`, `401`, `403`, `404`, `409`, `422`, `429`, `500`, `503`.

**Statuts calculés.** Les états d'abonné du CDC (Actif, Expire bientôt, Expiré, Suspendu,
Nouveau) sont exposés en enum anglais `active`, `expiring_soon`, `expired`, `suspended`,
`new` — la correspondance française est dans la description du schéma.

---

## 3. Endpoints, domaine par domaine

### Santé et authentification

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/health` | état de l'API et de la base (public) |
| POST | `/auth/login` | connexion backoffice, pose le cookie, renvoie le jeton |
| POST | `/auth/refresh` | nouveau jeton d'accès |
| POST | `/auth/logout` | révocation du jeton (liste noire, CDC §6.2) |
| POST | `/auth/forgot-password` | envoi d'un lien valable 1 h |
| POST | `/auth/reset-password` | nouveau mot de passe (8 car., 1 maj., 1 chiffre) |
| GET | `/auth/me` | utilisateur, rôle et salle courants |

### Salles

| Méthode | Route | Rôle |
|---|---|---|
| POST | `/gyms` | onboarding complet : salle + admin + plan SaaS (public) |
| GET | `/gyms/current` | fiche complète de la salle |
| PATCH | `/gyms/current` | modification partielle du profil |
| POST | `/gyms/current/logo` | logo PNG/JPG, 2 Mo max, 300×300 |
| POST | `/gyms/current/banners` | bannière (3 maximum) |
| DELETE | `/gyms/current/banners/{bannerId}` | suppression d'une bannière |

### Plans

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/plans` | liste triée par ordre d'affichage |
| POST | `/plans` | création (mensuel, hebdomadaire, journalier, pack) |
| GET | `/plans/{planId}` | détail |
| PATCH | `/plans/{planId}` | modification (sans effet rétroactif sur les paiements) |
| DELETE | `/plans/{planId}` | archivage logique |
| PUT | `/plans/order` | ordre issu du glisser-déposer |

### Abonnés

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/members` | liste, recherche plein texte, filtres état/plan/commune/expiration, tris |
| POST | `/members` | création : numéro `AAAA-XXXXX` et QR générés |
| GET | `/members/export` | export CSV de la liste filtrée |
| GET | `/members/{memberId}` | fiche détaillée + compteurs |
| PATCH | `/members/{memberId}` | modification du profil |
| DELETE | `/members/{memberId}` | archivage logique |
| POST | `/members/{memberId}/suspend` | suspension manuelle |
| POST | `/members/{memberId}/reactivate` | levée de suspension |
| GET | `/members/{memberId}/subscriptions` | historique des abonnements |
| GET | `/members/{memberId}/payments` | historique des paiements |
| GET | `/members/{memberId}/entries` | historique des entrées |
| GET | `/members/{memberId}/qr` | QR PNG (800×800 par défaut) ou SVG |
| POST | `/members/{memberId}/qr/regenerate` | nouveau QR, ancien UUID invalidé |
| POST | `/members/{memberId}/qr/send` | renvoi WhatsApp ou email |

### Abonnements

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/subscriptions` | liste de la salle |
| POST | `/subscriptions` | souscription d'un plan, calcul des dates de validité |
| GET | `/subscriptions/{subscriptionId}` | détail |
| POST | `/subscriptions/{subscriptionId}/renew` | renouvellement (changement de plan possible) |
| POST | `/subscriptions/{subscriptionId}/suspend` | suspension administrative |
| POST | `/subscriptions/{subscriptionId}/reactivate` | réactivation |

### Paiements

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/payments` | liste filtrable + totaux par mode sur la période |
| POST | `/payments` | enregistrement (espèces, Mobile Money, virement, chèque) |
| GET | `/payments/export` | export CSV comptable |
| POST | `/payments/callback/{provider}` | callback prestataire : `orange`, `mtn`, `wave`, `cinetpay` (public, signé) |
| GET | `/payments/{paymentId}` | détail |
| PATCH | `/payments/{paymentId}` | correction des notes, référence, date |
| POST | `/payments/{paymentId}/confirm` | confirmation d'un virement ou chèque |
| POST | `/payments/{paymentId}/cancel` | annulation motivée |
| POST | `/payments/{paymentId}/proof` | justificatif, 5 Mo max |
| GET | `/payments/{paymentId}/receipt` | reçu PDF |
| POST | `/payments/{paymentId}/receipt/link` | lien de partage valable 72 h |

### Contrôle d'accès et tableau de bord

| Méthode | Route | Rôle |
|---|---|---|
| GET | `/access/{uuid}` | validation du scan : accès accordé + enregistrement, ou refus motivé |
| GET | `/entries` | historique des entrées (période, abonné, caissier) |
| GET | `/dashboard` | abonnés actifs, revenus du jour, entrées, expirations, 2 graphiques, 5 derniers paiements, alertes |

### Espace abonné (`member.abidjangym.com`)

| Méthode | Route | Rôle |
|---|---|---|
| POST | `/member/auth/magic-link` | demande de lien magique (public) |
| POST | `/member/auth/magic-link/verify` | ouverture de session (public) |
| GET | `/member/profile` | profil en lecture seule |
| GET | `/member/subscription` | abonnement en cours, jours restants |
| GET | `/member/subscriptions` | historique |
| GET | `/member/payments` | historique + reçus |
| GET | `/member/entries` | historique des passages |
| GET | `/member/qr` | QR code en grand / téléchargement |

### Contact

| Méthode | Route | Rôle |
|---|---|---|
| POST | `/contact-messages` | formulaire public (prénom, nom, email, téléphone, objet, message) |

---

## 4. Couverture des flux du CDC §10

| Flux | Réalisable via |
|---|---|
| §10.1 Inscription d'une salle | `POST /gyms` → `GET /gyms/current` → `POST /plans` |
| §10.2 Inscription d'un abonné | `POST /members` → `POST /subscriptions` → `POST /payments` → `GET /payments/{id}/receipt` |
| §10.3 Renouvellement | `POST /subscriptions/{id}/renew` → `POST /payments` |
| §10.4 Contrôle d'accès QR | `GET /access/{uuid}` → passage enregistré, visible via `GET /entries` |
| §10.5 Paiement Mobile Money | `POST /payments` (initiation) → `POST /payments/callback/{provider}` → `GET /payments/{id}/receipt` |

---

## 5. Validation du fichier

Le contrat est vérifié avec [Redocly CLI](https://redocly.com/docs/cli) (aucun compte requis) :

```bash
npx @redocly/cli lint docs/api/openapi.yaml
```

Résultat sur cette version : **« Your API description is valid. »** — 0 erreur, 6 avertissements
assumés et expliqués :

| Avertissement | Pourquoi il est accepté |
|---|---|
| `no-server-example.com` (serveur `localhost`) | le serveur de développement est décrit volontairement, pour l'Entité A et le front |
| `no-ambiguous-paths` ×4 | `POST /payments/callback/{provider}` (URL imposée par le CDC §10.5) peut théoriquement être confondu avec `/payments/{paymentId}/…` par un générateur de SDK. À trancher : conserver l'URL du CDC, ou renommer en `/payment-callbacks/{provider}` |
| `operation-4xx-response` sur `/health` | un endpoint de supervision ne renvoie pas d'erreur 4xx par nature |

Le fichier se visualise aussi dans Swagger Editor (`editor.swagger.io`) ou avec :

```bash
npx @redocly/cli preview-docs docs/api/openapi.yaml
```

---

## 6. Points à trancher par Rufin

Ces points ne sont pas des oublis : le CDC est soit silencieux, soit contradictoire, et la
règle « aucun fait inventé » impose de les remonter plutôt que de choisir seul.

1. **JWT ou Sanctum ?** Le CDC §6.2 décrit des jetons JWT (accès 1 h + rafraîchissement 30 j) ;
   `backend/AGENTS.md` impose Laravel Sanctum. Le contrat décrit le comportement attendu
   (jeton en cookie httpOnly + Bearer), pas la technique. À confirmer : Sanctum avec jetons
   opaques, ou JWT signés ?
2. **Durée de session.** CDC §4.2.1 : « 8 h » ; CDC §6.2 : accès 1 h + rafraîchissement 30 j.
   Proposition retenue : jeton d'accès 1 h rafraîchi tant qu'il y a de l'activité, session
   plafonnée à 8 h sauf case « se souvenir de cet appareil ».
3. **Durée du lien magique abonné.** Non spécifiée. Proposition : 15 minutes.
4. **Pagination.** Offset (proposé) ou cursor-based comme suggéré au CDC §7.2 ?
5. **`per_page` maximum.** Fixé à 100 ; à ajuster selon les limites Hostinger.
6. **Reçu personnalisable.** QR code optionnel et image de signature/cachet (CDC §4.2.8) :
   qui les active — le gérant dans les paramètres, ou l'équipe AbidjanGym ?
7. **Nom du chemin de callback.** Voir l'avertissement `no-ambiguous-paths` ci-dessus.

---

## 7. Valeurs marquées `[À FOURNIR]`

Aucune valeur réelle n'a été inventée (AGENTS.md §5.3). À fournir par Rufin ou Eric :

| Élément | Emplacement |
|---|---|
| En-tête et algorithme de signature des callbacks Orange Money, MTN MoMo, Wave, CinetPay | `POST /payments/callback/{provider}` |
| Charge utile exacte de chaque prestataire | schéma du corps du callback |
| Identifiants API des prestataires (chiffrés AES-256 en base, CDC §6.6) | configuration salle, hors contrat |
| SMTP sortant pour les emails transactionnels et le formulaire de contact | `POST /contact-messages`, `POST /auth/forgot-password` |
| Fournisseur WhatsApp Business et SMS (Twilio, 360dialog, Infobip…) | notifications, hors v0.1 |
| Adresse email de contact technique | `info.contact.email` |
| Profondeur d'historique conservée pour les graphiques du tableau de bord | `GET /dashboard` |

---

## 8. Hors périmètre de la v0.1

Ces éléments du CDC existent mais ne sont pas contractualisés ici ; ils relèvent d'une v0.2,
dès que les flux principaux sont validés :

- configuration et journal des notifications (CDC §4.2.7) ;
- rapports et analyses avec export PDF/CSV (CDC §4.2.11) ;
- back-office **Super Admin** de la plateforme (CDC §4.2.10 : salles, MRR, plans SaaS) ;
- webhook domotique après scan (CDC §4.2.9) ;
- actions groupées sur les abonnés (notification, suspension, archivage en masse) ;
- régénération du QR par l'abonné lui-même.

---

## 9. Historique

| Version | Date | Auteur | Changements |
|---|---|---|---|
| 0.1.0 | 31 oct. 2026 | Entité B (DeepSeek), ticket #9 | Version initiale : 62 opérations, schémas, sécurité, erreurs, pagination |

# Stratégie de tests — AbidjanGym

**Rédigée par :** Valère, Tests et assurance qualité (Entité C)
**Version :** 1.0, sprint 0 (31 octobre 2026)
**Référence :** CDC v2.1 §2.4, §5.3, §6, §7.3, §8, et [fiche de rôle de Valère](../gouvernance/roles/valere-tests-qa.md)

---

## 1. Objectif

Prouver, à chaque livraison, que le produit fait ce que demande le cahier des charges, **sans régression**, **sans faille** et **sans fuite de données entre salles**. Rufin ne doit découvrir aucun bug pendant les démos du vendredi.

## 2. La pyramide de tests

| Niveau | Quoi | Outil | Écrit par | Exécuté |
|---|---|---|---|---|
| **Unitaires backend** | Règles métier : calcul des dates d'abonnement, états d'un abonné, packs de séances, numérotation des reçus… | PHPUnit | DeepSeek (B), relus par Valère | Chaque push (CI) |
| **Unitaires frontend** | Composants, validation des formulaires | Vitest | Codex (A), relus par Valère | Chaque push (CI) |
| **Contrat d'API** | Le backend respecte `docs/api/openapi.yaml` : routes, schémas, codes d'erreur | Schemathesis (à confirmer au S1) | Valère | Chaque PR backend |
| **API / intégration** | Parcours métier par l'API, cas d'erreur 400/401/403/404/422, **isolation multi-tenant** | Postman + Newman | Valère | Chaque PR + staging |
| **E2E** | Parcours utilisateur réels dans le navigateur | Playwright | Valère | Staging (puis CI quand le front sera stable) |
| **Sécurité** | Scan passif, en-têtes HTTP, limitation des tentatives de connexion, CSRF | OWASP ZAP | Valère | Chaque déploiement sur staging |
| **Performance et accessibilité** | PageSpeed, Core Web Vitals, WCAG 2.1 AA | Lighthouse CI, axe | Valère | Sprints 5 et 6, puis chaque mise en production |

Règle : **plus un test est bas dans la pyramide, plus il est nombreux et rapide.** Les E2E couvrent les parcours critiques, pas chaque détail.

## 3. Parcours critiques (E2E Playwright)

| # | Parcours | Référence CDC | Sprint visé |
|---|---|---|---|
| E1 | Landing : affichage, CTA « J'inscris ma salle », FAQ, balises SEO | §4.1.1, §5 | S1 |
| E2 | Contact : formulaire valide et invalide | §4.1.2 | S1 |
| E3 | Onboarding : 4 étapes, puis création de la salle, puis redirection | §4.1.4, §10.1 | S1 |
| E4 | Connexion backoffice, mot de passe oublié, déconnexion | §4.2.1 | S1–S2 |
| E5 | Création d'un abonné, abonnement et paiement cash, puis reçu | §10.2 | S2–S3 |
| E6 | Scan QR : accès autorisé (fond vert) et refusé (expiré, suspendu, invalide) | §4.2.9, §10.4 | S3 |
| E7 | Renouvellement d'abonnement | §10.3 | S3 |
| E8 | Espace abonné : profil, QR, historique | §4.1.5 | S4 |

## 4. Tests transverses systématiques

| Sujet | Règle |
|---|---|
| **Isolation multi-tenant** (§6.3) | Pour **chaque** ressource (abonnés, plans, paiements, entrées, notifications…), un test prouve qu'un utilisateur de la salle A reçoit `403` ou `404` sur une ressource de la salle B, en lecture **et** en écriture |
| **Rôles** (§4.2.10) | Un caissier n'accède ni aux rapports, ni aux paramètres, ni aux suppressions |
| **Authentification** (§6.2) | Blocage après 5 échecs, jeton révoqué à la déconnexion, mot de passe conforme (8 caractères, 1 majuscule, 1 chiffre) |
| **Validation** | Chaque champ obligatoire du CDC est testé vide et invalide |
| **Montants** | Entiers en FCFA, jamais de décimales |
| **Dates** | Fuseau `Africa/Abidjan`. Abonnement journalier valable jusqu'à 23h59, mensuel 30 jours, hebdomadaire 7 jours |

## 5. Données de test

- **Fictives uniquement** : le dépôt est public. Téléphones au format `+225 07 00 00 00 XX`, emails `@example.com`.
- Deux salles de test au minimum (« FitZone Test » et « Salle Témoin ») pour tous les tests d'isolation.
- Jeux de données dans `tests/fixtures/` (Valère) et *seeders* Laravel (DeepSeek).

## 6. Critères de passage

| Moment | Critère |
|---|---|
| **Merge d'une PR** | CI verte, critères d'acceptation couverts par des tests, couverture qui ne baisse pas, relecture de Benjamin et de Valère |
| **Démo du vendredi** | Recette du jeudi soir : avis « prêt » de Valère, zéro bug bloquant ou majeur ouvert sur le périmètre du sprint |
| **Mise en production** | Tous les parcours E1 à E8 au vert sur staging, zéro alerte OWASP élevée, PageSpeed mobile ≥ 80 et desktop ≥ 90, procès-verbal de recette finale signé |

## 7. Gravité des anomalies

| Gravité | Définition | Délai |
|---|---|---|
| **Bloquant** (`P0`) | Fuite de données entre salles, faille de sécurité, parcours critique impossible, perte de données | Immédiat : bloque la démo et la production |
| **Majeur** (`P1`) | Fonctionnalité du CDC incorrecte, mais avec un contournement possible | Dans le sprint |
| **Mineur** (`P2`) | Défaut d'affichage, libellé, confort | Planifié par Benjamin |

## 8. Mise en place par sprint

| Sprint | Livrables de Valère |
|---|---|
| S0 | Cette stratégie |
| S1 | `tests/fixtures/`, tests de contrat, E1 à E3, premiers tests d'isolation |
| S2 | Collection Newman complète (auth, abonnés, plans), isolation multi-tenant, premier scan OWASP |
| S3 | E5 à E7, tests des reçus et du scan |
| S4 | Tests des webhooks de paiement et des notifications (drivers fictifs), E8 |
| S5 | Audits performance et accessibilité |
| S6 | Recette finale et procès-verbal |

## 9. Points ouverts

- **Outil de tests de contrat :** Schemathesis (Python) est pressenti. La décision sera prise au S1 avec Eric, selon le temps d'exécution en CI.
- **E2E en CI :** les tests Playwright tourneront d'abord contre staging. Leur exécution sur chaque PR sera étudiée quand le front sera stable.

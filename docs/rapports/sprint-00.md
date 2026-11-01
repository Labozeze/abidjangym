# Rapport du sprint 0 — Fondations

**Période :** 31 octobre → 1er novembre 2026 (prévu jusqu'au 6 novembre)
**Rédigé par :** Benjamin (avancement) et Valère (qualité), Entité C
**Ticket de suivi :** #1

---

## 1. En bref

Le sprint 0 est **terminé avec 4 jours d'avance**. Les **deux points de contrôle** prévus sont validés par Rufin. Les trois entités savent livrer par le dépôt, et les squelettes du front et du back sont en place, testés et surveillés par la CI.

| Indicateur | Valeur |
|---|---|
| Tickets terminés | **16** (#2 à #12, #18, #23, #27, #29, #30) |
| PR mergées | **15** : Entité A 4, Entité B 6, Entité C 5 |
| Points de contrôle validés | **2 / 2** : maquettes (#7), contrat d'API (#9) |
| Version du contrat d'API | **v0.3.0**, 68 opérations |
| Version du cahier des charges | **v2.3** (3 évolutions validées par Rufin) |

## 2. Ce qui a été livré

| Entité | Livraisons |
|---|---|
| **A — Codex** | Test de la chaîne (#5), audit des maquettes (#7), squelette Nuxt 3 statique (#8), FAQ de 12 questions (#18) |
| **B — DeepSeek** | Test de la chaîne (#6), contrat d'API v0.2 puis v0.3 (#9, #23), squelette Laravel avec socle multi-tenant (#10), synchronisation du lock (#27), migration vers Laravel 13 (#30) |
| **C — Claude** | Dépôt, protections, tableau, CI et PR automatiques (#2), briefs `AGENTS.md` (#3), stratégie de tests (#4), CI du front et du back (#11), serveur fictif Prism (#12), cahier des charges v2.1 → v2.3, journal de bord |

## 3. Décisions de Rufin pendant le sprint

| Date | Décision | Trace |
|---|---|---|
| 31/10 | Maquettes validées avec 5 décisions (essai de 2 mois, aucun chiffre ni témoignage inventé, page Tarifs, FAQ, maquette de l'espace abonné au sprint 3) | #7 |
| 31/10 | Merger seulement après la relecture de Benjamin et Valère | journal |
| 01/11 | 5 arbitrages techniques du contrat (Sanctum, session 1 h / 8 h, lien magique 15 min, pagination, `per_page` 100) | PR #20 |
| 01/11 | Contrat d'API v0.2.0 validé | PR #20 |
| 01/11 | Moyens de paiement activables (on/off), espèces seules activées par défaut | #23, CDC v2.2 |
| 01/11 | Passage à **Laravel 13 / PHP 8.3** (failles de Laravel 11) | #30, CDC v2.3 |

## 4. Qualité (Valère)

### Méthode
Chaque PR de code est vérifiée **en exécutant** ses commandes sur un **clone propre** de la branche, sans se fier à la description de la PR.

### Ce que les relectures ont détecté avant le merge
| PR | Problème | Origine | Corrigé |
|---|---|---|---|
| #17 | L'audit renvoyait à Rufin des décisions déjà tranchées par le CDC §1.3 | Entité A | ✅ |
| #20 | Gestion des caissiers absente, limitation des tentatives manquante sur 3 routes, caissier autorisé à tort à modifier un paiement | Entité B | ✅ |
| #22 | FAQ écrite pour l'équipe projet au lieu des clients | Entité A | ✅ |
| #25 | 1 test sur 13 en échec sur un clone propre | **Entité C** (`.gitignore` d'Eric) | ✅ PR #26 |
| #25 | Une donnée pouvait être déplacée vers une autre salle lors d'une modification | Entité B | ✅ |
| #25 | `composer.lock` désynchronisé | Entité B | ✅ #27 (après un merge prématuré) |
| #28 | **5 failles dans Laravel 11, dont une critique**, signalées par DeepSeek | Dépendance | ✅ Laravel 13 (#30) |

**4 livraisons sur 10** des entités A et B ont demandé au moins une correction. Aucune anomalie connue n'a atteint `develop` sans être corrigée ensuite.

### État technique en fin de sprint
| Contrôle | État |
|---|---|
| Tests backend | 15/15, dont 8 d'isolation multi-tenant |
| Tests frontend | 1/1 (squelette) |
| `composer audit` | 0 faille |
| CI obligatoire sur `develop` et `main` | `secrets`, `perimetre`, `frontend`, `backend` (moins de 30 s par job) |

### Poste de travail (hors dépôt)
PHP 8.3.28 installé, certificats configurés dans `php.ini`, Composer 2.11.1 avec TLS réactivé (il était désactivé, et Composer 2.8.8 avait 4 failles graves).

## 5. Ce qui a bien fonctionné
- **La chaîne de livraison par le dépôt** : les trois IA livrent sans copier-coller, et la PR s'ouvre toute seule.
- **Le contrôle croisé** : une erreur de l'Entité C elle-même a été attrapée par la relecture.
- **Les remontées des entités** : DeepSeek a signalé les failles au lieu de les corriger hors de son périmètre.

## 6. À améliorer au sprint 1
| Constat | Action |
|---|---|
| Une PR mergée avant le traitement du dernier commentaire (#25) | Ne merger qu'après la mention « Prêt pour le merge par Rufin » |
| Une branche partie d'un `develop` ancien a provoqué un conflit (#32) | Rappel dans les tickets : `git pull origin develop` avant de pousser |
| Ticket ouvert en doublon par une entité (#29) | Règle ajoutée à `AGENTS.md` : signaler dans la PR, ne pas ouvrir de ticket soi-même |
| Descriptions de PR parfois hors modèle (#22) | Rappel du modèle d'`AGENTS.md` §4 |

## 7. Informations attendues de Rufin
Voir #13. Pour le sprint 1, l'accès Hostinger pour le staging (sous-domaine, PHP 8.3, clé de déploiement) et le registraire du domaine (bascule Cloudflare) deviennent nécessaires.

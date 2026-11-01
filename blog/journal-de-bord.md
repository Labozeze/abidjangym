# Journal de bord — AbidjanGym

**Tenu par :** César, Rédaction (Entité C)
**Rôle :** source unique des faits pour les notes de version et l'article de blog. Chaque fait renvoie à sa source.

---

## Avant-projet — 30 octobre 2026

### Point de départ
- Le dépôt local contient le cahier des charges v2.0, 14 maquettes HTML (`demo/`) et des visuels (`photos/`). Aucun code applicatif, aucun commit. *(Source : inventaire du dépôt par Claude, 30/10)*

### Décisions de Rufin
- **Répartition des entités :** Codex pour le frontend (Entité A), DeepSeek pour le backend (Entité B), Claude pour l'Entité C (pilotage, DevOps, tests, rédaction). *(Source : message de Rufin, 30/10)*
- **Agents de l'Entité C nommés :** Benjamin (chef de projet), Eric (DevOps), César (rédaction). Après une première proposition qui confiait les tests à Eric, Rufin a préféré **un agent séparé pour les tests : Valère**. *(Source : messages de Rufin, 30/10)*
- **Outils :** Codex en version desktop, DeepSeek via « DeepSeek harness ». *(Source : message de Rufin, 30/10)*
- **Déploiement :** c'est Rufin qui a proposé de relier directement l'hébergement Hostinger au dépôt Git, à la place de la solution FTP initialement prévue. La proposition a été adaptée : Hostinger suit des branches de déploiement construites et testées par la CI, et le backend est mis à jour par SSH. *(Source : échange du 30/10, plan v0.3)*
- **Dépôt GitHub public**, dans l'organisation `Labozeze`. *(Source : message de Rufin, 30/10)*
- **Cloudflare :** d'abord « pas pour le moment », puis compte créé le jour même. *(Source : messages de Rufin, 30/10)*
- **Validation du plan de collaboration v1.0** et des quatre fiches de rôle. Lancement du sprint 0 fixé au **31 octobre 2026**. *(Source : message de Rufin, 30/10)*

### Ce qui a été corrigé en route
- **Nom de l'organisation GitHub.** Le cahier des charges indiquait `lelabodeze`. En vérifiant l'accès avec GitHub CLI, Claude a constaté que cette organisation n'existait pas : la vraie s'appelle `Labozeze`. Le cahier des charges a été corrigé en v2.1. *(Source : vérification `gh` du 30/10, changelog CDC v2.1)*
- **Limites de DeepSeek harness découvertes avant le démarrage.** À la demande de Claude, DeepSeek a testé lui-même ses capacités : il écrit uniquement dans son espace de travail, lit `AGENTS.md` automatiquement, dispose de `git` mais pas de `gh`. Conséquence : les PR seront ouvertes automatiquement par un workflow GitHub. *(Source : rapport de DSH transmis par Rufin, 30/10, plan v0.4)*

### Interventions humaines
- Rufin a installé et connecté GitHub CLI lui-même, en suivant les instructions de Claude. *(Source : échange du 30/10)*

### Citation
- *(aucune pour l'instant : toute citation de Rufin demande son accord)*

---

## Sprint 0 — semaine du 31/10/2026 (en cours)

### Jour 1 — 31/10
- **Feu vert de Rufin** pour publier les documents sur le dépôt public et lancer le sprint 0. *(Source : message de Rufin, 31/10)*
- **Constat au démarrage :** le dépôt `Labozeze/abidjangym` existait déjà sur GitHub (créé le 29/10), public et vide. *(Source : vérification `gh`, 31/10)*
- **[Entité C — Eric]** Premier commit, branches `main` et `develop` (develop par défaut), protections des deux branches (PR obligatoire, checks `secrets` et `perimetre`, règles appliquées aussi aux administrateurs), secret scanning et push protection, 17 étiquettes, tableau GitHub Projects à 6 colonnes, CI de base et workflow de PR automatique. *(Source : commits `fec4a88`, `dc79bf4`)*
- **Contrainte découverte :** l'organisation `Labozeze` interdit à GitHub Actions de créer des PR. L'ouverture automatique des PR, prévue pour DeepSeek, est donc bloquée tant que Rufin n'a pas modifié ce réglage. *(Source : réponse de l'API GitHub, 31/10, ticket #13)*
- **Choix de protection :** tous les outils utilisent le même compte GitHub. Une approbation obligatoire bloquerait donc toutes les PR, puisqu'on ne peut pas approuver la sienne. Les protections exigent une PR et des checks verts, mais pas d'approbation formelle. La relecture se fait par commentaires (Benjamin, Valère). *(Source : configuration des protections, 31/10)*
- **[Entité C — Benjamin]** `AGENTS.md` commun, briefs de Codex et de DeepSeek, 13 tickets du sprint 0 (#1 à #13). *(Source : tickets #1 à #13)*
- **[Entité C — Valère]** Stratégie de tests v1.0. *(Source : ticket #4)*
- **Première livraison par PR (#14).** La CI est verte (secrets, périmètre). Comme prévu, l'ouverture automatique de la PR a échoué à cause du réglage de l'organisation : la PR a été ouverte à la main. Claude n'a pas pu la merger lui-même, car le garde-fou de son environnement interdit de merger sans relecture humaine. Le merge revient donc à Rufin. *(Source : PR #14, 31/10)*
- **[Entité C — Eric]** Dossiers de travail créés : `abidjangym-front` (Codex) et `default-workspace\abidjangym-back` (DeepSeek), tous deux sur `develop`. *(Source : ticket #2)*
- **Réglage de l'organisation modifié par Rufin :** GitHub Actions peut désormais ouvrir les PR automatiquement. *(Source : ticket #13, 31/10)*
- **[Entité B — DeepSeek] Test de la chaîne réussi (#6, PR #15).** Le `git push` passe depuis le bac à sable de DeepSeek harness, la PR s'ouvre toute seule. C'était le point le plus incertain du plan. *(Source : PR #15)*
- **[Entité A — Codex] Test de la chaîne réussi (#5, PR #16).** Rufin a mergé avant la relecture de Benjamin et Valère, puis a décidé d'attendre désormais leur relecture avant chaque merge. Depuis, chaque merge attend leur commentaire. *(Source : PR #16, message de Rufin du 31/10)*

### Jours 1 et 2 — du 31/10 au 01/11 : les deux points de contrôle
- **[Entité A — Codex] Audit des maquettes (#7, PR #17).** Travail jugé de très bonne qualité, mais **première correction demandée** : l'audit renvoyait à Rufin la durée d'essai et les prix, alors que le CDC §1.3 les fixe déjà (2 mois). Codex a corrigé, ce qui a retiré deux décisions inutiles à Rufin. *(Source : relectures de la PR #17)*
- **Point de contrôle n° 1 : maquettes validées par Rufin**, avec 5 décisions, dont : aucun chiffre ni témoignage inventé sur le site, page Tarifs dédiée, FAQ rédigée par Codex. *(Source : ticket #7, commentaire du 31/10)*
- **[Entité B — DeepSeek] Contrat d'API (#9, PR #20)** : 62 opérations, 0 erreur de validation. Corrections demandées par Benjamin et Valère : gestion des caissiers absente, limitation des tentatives manquante sur 3 routes, caissier autorisé à tort à modifier un paiement. Rufin a tranché 5 arbitrages techniques sur recommandation de Benjamin. DeepSeek a tout corrigé en une passe (v0.2.0, 66 opérations). *(Source : PR #20)*
- **Point de contrôle n° 2 : contrat d'API v0.2.0 validé par Rufin** le 01/11. *(Source : PR #20, commentaire du 01/11)*
- **[Entité A — Codex] Squelette Nuxt (#8, PR #21)** : Valère et Eric ont exécuté eux-mêmes l'installation, le lint, les tests et la génération. Tout passe, les tokens sont identiques aux maquettes. *(Source : relecture de la PR #21)*
- **[Entité A — Codex] FAQ (#18, PR #22)** : contenu juste, mais écrit « pour l'équipe projet » (« Le CDC prévoit… », « les routes de l'API… »). Réécriture demandée pour les gérants, puis faite (12 questions). *(Source : relectures de la PR #22)*
- **Décision de Rufin en relisant la FAQ :** chaque moyen de paiement doit pouvoir s'activer ou se désactiver, et seuls les moyens activés sont proposés. En relisant un texte destiné aux clients, Rufin a fait naître une nouvelle règle fonctionnelle. Elle a été reportée dans le CDC v2.2 et dans un ticket de contrat d'API (#23). *(Source : message de Rufin du 01/11, ticket #23)*

### Suite du sprint 0 : le squelette Laravel et une alerte de sécurité
- **[Entité B — DeepSeek] Squelette Laravel (#10, PR #25).** En exécutant les tests sur une copie propre, Valère obtient 12 tests sur 13. **La cause venait de l'Entité C** : le `.gitignore` écrit par Eric au sprint 0 excluait des dossiers dont Laravel a besoin. Sur le poste de DeepSeek, les tests passaient ; après une copie fraîche, en CI ou sur Hostinger, ils auraient échoué. Eric a corrigé son erreur (PR #26). Valère a aussi demandé un garde-fou supplémentaire : empêcher de déplacer une donnée vers une autre salle lors d'une modification. DeepSeek l'a ajouté, et 15 tests sur 15 passent. *(Source : relectures des PR #25 et #26)*
- **Merge avant le dernier commentaire.** La PR #25 a été mergée avant que DeepSeek traite le dernier point d'Eric (`composer.lock` désynchronisé). Le point a été repris dans un petit ticket (#27, PR #28). *(Source : PR #25, ticket #27)*
- **Alerte de sécurité remontée par DeepSeek (PR #28).** En vérifiant son correctif, DeepSeek a lancé un audit et trouvé 5 failles dans Laravel 11, dont une critique. Il ne les a pas corrigées lui-même, car cela sortait de son périmètre : il les a signalées. Eric a confirmé l'audit, et Rufin a décidé de **passer à Laravel 13** (PHP 8.3), sur recommandation de Benjamin. Cahier des charges v2.3, ticket #30. *(Source : PR #28, ticket #30)*
- **[Entité C — Eric] CI du front et du back (#11, PR #33)** : chaque PR est désormais vérifiée automatiquement (lint, tests, génération statique, `composer.lock` synchronisé, audit de sécurité). Ces contrôles auraient détecté seuls les deux problèmes de la semaine. Ils sont devenus obligatoires sur `develop` et `main`. *(Source : PR #33)*
- **[Entité C — Eric] Serveur fictif Prism (#12)**, testé avant d'être documenté. Piège repéré pendant le test : il sert les routes sans le préfixe `/api/v1`. *(Source : `docs/ops/serveur-fictif.md`)*
- **Poste de travail :** en vérifiant la migration, Eric a trouvé la vraie raison pour laquelle le chiffrement TLS de Composer avait été désactivé : PHP ne connaissait pas l'emplacement des certificats. Avec l'accord de Rufin, les certificats ont été configurés, le chiffrement réactivé et Composer mis à jour (sa version précédente avait 4 failles graves). *(Source : échanges du 01/11)*
- **Ticket en doublon :** DeepSeek, qui dispose désormais de `gh`, a ouvert lui-même un ticket (#29) proposant Laravel 12, en parallèle de la décision de Rufin pour Laravel 13. Il a été fermé avec l'accord de Rufin, et une règle a été ajoutée à `AGENTS.md` : les entités signalent dans leur PR, Benjamin crée les tickets. *(Source : #29)*
- **[Entité B — DeepSeek] Moyens de paiement on/off (#23, PR #34)**, contrat v0.3.0. Les identifiants des prestataires ne sont jamais renvoyés par l'API. DeepSeek a laissé libres les noms de ces identifiants plutôt que de les inventer. *(Source : PR #34)*

### Bilan du sprint 0
- **Terminé avec 4 jours d'avance** : 16 tickets, 15 PR, les 2 points de contrôle validés. Rapport : `docs/rapports/sprint-00.md`. *(Source : rapport du sprint 0)*

---

## Publications

### Épisode 1 — « Les fondations » (publié le 01/11/2026)
- **Article publié par Rufin** sur le blog du Labo de Zézé : https://lelabodezeze.com/blog/construire-saas-agents-ia-episode-1/ *(Source : message de Rufin, 01/11)*
- Rédigé par César à partir de ce journal et du rapport du sprint 0. Rufin a fourni sa motivation (les expériences du Labo de Zézé sur l'IA) et a choisi de gérer lui-même l'appel à l'action et la catégorie sur son site. Le slug publié diffère de celui proposé. Sources de l'article : `blog/articles/episode-1-fondations/`. *(Source : échanges du 01/11)*
- **Point notable pour la suite :** l'article a été publié pendant le projet, comme le premier épisode d'une série, et non à la fin comme le prévoyait le CDC §9. *(Source : décision de Rufin, 01/11)*

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

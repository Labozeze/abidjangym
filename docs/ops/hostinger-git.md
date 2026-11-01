# Mettre en ligne le staging sur Hostinger : marche à suivre pour Rufin

**Rédigé par :** Eric, DevOps (Entité C)
**Ticket :** #42
**Durée estimée :** 30 à 45 minutes, une seule fois

---

## Comment ça marche (en bref)

```
merge dans develop
   └─► GitHub construit le site (front généré + back avec ses dépendances)
          └─► le publie sur la branche « deploy-staging »
                 └─► se connecte à Hostinger en SSH : récupère cette branche,
                     lance les migrations de la base, met en cache la configuration
                        └─► vérifie que le front et l'API répondent
```

Sur le serveur, un seul dossier reçoit la branche `deploy-staging` :

```
<dossier de déploiement>/
├── front/          ← racine web de staging.abidjangym.com
├── api/
│   └── public/     ← racine web de api-staging.abidjangym.com
└── VERSION         ← version déployée
```

> **Noms proposés :** `staging.abidjangym.com` (site public) et `api-staging.abidjangym.com` (API et backoffice).
> Le CDC §3.3 ne prévoit que `staging.abidjangym.com` : le second sous-domaine est une proposition d'Eric, à valider.

**Rien n'est mis en ligne tant que tu n'as pas terminé l'étape 7** : la publication de la branche fonctionne dès le merge, mais la mise à jour du serveur attend ton feu vert.

---

## Étape 1 — Créer la branche de déploiement

Rien à faire : au premier merge dans `develop` après celui de la PR du ticket #42, GitHub crée la branche `deploy-staging`. Vérifie qu'elle existe dans la liste des branches du dépôt avant l'étape 4.

## Étape 2 — PHP 8.3 et sous-domaines (hPanel)

1. **hPanel → Sites web → abidjangym.com → Avancé → Configuration PHP** : choisir **PHP 8.3**.
2. **hPanel → Domaines → Sous-domaines** : créer les deux sous-domaines en cochant **« Dossier personnalisé »** :

| Sous-domaine | Dossier personnalisé |
|---|---|
| `staging` | `<dossier de déploiement>/front` |
| `api-staging` | `<dossier de déploiement>/api/public` |

   Par exemple : `<dossier de déploiement>` = `domains/abidjangym.com/staging-app`. Note le chemin exact, il servira aux étapes 4 et 6.
3. **SSL** : vérifier que le certificat Let's Encrypt est actif sur les deux sous-domaines (hPanel → Sécurité → SSL).

> Si le domaine `abidjangym.com` n'est pas géré par Hostinger (voir #13, registraire), les sous-domaines devront aussi être déclarés dans la zone DNS du registraire. Dis-le à Eric.

## Étape 3 — Activer l'accès SSH

**hPanel → Avancé → Accès SSH** : activer SSH et noter les **4 informations** affichées :
- l'adresse IP ou le nom d'hôte ;
- le **port** (souvent `65002` chez Hostinger) ;
- le nom d'utilisateur ;
- le chemin du dossier d'accueil (*home*).

## Étape 4 — Clé SSH dédiée au déploiement

Sur ton PC, dans un terminal (Git Bash) :

```bash
ssh-keygen -t ed25519 -C "deploiement-staging-abidjangym" -f ~/.ssh/abidjangym_staging -N ""
```

Cela crée deux fichiers :
- `~/.ssh/abidjangym_staging.pub` : la clé **publique**. Ouvre-la et copie son contenu dans **hPanel → Avancé → Accès SSH → Clés SSH → Ajouter**.
- `~/.ssh/abidjangym_staging` : la clé **privée**. Elle ira **uniquement** dans les secrets GitHub (étape 6). **Ne l'envoie à personne, ne la colle dans aucune conversation, ni ici ni ailleurs.**

Récupère ensuite l'empreinte du serveur, qui sert à vérifier qu'on se connecte bien à Hostinger :

```bash
ssh-keyscan -p <PORT> <HÔTE>
```

Garde le résultat (une ou plusieurs lignes) pour l'étape 6.

## Étape 5 — Base de données, premier clonage et fichier `.env`

1. **hPanel → Bases de données → MySQL** : créer une base et un utilisateur pour le staging. Note le nom de la base, l'utilisateur et le mot de passe.
2. Connecte-toi en SSH :
   ```bash
   ssh -i ~/.ssh/abidjangym_staging -p <PORT> <UTILISATEUR>@<HÔTE>
   ```
3. Clone la branche de déploiement dans le dossier choisi à l'étape 2 (le dépôt est public, aucune clé n'est nécessaire pour le lire) :
   ```bash
   git clone -b deploy-staging https://github.com/Labozeze/abidjangym.git <dossier de déploiement>
   cd <dossier de déploiement>/api
   cp .env.example .env
   ```
4. Ouvre `.env` (avec `nano .env`) et renseigne **au minimum** :
   ```
   APP_ENV=staging
   APP_DEBUG=false
   APP_URL=https://api-staging.abidjangym.com
   DB_DATABASE=<ta base>
   DB_USERNAME=<ton utilisateur>
   DB_PASSWORD=<ton mot de passe>
   CORS_ALLOWED_ORIGINS=https://staging.abidjangym.com
   MAIL_MAILER=log
   ```
   Le mot de passe de la base reste **sur le serveur, dans ce fichier uniquement**.
5. Génère la clé de l'application, puis vérifie la version de PHP en ligne de commande :
   ```bash
   php artisan key:generate
   php -v
   ```
   Si `php -v` n'affiche pas 8.3, note le chemin du PHP 8.3 (souvent `/opt/alt/php83/usr/bin/php`) pour l'étape 6.

## Étape 6 — Secrets et variables dans GitHub

Dans le dépôt : **Settings → Secrets and variables → Actions**.

**Secrets** (onglet *Secrets*, valeurs confidentielles) :

| Nom | Valeur |
|---|---|
| `STAGING_SSH_HOST` | l'hôte (étape 3) |
| `STAGING_SSH_PORT` | le port (étape 3) |
| `STAGING_SSH_USER` | l'utilisateur (étape 3) |
| `STAGING_SSH_KEY` | **tout** le contenu du fichier `~/.ssh/abidjangym_staging` (clé privée) |
| `STAGING_SSH_KNOWN_HOSTS` | le résultat de `ssh-keyscan` (étape 4) |
| `STAGING_DEPLOY_PATH` | le chemin complet du dossier de déploiement |

**Variables** (onglet *Variables*, non confidentielles) :

| Nom | Valeur |
|---|---|
| `STAGING_FRONT_URL` | `https://staging.abidjangym.com` |
| `STAGING_API_ORIGIN` | `https://api-staging.abidjangym.com` |
| `STAGING_PHP_BIN` | seulement si `php -v` n'affichait pas 8.3 : le chemin noté à l'étape 5 |

## Étape 7 — Activer le déploiement automatique

Ajoute la **variable** `STAGING_DEPLOY_ENABLED` = `true`.

Puis lance un premier déploiement : **Actions → Déploiement staging → Run workflow** (branche `develop`). Les étapes *construire* puis *deployer* doivent passer au vert, et les tests de fumée afficher `Front : 200` et `API : 200`.

À partir de là, **chaque merge dans `develop` met le staging à jour tout seul.**

---

## Ce que le staging garantit

| Garantie | Comment |
|---|---|
| Seul du code testé arrive sur le serveur | Seule la CI écrit sur `deploy-staging`, après les tests (protection de `develop`) |
| Migrations jamais lancées avant le nouveau code | Script SSH : récupération du code **puis** migrations |
| HTTPS et en-têtes de sécurité (CDC §6.1, §6.5) | `.htaccess` ajoutés au déploiement (`ops/htaccess/`) |
| Staging non indexé par Google | `X-Robots-Tag: noindex` et `robots.txt` bloquant sur le staging |
| Aucun secret dans le dépôt | Clé privée et identifiants dans les secrets GitHub, mot de passe de la base dans `.env` sur le serveur |

## En cas de problème

| Symptôme | Piste |
|---|---|
| L'étape *deployer* échoue avec « Host key verification failed » | Refaire `ssh-keyscan` avec le bon port, puis mettre à jour `STAGING_SSH_KNOWN_HOSTS` |
| « api/.env absent sur le serveur » | Étape 5 non faite, ou mauvais `STAGING_DEPLOY_PATH` |
| Erreur 500 sur l'API | Sur le serveur : `tail -n 50 <dossier>/api/storage/logs/laravel.log` (à transmettre à Eric, **sans** le fichier `.env`) |
| Le front appelle la mauvaise API | Vérifier la variable `STAGING_API_ORIGIN`, puis relancer le workflow |

## Notes techniques (pour Eric et les entités)

- **CSP :** le CDC §6.5 ne prévoit pas `connect-src`. Sans cette directive, le navigateur bloquerait les appels du front vers l'API. Elle est ajoutée avec l'origine de l'API (`STAGING_API_ORIGIN`). À reporter dans le CDC lors de sa prochaine version.
- **`X-XSS-Protection: 1; mode=block`** est conservé parce que le CDC l'exige, mais cet en-tête est obsolète dans les navigateurs récents. À reconsidérer lors de la prochaine version du CDC.
- **Webhook Git de l'hPanel :** inutile avec ce fonctionnement, puisque le serveur est mis à jour en SSH dans un ordre garanti. Le clonage initial peut aussi se faire depuis l'outil Git de l'hPanel (dépôt public, branche `deploy-staging`) au lieu de la commande de l'étape 5.

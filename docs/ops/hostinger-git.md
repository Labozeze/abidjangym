# Mettre en ligne le staging sur Hostinger : marche à suivre pour Rufin

**Rédigé par :** Eric, DevOps (Entité C)
**Ticket :** #42
**Durée estimée :** 30 à 45 minutes, une seule fois

Les valeurs à saisir sont indiquées **entre parenthèses** à chaque étape. Quand une valeur dépend de ton compte Hostinger, l'exemple le plus probable est donné.

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

L'application est installée **en dehors de `public_html`**, pour que le fichier `.env` (qui contient le mot de passe de la base) ne soit jamais accessible depuis le web. Les sous-domaines n'y accèdent que par des liens :

```
~/staging-app/                    ← dossier de déploiement (hors du web)
├── front/                        ← servi par staging.abidjangym.com
├── api/
│   ├── .env                      ← jamais accessible depuis le web
│   └── public/                   ← servi par api-staging.abidjangym.com
└── VERSION

~/domains/abidjangym.com/public_html/
├── staging       → lien vers ~/staging-app/front
└── api-staging   → lien vers ~/staging-app/api/public
```

Sous-domaines retenus, validés par Rufin le 01/11/2026 : (`staging.abidjangym.com`) pour le site public et (`api-staging.abidjangym.com`) pour l'API et le backoffice.

**Rien n'est mis en ligne tant que tu n'as pas terminé l'étape 7.**

---

## Étape 1 — Merger la PR du déploiement

Merge la PR (#46). Au merge suivant dans `develop`, ou en lançant **Actions → Déploiement staging → Run workflow** sur la branche (`develop`), GitHub crée la branche (`deploy-staging`). Vérifie qu'elle apparaît dans la liste des branches du dépôt avant l'étape 5.

## Étape 2 — PHP 8.3 et sous-domaines (hPanel)

1. **hPanel → Sites web → (abidjangym.com) → Avancé → Configuration PHP** : choisir (**PHP 8.3**).
2. **hPanel → Domaines → Sous-domaines** : créer le sous-domaine (`staging`). Si hPanel propose un « dossier personnalisé », mets (`staging`). Il sera créé dans `public_html`, et on le transformera en lien à l'étape 5.
3. Même chose pour le sous-domaine (`api-staging`), avec le dossier (`api-staging`).
4. **hPanel → Sécurité → SSL** : vérifier que le certificat (Let's Encrypt) est actif sur (`staging.abidjangym.com`) et (`api-staging.abidjangym.com`).

> Si le domaine `abidjangym.com` n'est pas géré par Hostinger (ticket #13, registraire), les deux sous-domaines devront aussi être déclarés dans la zone DNS du registraire. Dis-le à Eric.

## Étape 3 — Activer l'accès SSH

**hPanel → Avancé → Accès SSH** : activer SSH et noter les 3 informations affichées. Tu les réutiliseras aux étapes 4 à 6 :
- l'**hôte** (une adresse IP, ex. `123.45.67.89`) ;
- le **port** (chez Hostinger, généralement `65002`) ;
- l'**utilisateur** (ex. `u123456789`).

## Étape 4 — Clé SSH dédiée au déploiement

Sur ton PC, dans un terminal Git Bash :

```bash
ssh-keygen -t ed25519 -C "deploiement-staging-abidjangym" -f ~/.ssh/abidjangym_staging -N ""
```

Deux fichiers sont créés :
- la clé **publique** (`~/.ssh/abidjangym_staging.pub`) : ouvre-la et colle son contenu dans **hPanel → Avancé → Accès SSH → Clés SSH → Ajouter** ;
- la clé **privée** (`~/.ssh/abidjangym_staging`) : elle ira **uniquement** dans les secrets GitHub (étape 6). **Ne l'envoie à personne, ne la colle dans aucune conversation, ni ici ni ailleurs.**

Récupère ensuite l'empreinte du serveur (remplace par ton port et ton hôte, ex. `ssh-keyscan -p 65002 123.45.67.89`) :

```bash
ssh-keyscan -p <PORT> <HÔTE>
```

Garde le résultat (une ou plusieurs lignes) pour l'étape 6.

## Étape 5 — Base de données, installation et fichier `.env`

1. **hPanel → Bases de données → MySQL** : créer une base (ex. `u123456789_staging`) et un utilisateur (ex. `u123456789_staging`). Hostinger ajoute automatiquement le préfixe de ton compte. Note le nom exact de la base, l'utilisateur et le mot de passe.
2. Connecte-toi en SSH (ex. `ssh -i ~/.ssh/abidjangym_staging -p 65002 u123456789@123.45.67.89`) :
   ```bash
   ssh -i ~/.ssh/abidjangym_staging -p <PORT> <UTILISATEUR>@<HÔTE>
   ```
3. Installe la branche de déploiement **hors du web**, dans (`~/staging-app`) :
   ```bash
   git clone -b deploy-staging https://github.com/Labozeze/abidjangym.git ~/staging-app
   ```
4. Remplace les dossiers des sous-domaines créés à l'étape 2 par des liens vers l'application :
   ```bash
   cd ~/domains/abidjangym.com/public_html
   rmdir staging api-staging
   ln -s ~/staging-app/front staging
   ln -s ~/staging-app/api/public api-staging
   ls -l staging api-staging
   ```
   La dernière commande doit afficher deux flèches (`staging -> /home/…/staging-app/front` et `api-staging -> /home/…/staging-app/api/public`). Si `rmdir` refuse parce qu'un dossier n'est pas vide, regarde son contenu avec `ls -la staging` et signale-le à Eric avant de supprimer quoi que ce soit.
5. Crée le fichier de configuration de l'API :
   ```bash
   cd ~/staging-app/api
   cp .env.example .env
   nano .env
   ```
   Modifie **au minimum** ces lignes (avec tes vraies valeurs de base de données) :
   ```
   APP_ENV=staging
   APP_DEBUG=false
   APP_URL=https://api-staging.abidjangym.com
   DB_DATABASE=u123456789_staging
   DB_USERNAME=u123456789_staging
   DB_PASSWORD=le-mot-de-passe-de-la-base
   CORS_ALLOWED_ORIGINS=https://staging.abidjangym.com
   MAIL_MAILER=log
   ```
   Enregistre avec `Ctrl+O`, `Entrée`, puis quitte avec `Ctrl+X`. Le mot de passe de la base reste **sur le serveur, dans ce fichier uniquement**.
6. Génère la clé de l'application et vérifie la version de PHP :
   ```bash
   php artisan key:generate
   php -v
   ```
   Si `php -v` n'affiche pas **8.3**, utilise (`/opt/alt/php83/usr/bin/php -v`). Si cette commande affiche 8.3, retiens ce chemin pour l'étape 6.

## Étape 6 — Secrets et variables dans GitHub

Dans le dépôt : **Settings → Secrets and variables → Actions**.

**Onglet Secrets** (bouton *New repository secret*), à créer un par un :
1. nom (`STAGING_SSH_HOST`), valeur : ton hôte (ex. `123.45.67.89`)
2. nom (`STAGING_SSH_PORT`), valeur : ton port (ex. `65002`)
3. nom (`STAGING_SSH_USER`), valeur : ton utilisateur (ex. `u123456789`)
4. nom (`STAGING_SSH_KEY`), valeur : **tout** le contenu du fichier (`~/.ssh/abidjangym_staging`), lignes `-----BEGIN…` et `-----END…` comprises. Pour l'afficher : `cat ~/.ssh/abidjangym_staging`
5. nom (`STAGING_SSH_KNOWN_HOSTS`), valeur : le résultat complet de `ssh-keyscan` (étape 4)
6. nom (`STAGING_DEPLOY_PATH`), valeur : le chemin complet (`/home/<UTILISATEUR>/staging-app`, ex. `/home/u123456789/staging-app`)

**Onglet Variables** (bouton *New repository variable*) :
1. nom (`STAGING_FRONT_URL`), valeur (`https://staging.abidjangym.com`)
2. nom (`STAGING_API_ORIGIN`), valeur (`https://api-staging.abidjangym.com`)
3. **Seulement si** `php -v` n'affichait pas 8.3 à l'étape 5 : nom (`STAGING_PHP_BIN`), valeur (`/opt/alt/php83/usr/bin/php`)

## Étape 7 — Activer le déploiement automatique

1. Onglet **Variables** : nom (`STAGING_DEPLOY_ENABLED`), valeur (`true`).
2. **Actions → Déploiement staging → Run workflow**, branche (`develop`).
3. Les jobs (`construire`) puis (`deployer`) doivent passer au vert, et les tests de fumée afficher (`Front : 200`) et (`API : 200`).
4. Ouvre (`https://staging.abidjangym.com`) et (`https://api-staging.abidjangym.com/api/v1/health`) dans ton navigateur.

À partir de là, **chaque merge dans `develop` met le staging à jour tout seul.**

---

## Ce que le staging garantit

| Garantie | Comment |
|---|---|
| Seul du code testé arrive sur le serveur | Seule la CI écrit sur `deploy-staging`, après les tests (protection de `develop`) |
| Le `.env` n'est jamais accessible depuis le web | Application installée hors de `public_html`, les sous-domaines ne voient que `front/` et `api/public/` |
| Migrations jamais lancées avant le nouveau code | Script SSH : récupération du code **puis** migrations |
| HTTPS et en-têtes de sécurité (CDC §6.1, §6.5) | `.htaccess` ajoutés au déploiement (`ops/htaccess/`) |
| Staging non indexé par Google | `X-Robots-Tag: noindex` et `robots.txt` bloquant sur le staging |
| Aucun secret dans le dépôt | Clé privée et identifiants dans les secrets GitHub, mot de passe de la base dans `.env` sur le serveur |

## En cas de problème

| Symptôme | Piste |
|---|---|
| « Host key verification failed » dans le job `deployer` | Refaire `ssh-keyscan -p 65002 <HÔTE>`, puis mettre à jour le secret (`STAGING_SSH_KNOWN_HOSTS`) |
| « api/.env absent sur le serveur » | Étape 5.5 non faite, ou mauvais secret (`STAGING_DEPLOY_PATH`) |
| Page blanche ou 404 sur un sous-domaine | Vérifier les liens : `ls -l ~/domains/abidjangym.com/public_html/staging ~/domains/abidjangym.com/public_html/api-staging` |
| Erreur 500 sur l'API | `tail -n 50 ~/staging-app/api/storage/logs/laravel.log` (à transmettre à Eric, **jamais** le fichier `.env`) |
| Le front appelle la mauvaise API | Vérifier la variable (`STAGING_API_ORIGIN`), puis relancer le workflow |

## Notes techniques (pour Eric et les entités)

- **CSP :** le CDC §6.5 ne prévoit pas `connect-src`. Sans cette directive, le navigateur bloquerait les appels du front vers l'API. Elle est ajoutée avec l'origine de l'API (`STAGING_API_ORIGIN`). À reporter dans le CDC lors de sa prochaine version.
- **`X-XSS-Protection: 1; mode=block`** est conservé parce que le CDC l'exige, mais cet en-tête est obsolète dans les navigateurs récents. À reconsidérer lors de la prochaine version du CDC.
- **Liens symboliques :** Apache suit les liens des racines de sous-domaines sur Hostinger. Si ce n'est pas le cas sur l'offre souscrite, l'alternative est de faire pointer le dossier personnalisé du sous-domaine directement sur `~/staging-app/...` si hPanel le permet.
- **Webhook Git de l'hPanel :** inutile avec ce fonctionnement, puisque le serveur est mis à jour en SSH dans un ordre garanti.

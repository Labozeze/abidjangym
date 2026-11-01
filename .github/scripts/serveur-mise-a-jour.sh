#!/usr/bin/env bash
# Exécuté SUR le serveur Hostinger, en SSH, par le workflow de déploiement.
# Usage : serveur-mise-a-jour.sh <dossier de déploiement> <binaire php> <branche>
set -euo pipefail

DEPLOY_PATH="$1"
PHP_BIN="${2:-php}"
BRANCHE="${3:-deploy-staging}"

cd "$DEPLOY_PATH"
git fetch --quiet origin "$BRANCHE"
git reset --hard --quiet "origin/$BRANCHE"

cd api
if [ ! -f .env ]; then
  echo "ERREUR : api/.env absent sur le serveur (voir docs/ops/hostinger-git.md, étape 5)." >&2
  exit 1
fi

# Ordre garanti : le code est en place AVANT les migrations
"$PHP_BIN" artisan migrate --force --no-interaction
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

echo "Version déployée : $(cat ../VERSION)"

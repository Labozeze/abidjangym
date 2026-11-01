#!/usr/bin/env bash
# Indique (sortie GitHub Actions « change ») si l'un des chemins donnés a été
# modifié par rapport à la branche de base. Usage : a-change.sh <chemin>...
set -euo pipefail

BASE="${BASE:-develop}"
git fetch --quiet origin "$BASE"

if git diff --quiet "origin/$BASE...HEAD" -- "$@"; then
  echo "Aucun changement dans : $* (étapes ignorées)."
  echo "change=false" >> "$GITHUB_OUTPUT"
else
  echo "Changements détectés dans : $*"
  echo "change=true" >> "$GITHUB_OUTPUT"
fi

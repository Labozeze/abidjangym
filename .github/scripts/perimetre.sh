#!/usr/bin/env bash
# Garde-fou de périmètre : chaque entité ne modifie que son dossier.
# Entité déduite du nom de branche : feature/a-*, feature/b-*, feature/c-*.
set -euo pipefail

BRANCH="${BRANCH:?}"
BASE="${BASE:-develop}"

case "$BRANCH" in
  feature/a-*) ENTITE="a" ;;
  feature/b-*) ENTITE="b" ;;
  feature/c-*) ENTITE="c" ;;
  feature/*)
    echo "::error::Branche '$BRANCH' invalide : utilisez feature/a-<n°>-<sujet>, feature/b-... ou feature/c-..."
    exit 1 ;;
  *)
    echo "Branche '$BRANCH' hors convention d'entité (develop, main, hotfix) : contrôle non applicable."
    exit 0 ;;
esac

git fetch --quiet origin "$BASE"
FICHIERS=$(git diff --name-only "origin/$BASE...HEAD")

if [ -z "$FICHIERS" ]; then
  echo "Aucun fichier modifié par rapport à $BASE."
  exit 0
fi

# Chemins autorisés (expressions régulières) par entité
case "$ENTITE" in
  a) AUTORISE='^(frontend/|docs/adr/)' ;;
  b) AUTORISE='^(backend/|docs/api/|docs/adr/)' ;;
  c) AUTORISE='^(\.github/|\.claude/|ops/|tests/|docs/|blog/|demo/|livrables/|photos/|AGENTS\.md|CLAUDE\.md|README\.md|\.gitignore|\.gitattributes|frontend/AGENTS\.md$|backend/AGENTS\.md$)' ;;
esac

HORS=$(echo "$FICHIERS" | grep -Ev "$AUTORISE" || true)

if [ -n "$HORS" ]; then
  echo "::error::L'entité ${ENTITE^^} a modifié des fichiers hors de son périmètre :"
  echo "$HORS" | sed 's/^/  - /'
  echo "Voir AGENTS.md, section « Périmètre »."
  exit 1
fi

echo "Périmètre respecté pour l'entité ${ENTITE^^} ($(echo "$FICHIERS" | wc -l) fichier(s))."

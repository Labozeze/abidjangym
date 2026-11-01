#!/usr/bin/env bash
# Assemble le site prêt à déployer dans _deploy/ :
#   _deploy/front/  ← site statique généré (racine web du sous-domaine du front)
#   _deploy/api/    ← application Laravel avec vendor/ (racine web : api/public)
# Variables : API_ORIGIN (pour la CSP du front), NOINDEX=true (staging non indexé).
set -euo pipefail

rm -rf _deploy
mkdir -p _deploy/front _deploy/api

# --- Front ---
cp -r frontend/.output/public/. _deploy/front/
cp ops/htaccess/front.htaccess _deploy/front/.htaccess
sed -i "s#__API_ORIGIN__#${API_ORIGIN:-}#g" _deploy/front/.htaccess
if [ "${NOINDEX:-false}" = "true" ]; then
  sed -i 's|^# __NOINDEX__$|  Header always set X-Robots-Tag "noindex, nofollow"|' _deploy/front/.htaccess
  printf 'User-agent: *\nDisallow: /\n' > _deploy/front/robots.txt
else
  sed -i '/^# __NOINDEX__$/d' _deploy/front/.htaccess
fi
# Fichiers versionnés par Nuxt (nom contenant une empreinte) : cache d'un an
if [ -d _deploy/front/_nuxt ]; then
  printf '<IfModule mod_headers.c>\n  Header set Cache-Control "public, max-age=31536000, immutable"\n</IfModule>\n' \
    > _deploy/front/_nuxt/.htaccess
fi

# --- API ---
rsync -a \
  --exclude tests/ --exclude node_modules/ --exclude .env --exclude .phpunit.result.cache \
  --exclude phpunit.xml --exclude 'storage/logs/*.log' \
  backend/ _deploy/api/

# En-têtes de sécurité et HTTPS placés AVANT les règles de Laravel
{ cat ops/htaccess/api-entete.conf; echo; cat _deploy/api/public/.htaccess; } > _deploy/api/public/.htaccess.tmp
mv _deploy/api/public/.htaccess.tmp _deploy/api/public/.htaccess

# Rien de vendor/ ne doit être ignoré par Git sur la branche de déploiement
rm -f _deploy/api/.gitignore
find _deploy/api/vendor -name .gitignore -delete
cp ops/deploiement/api.gitignore _deploy/api/.gitignore

echo "${GITHUB_SHA:-local}" > _deploy/VERSION
echo "Assemblage terminé : $(du -sh _deploy | cut -f1)"

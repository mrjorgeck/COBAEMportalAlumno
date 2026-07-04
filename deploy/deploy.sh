#!/usr/bin/env bash
#
# Despliegue del Portal de Nuevo Ingreso a Hostinger (docs/08).
# Requiere: llave SSH registrada en hPanel, git remoto configurado en el servidor.
#
# Uso: ./deploy/deploy.sh
# Configurar las variables o exportarlas antes de ejecutar.

set -euo pipefail

# ── Configuración ────────────────────────────────────────────────────────────
SSH_USER="${DEPLOY_SSH_USER:-u132762550}"
SSH_HOST="${DEPLOY_SSH_HOST:?Define DEPLOY_SSH_HOST (host SSH de Hostinger)}"
SSH_PORT="${DEPLOY_SSH_PORT:-65002}"
APP_DIR="/home/${SSH_USER}/apps/portal"
BRANCH="${DEPLOY_BRANCH:-main}"
PHP_BIN="${DEPLOY_PHP_BIN:-php}"   # ajustar si el CLI 8.3 tiene otra ruta
# ─────────────────────────────────────────────────────────────────────────────

SSH="ssh -p ${SSH_PORT} ${SSH_USER}@${SSH_HOST}"

echo "==> 1/4 Compilando assets localmente (el servidor no compila)"
cd "$(dirname "$0")/../portal"
npm ci
npm run build

echo "==> 2/4 Subiendo assets compilados"
rsync -avz --delete -e "ssh -p ${SSH_PORT}" \
    public/build/ "${SSH_USER}@${SSH_HOST}:${APP_DIR}/public/build/"

echo "==> 3/4 Respaldo de BD previo al deploy"
$SSH "cd ${APP_DIR} && ${PHP_BIN} artisan db:backup-predeploy || \
      mysqldump --defaults-extra-file=~/.my.cnf u132762550_COBAEM \
      > ~/backups/predeploy-\$(date +%Y%m%d-%H%M%S).sql || \
      echo 'AVISO: respaldo manual no disponible; continuar bajo tu propio riesgo'"

echo "==> 4/4 Actualizando aplicación en el servidor"
$SSH bash -s <<EOF
set -euo pipefail
cd ${APP_DIR}
${PHP_BIN} artisan down --retry=30 || true
git fetch origin ${BRANCH}
git reset --hard origin/${BRANCH}
composer install --no-dev --optimize-autoloader --no-interaction
${PHP_BIN} artisan migrate --force
${PHP_BIN} artisan config:cache
${PHP_BIN} artisan route:cache
${PHP_BIN} artisan view:cache
${PHP_BIN} artisan up
EOF

echo "✔ Despliegue completado: https://registrocobaemario.ariocentro.com"

#!/usr/bin/env bash
#
# Despliegue del Portal de Nuevo Ingreso a Hostinger (docs/08).
# Requiere: llave SSH registrada en hPanel, git remoto configurado en el servidor.
#
# Uso: ./deploy/deploy.sh
# Configurar las variables o exportarlas antes de ejecutar.

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DEFAULT_SSH_KEY="${ROOT_DIR}/cert/hostinger_antigua_ed25519"

# ── Configuración ────────────────────────────────────────────────────────────
SSH_USER="${DEPLOY_SSH_USER:-u132762550}"
SSH_HOST="${DEPLOY_SSH_HOST:-46.202.183.138}"
SSH_PORT="${DEPLOY_SSH_PORT:-65002}"
SSH_KEY="${DEPLOY_SSH_KEY:-$DEFAULT_SSH_KEY}"
REPO_DIR="/home/${SSH_USER}/apps/portal"
APP_DIR="${REPO_DIR}/portal"
BRANCH="${DEPLOY_BRANCH:-main}"
PHP_BIN="${DEPLOY_PHP_BIN:-php}"   # ajustar si el CLI 8.3 tiene otra ruta
# ─────────────────────────────────────────────────────────────────────────────

SSH_KEY_ARGS=()
if [[ -n "${SSH_KEY}" ]]; then
    if [[ ! -f "${SSH_KEY}" ]]; then
        echo "ERROR: no existe la llave SSH configurada: ${SSH_KEY}" >&2
        echo "Define DEPLOY_SSH_KEY con la ruta correcta o coloca la llave en cert/hostinger_antigua_ed25519." >&2
        exit 1
    fi

    chmod 600 "${SSH_KEY}" 2>/dev/null || true
    SSH_KEY_ARGS=(-i "${SSH_KEY}" -o IdentitiesOnly=yes)
fi

SSH=(ssh -p "${SSH_PORT}" "${SSH_KEY_ARGS[@]}" "${SSH_USER}@${SSH_HOST}")
RSYNC_SSH="ssh -p ${SSH_PORT}"
if [[ -n "${SSH_KEY}" ]]; then
    RSYNC_SSH="${RSYNC_SSH} -i ${SSH_KEY} -o IdentitiesOnly=yes"
fi

echo "==> 1/4 Compilando assets localmente (el servidor no compila)"
cd "${ROOT_DIR}/portal"
npm ci
npm run build

echo "==> 2/5 Subiendo assets compilados"
rsync -avz --delete -e "${RSYNC_SSH}" \
    public/build/ "${SSH_USER}@${SSH_HOST}:${APP_DIR}/public/build/"

echo "==> 3/5 Verificando entorno de produccion"
"${SSH[@]}" "cd ${APP_DIR} && test \"\$(${PHP_BIN} artisan tinker --execute='echo app()->environment();')\" = production && test \"\$(${PHP_BIN} artisan tinker --execute='echo config(\"app.debug\") ? \"true\" : \"false\";')\" = false"

echo "==> 4/5 Respaldo de BD previo al deploy"
"${SSH[@]}" "cd ${APP_DIR} && ${PHP_BIN} artisan db:backup-predeploy"

echo "==> 5/5 Actualizando aplicación en el servidor"
"${SSH[@]}" bash -s <<EOF
set -euo pipefail
cd ${REPO_DIR}
${PHP_BIN} portal/artisan down --retry=30 || true
git fetch origin ${BRANCH}
git reset --hard origin/${BRANCH}
cd ${APP_DIR}
composer install --no-dev --optimize-autoloader --no-interaction
${PHP_BIN} artisan migrate --force
${PHP_BIN} artisan config:cache
${PHP_BIN} artisan route:cache
${PHP_BIN} artisan view:cache
${PHP_BIN} artisan up
EOF

echo "✔ Despliegue completado: https://registrocobaemario.ariocentro.com"

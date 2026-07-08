# 08 — Despliegue en Hostinger (plan compartido)

Producción: `https://registrocobaemario.ariocentro.com` · BD `u132762550_COBAEM` · Despliegue por SSH con llave.

---

## 1. Preparación (una sola vez)

### 1.1 Subdominio y SSL
1. hPanel → Dominios → crear subdominio `registrocobaemario` bajo `ariocentro.com`.
2. Activar SSL gratuito (Let's Encrypt) para el subdominio y "Forzar HTTPS".
   *Nota: el sitio debe operar en `https://`; la referencia `http://` del requerimiento queda solo como redirección.*

### 1.2 SSH con llave (el "certificado" de despliegue)
1. hPanel → Avanzado → Acceso SSH: habilitar; conexión base `ssh -p 65002 u132762550@46.202.183.138`.
2. Generar llave local: `ssh-keygen -t ed25519 -C "deploy-cobaem"` y registrar la pública en hPanel → SSH → Manage SSH keys.
   - Para este repo, la llave privada local usada por defecto es `cert/hostinger_antigua_ed25519`.
   - `cert/` está ignorado por git; no subir llaves ni certificados al repositorio.
   - En Windows, si OpenSSH marca `UNPROTECTED PRIVATE KEY FILE`, restringir permisos: `icacls cert\hostinger_antigua_ed25519 /inheritance:r /grant:r "%USERNAME%:R"`.
3. Probar con la llave privada local: `ssh -p 65002 -i cert/hostinger_antigua_ed25519 -o IdentitiesOnly=yes u132762550@46.202.183.138`.

### 1.3 Estructura en el servidor
Hostinger compartido no permite cambiar document root arbitrariamente, así que se usa el patrón app-fuera-del-webroot + symlink:

```
/home/u132762550/
├── apps/portal/                  # repositorio completo (git clone)
│   ├── deploy/
│   ├── docs/
│   └── portal/                   # aplicación Laravel
│       ├── .env                  # SOLO aquí: credenciales de producción
│       ├── public/ ...
└── domains/registrocobaemario.ariocentro.com/public_html/
        → symlink a /home/u132762550/apps/portal/portal/public
```

Si el panel no permite reemplazar `public_html` por symlink: alternativa soportada — dejar el subdominio apuntando a su carpeta, vaciar `public_html` y copiar ahí el contenido de `portal/public` ajustando `index.php` (última opción, documentar si se usa).

### 1.4 Base de datos
1. hPanel → Bases de datos: confirmar `u132762550_COBAEM`, crear usuario dedicado y contraseña fuerte, privilegios solo sobre esa BD.
2. Anotar host de MariaDB (usualmente `localhost` o el host que indique el panel).

### 1.5 `.env` de producción (crear a mano por SSH, nunca en git)
```env
APP_NAME="Portal Nuevo Ingreso COBAEM Ario"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://registrocobaemario.ariocentro.com
APP_LOCALE=es

DB_CONNECTION=mariadb
DB_HOST=localhost
DB_DATABASE=u132762550_COBAEM
DB_USERNAME=<usuario del panel>
DB_PASSWORD=<contraseña del panel>

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=local
PORTAL_AVISO_PRIVACIDAD_FECHA_PUBLICACION="fecha oficial fijada por el plantel"

OMR_SERVICE_URL=https://<host-servicio-omr>
OMR_SERVICE_KEY=<api key>

MAIL_MAILER=smtp   # SMTP de Hostinger si se usa correo
```

### 1.6 Cron (hPanel → Avanzado → Cron Jobs)
```
* * * * * cd /home/u132762550/apps/portal/portal && php artisan schedule:run >> /dev/null 2>&1
```
El scheduler ejecuta: `queue:work --stop-when-empty --max-time=50` cada minuto (jobs de CSV/OMR), `db:backup-predeploy --daily` a las 02:15, limpieza de sesiones. Sin workers persistentes (limitación del plan compartido).

## 2. Despliegue (cada release) — `deploy/deploy.sh`

Los assets se compilan **localmente** (no hay Node garantizado en el servidor): `npm run build` y el directorio `public/build` se versiona o se sube por rsync.

```bash
#!/usr/bin/env bash
set -euo pipefail
SERVER="u132762550@46.202.183.138 -p 65002"
REPO="/home/u132762550/apps/portal"
APP="$REPO/portal"

# 1. Build local de assets
cd portal
npm ci && npm run build
cd ..

# 2. Subir assets compilados
rsync -avz -e "ssh -p 65002 -i cert/hostinger_antigua_ed25519 -o IdentitiesOnly=yes" portal/public/build/ u132762550@46.202.183.138:$APP/public/build/

# 3. Actualizar código y dependencias en servidor
ssh -i cert/hostinger_antigua_ed25519 -o IdentitiesOnly=yes $SERVER bash -s <<'EOF'
cd /home/u132762550/apps/portal
php portal/artisan down --retry=30
git pull origin main
cd portal
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
EOF
```

Verificar versión de PHP CLI en servidor (`php -v`); si el CLI default no es 8.3 usar la ruta explícita (ej. `/usr/bin/php8.3` o el alias que indique Hostinger). El script falla si `APP_ENV` no es `production` o si `APP_DEBUG` no es `false`.

Antes de migrar, `deploy/deploy.sh` ejecuta `php artisan db:backup-predeploy`. El comando genera un dump MariaDB en `~/backups` con `mysqldump`, usa `DB_BACKUP_RETENTION=14` por defecto y respeta `DB_BACKUP_PATH`/`MYSQLDUMP_BIN` si Hostinger requiere rutas específicas.

El script `deploy/deploy.sh` toma esta llave por defecto. Si necesitas usar otra, ejecuta:

```bash
DEPLOY_SSH_KEY=/ruta/a/otra_llave ./deploy/deploy.sh
```

La conexión base del despliegue es `ssh -p 65002 u132762550@46.202.183.138`; el script agrega la llave privada local automáticamente. Si Hostinger cambia el host SSH, se puede sobreescribir con `DEPLOY_SSH_HOST`.

## 3. Primer despliegue

```bash
ssh -p 65002 -i cert/hostinger_antigua_ed25519 -o IdentitiesOnly=yes u132762550@46.202.183.138
cd ~/apps && git clone <repo> portal && cd portal/portal
composer install --no-dev --optimize-autoloader
cp .env.example .env   # editar con credenciales reales
php artisan key:generate
php artisan migrate --force --seed          # seeders: catálogos, plantel, ciclo, admin
php artisan storage:link
# crear symlink del subdominio → /home/u132762550/apps/portal/portal/public (ver 1.3)
```

## 4. Entornos

| Entorno | Dónde | BD | Rama |
|---|---|---|---|
| Local | Laravel Herd/Sail o XAMPP + MariaDB | `cobaem_portal_dev` | feature/* |
| Staging (opcional) | subcarpeta/subdominio `staging-registro...` | `u132762550_COBAEM_stg` (si el plan lo permite) | develop |
| Producción | `registrocobaemario.ariocentro.com` | `u132762550_COBAEM` | main |

Flujo git: feature branch → PR → main → `deploy.sh`. Etiquetar releases (`v1.0.0`).

## 5. Rollback

1. `php artisan down`
2. `git checkout <tag anterior>` + `composer install --no-dev`
3. Si hubo migración incompatible: restaurar dump de BD del backup previo al deploy generado por `php artisan db:backup-predeploy`.
4. `php artisan config:cache && php artisan up`

## 6. Monitoreo mínimo

- `php artisan schedule:list` para verificar tareas.
- Logs en `storage/logs/laravel.log` (rotación diaria, retención 14 días).
- Healthcheck simple `/up` (ruta health de Laravel) + monitor externo gratuito (UptimeRobot) apuntando al subdominio.
- Panel admin muestra: último backup, jobs fallidos (`failed_jobs`), estado del servicio OMR.

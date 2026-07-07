# Checklist de preparación en Hostinger (una sola vez)

Pasos manuales en hPanel antes del primer despliegue (detalle en `docs/08-despliegue-hostinger.md`).

## Subdominio y SSL
- [ ] Crear subdominio `registrocobaemario` en `ariocentro.com`
- [ ] Activar SSL (Let's Encrypt) para el subdominio
- [ ] Activar "Forzar HTTPS"

## SSH
- [ ] Habilitar acceso SSH: `ssh -p 65002 u132762550@46.202.183.138`
- [ ] Generar llave local: `ssh-keygen -t ed25519 -C "deploy-cobaem"`
- [ ] Registrar la llave pública en hPanel → SSH → Manage SSH keys
- [ ] Guardar la llave privada local en `cert/hostinger_antigua_ed25519` (`cert/` está ignorado por git)
- [ ] En Windows, corregir permisos si OpenSSH rechaza la llave: `icacls cert\hostinger_antigua_ed25519 /inheritance:r /grant:r "%USERNAME%:R"`
- [ ] Probar conexión con llave privada local: `ssh -p 65002 -i cert/hostinger_antigua_ed25519 -o IdentitiesOnly=yes u132762550@46.202.183.138`
- [ ] Verificar PHP CLI: `php -v` (debe ser 8.3; si no, ubicar binario 8.3)

## Base de datos
- [ ] Confirmar BD `u132762550_COBAEM`
- [ ] Crear usuario dedicado con contraseña fuerte (solo esta BD)
- [ ] Anotar host de MariaDB que indique el panel

## Estructura y aplicación
- [ ] `mkdir -p ~/apps ~/backups` y `git clone <repo> ~/apps/portal`
- [ ] Crear `~/apps/portal/portal/.env` a mano (plantilla en docs/08 §1.5) — nunca en git
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan key:generate`
- [ ] `php artisan migrate --force --seed`
- [ ] `php artisan storage:link`
- [ ] Apuntar el subdominio a `public/` (symlink, ver docs/08 §1.3)

## Cron
- [ ] Crear cron job (cada minuto):
      `cd /home/u132762550/apps/portal && php artisan schedule:run >> /dev/null 2>&1`

## Verificación final
- [ ] `https://registrocobaemario.ariocentro.com` carga la landing con candado SSL
- [ ] `https://registrocobaemario.ariocentro.com/up` responde OK
- [ ] `/admin/login` permite entrar con el usuario admin del seeder
- [ ] Cambiar la contraseña inicial del admin
- [ ] `APP_DEBUG=false` confirmado (una URL inexistente NO muestra stack trace)

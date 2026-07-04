# Puesta en marcha local (primera vez)

El esqueleto del proyecto ya está completo en esta carpeta. Falta instalar
dependencias (requiere PHP 8.3, Composer y Node 20+ en tu máquina).

## 1. Instalar dependencias

```bash
cd portal
composer install
npm install
```

**Nota sobre la versión de Laravel**: `composer.json` acepta `^12.0|^13.0` con
plataforma fijada a PHP 8.3. Composer instalará Laravel 13.2.x (la última
compatible con 8.3; las 13.3+ requieren PHP 8.4) o Laravel 12 si algún paquete
aún no soporta 13. Ambas funcionan con esta estructura.

## 2. Publicar migraciones de paquetes

```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --provider="Spatie\Activitylog\ActivitylogServiceProvider" --tag="activitylog-migrations"
```

## 3. Configurar entorno

```bash
cp .env.example .env
php artisan key:generate
# Crear BD local 'cobaem_portal_dev' en MariaDB y ajustar credenciales en .env
# Opcional: ADMIN_INITIAL_PASSWORD=... para fijar la contraseña del admin
```

## 4. Migrar y sembrar

```bash
php artisan migrate --seed
```

Crea: roles/permisos, catálogos base (incl. 32 entidades y municipios de
Michoacán), plantel ARIO, ciclo 2026 y el usuario
`admin@registrocobaemario.ariocentro.com` (la contraseña se muestra en consola
si no definiste `ADMIN_INITIAL_PASSWORD`).

## 5. Verificar

```bash
php artisan test        # deben pasar CurpValidator, FolioService y AccesoAdmin
npm run dev             # en una terminal
php artisan serve       # en otra; abrir http://localhost:8000 y /admin/login
```

## 6. Traducciones al español (recomendado)

```bash
composer require laravel-lang/common --dev
php artisan lang:add es
php artisan lang:update
```

## Producción

Ver `../deploy/hostinger-checklist.md` (preparación única) y
`../deploy/deploy.sh` (cada release). Documentación completa: `../docs/08`.

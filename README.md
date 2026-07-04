# Portal Académico de Nuevo Ingreso — COBAEM Plantel Ario de Rosales

Plataforma de autoservicio para alumnos de nuevo ingreso: registro digital, formato de inscripción en PDF, seguimiento de documentación, resultados de evaluación diagnóstica, curso propedéutico, grupo escolar, matrícula, horario y avisos. Reutilizable por ciclo escolar.

**Producción**: https://registrocobaemario.ariocentro.com

## Stack

- Laravel (PHP 8.3) · MariaDB · Blade + Livewire 3 + Tailwind CSS
- PDF: laravel-dompdf · CSV: league/csv · Permisos: spatie/laravel-permission · Auditoría: spatie/laravel-activitylog
- Servicio OMR externo: FastAPI + OpenCV (carpeta `omr-service/`)
- Hosting: Hostinger compartido (jobs por cron, sin workers persistentes)

## Documentación

| Documento | Contenido |
|---|---|
| `requerimientos_portal_academico_nuevo_ingreso_cobaem.md` | Requerimientos funcionales y no funcionales (fuente de verdad) |
| `docs/01-arquitectura.md` | Arquitectura, stack, decisiones (ADR), estructura de carpetas |
| `docs/02-modelo-datos.md` | Modelo de datos, tablas, unicidad, migraciones |
| `docs/03-modulos-roles-flujos.md` | Módulos, roles/permisos, flujos de alumno y admin |
| `docs/04-endpoints.md` | Rutas web y API |
| `docs/05-omr-servicio.md` | Diseño y contrato del microservicio OMR |
| `docs/06-csv-pdf.md` | Importación/exportación CSV y generador PDF |
| `docs/07-seguridad-auditoria.md` | Seguridad, privacidad, auditoría, respaldos |
| `docs/08-despliegue-hostinger.md` | Despliegue, entornos, rollback |
| `docs/09-plan-construccion.md` | Fases, backlog, estimaciones, riesgos, pendientes |
| `CLAUDE.md` | Contexto y convenciones para asistentes de IA |

## Desarrollo local

```bash
cd portal
composer install
cp .env.example .env && php artisan key:generate
# configurar BD local (MariaDB): cobaem_portal_dev
php artisan migrate --seed        # catálogos, plantel ARIO, ciclo, admin
npm install && npm run dev
php artisan serve
```

Usuario admin inicial: definido por el seeder (cambiar contraseña de inmediato).

## Pruebas

```bash
php artisan test
```

## Despliegue a producción

Por SSH con llave (ver `docs/08-despliegue-hostinger.md`):

```bash
./deploy/deploy.sh
```

Credenciales de BD (`u132762550_COBAEM`) y llaves solo en el `.env` del servidor — nunca en el repositorio.

## Estado del proyecto

Fase de diseño completada. Construcción por fases según `docs/09-plan-construccion.md`:
Fase 0 cimientos → Fase 1 MVP registro → Fase 2 evaluación → Fase 3 escolar → Fase 4 piloto OMR.

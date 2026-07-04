# AGENTS.md — Contexto para asistentes de IA

Portal Académico de Nuevo Ingreso, COBAEM Plantel Ario de Rosales. Registro digital de aspirantes, PDF de inscripción, seguimiento de documentación, resultados de evaluación diagnóstica y publicación de grupos/matrícula/horario. Multiciclo (reutilizable cada año escolar).

## Regla de oro

`requerimientos_portal_academico_nuevo_ingreso_cobaem.md` es la **fuente de verdad funcional**. Los documentos `docs/01..09` son el diseño técnico aprobado. Ante conflicto: requerimientos > docs > código. Si un cambio contradice el diseño, actualizar el doc correspondiente en el mismo PR.

## Stack y restricciones duras

- Laravel (PHP 8.3) + MariaDB + Blade/Livewire 3/Tailwind. **No introducir** SPA, Redis, workers persistentes, ni binarios: producción es **Hostinger compartido** (colas driver `database` vía cron, ver `docs/08`).
- OMR es un **servicio externo** (FastAPI/OpenCV, `omr-service/`); el portal solo lo consume por HTTP (`OmrClient`). El portal debe funcionar sin él (fallback CSV).
- Assets se compilan localmente (`npm run build`); el servidor no compila.

## Convenciones del proyecto

- **Idioma**: BD, rutas, vistas, mensajes y commits en **español**. Código (clases/métodos) en inglés con nombres de dominio en español permitidos (ej. `ProcesoIngreso`, `FolioService`).
- **BD**: tablas en español, snake_case, plural (`procesos_ingreso`, `hojas_respuesta`). Esquema completo en `docs/02-modelo-datos.md` — no inventar tablas; respetarlo o actualizarlo.
- **Modelos clave**: `Alumno` (identidad, CURP única) ≠ `ProcesoIngreso` (un registro por ciclo). Casi todo cuelga de `ProcesoIngreso`. Nunca mezclar datos entre ciclos.
- **Catálogos**: tabla genérica `catalogos` (tipo, clave, parent_id) para listas; tablas propias para entidades con comportamiento (ciclos, planteles, exámenes, grupos). No hardcodear valores catalogables.
- **Enums de app** en `app/Enums/` para estados (EstadoDocumento, EstadoProcesamiento, etc.).
- **Livewire** para formularios/CRUD; controladores solo para descargas de archivos y API OMR.
- Reglas de negocio configurables en `config/portal.php` (rangos de riesgo, formato de folio, columnas del export federal).

## Reglas de negocio críticas (no romper)

1. Folio interno: `NI-{AÑO}-{PLANTEL}-{consecutivo}`; generar SOLO vía `FolioService` (transacción + lock en `folio_secuencias`).
2. Unicidad: CURP global; (alumno, ciclo) único; (folio_examen, ciclo) único; matrícula única.
3. Importaciones CSV: llave CURP + ciclo, reglas de la §23.3 del requerimiento; siempre job encolado + registro en `importaciones_csv`.
4. Bloqueo de edición NUNCA bloquea consulta del alumno (RF-15/RNF-19).
5. Visibilidad de secciones del alumno controlada por `modulos_ciclo` (middleware `modulo.publicado`).
6. Respuestas OMR requieren validación humana antes de entrar a cálculo de resultados.
7. PDF: plantillas versionadas por ciclo (`resources/views/pdf/inscripcion/v{año}/`); no editar versiones de ciclos pasados.

## Seguridad (siempre)

- Alumno sin cuenta: sesión por CURP; secciones sensibles exigen segundo dato (fecha de nacimiento o folio). Consultas del alumno SIEMPRE filtradas por el proceso en sesión, nunca por parámetro de URL.
- Archivos sensibles en `storage/app/private`, servidos por controlador con Policy. Nada sensible en `public/`.
- `Cache-Control: no-store` en vistas con datos personales. "Recordar CURP" solo en localStorage con consentimiento.
- Cambios relevantes auditados (activitylog). Exportaciones CSV auditadas y con permiso.
- Jamás credenciales en código/commits: todo en `.env` (prod: BD `u132762550_COBAEM`).

## Comandos

```bash
# desarrollo (desde portal/)
php artisan serve · npm run dev · php artisan test
php artisan migrate:fresh --seed          # reset local con catálogos

# calidad
./vendor/bin/pint                          # estilo PHP
php artisan test --filter=NombreTest

# despliegue (desde raíz; ver docs/08)
./deploy/deploy.sh
```

## Pruebas

Feature tests obligatorios para: registro completo del alumno, acceso/verificación, generación de folio (concurrencia), importación CSV (reglas §23.3), bloqueo de edición, permisos por rol, cálculo de resultados. Datos de prueba: factories con CURPs sintéticas válidas (nunca CURPs reales en el repo).

## Mapa de documentación

- Arquitectura y ADRs: `docs/01` · Modelo de datos: `docs/02` · Roles/flujos: `docs/03`
- Rutas: `docs/04` · OMR: `docs/05` · CSV/PDF: `docs/06`
- Seguridad: `docs/07` · Despliegue: `docs/08` · Plan y backlog: `docs/09`

## Estado actual

Fases 0 y 1 construidas y verificadas (tests + pint, commits Fase 1.1–1.15 más guardas de escritura del alumno en `RegistroAlumnoService` y `BloqueoEscrituraAlumnoTest`). Fase 2 construida (commits Fase 2.1–2.9: evaluación, `CalculoResultadosService`, materiales, propedéutico, segunda evaluación, dashboard académico) y con revisión estática aprobada, pero **verificación ejecutable PENDIENTE**: el entorno local carecía de PHP 8.3/pdo_sqlite/mbstring. Existe workflow CI (`portal/.github/workflows/ci.yml`). NO declarar Fase 2 terminada ni desplegar hasta que `php artisan test` pase en PHP 8.3. Siguiente paso tras verificar: Fase 3 (`docs/09` §2). Pendientes con el plantel en `docs/09` §6; estructura provi
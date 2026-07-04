# Prompt de construcción — Fase 1 (Codex, modo Goal)

**Cómo usarlo**: abre Codex (app, CLI o extensión IDE, versión ≥ 0.128) en la
raíz del repositorio, escribe `/goal` y pega el bloque de abajo. Codex leerá
`AGENTS.md` automáticamente. Gestión: `/goal pause`, `/goal resume`, `/goal clear`.

**Prerrequisito**: haber completado `portal/SETUP.md` (composer install,
migrate --seed y `php artisan test` en verde). No lanzar el goal sobre un
entorno que no arranca.

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Construir la Fase 1 (MVP Registro) del Portal Académico de Nuevo Ingreso,
ejecutando en orden las tareas 1.1 a 1.15 del backlog en
docs/09-plan-construccion.md §2 "Fase 1". Trabaja dentro de portal/.
La Fase 0 ya está construida: NO rehagas cimientos (migraciones núcleo,
FolioService, CurpValidator, middleware, layouts, login admin ya existen).

CONDICIÓN DE TÉRMINO (el goal se cumple cuando TODO esto sea verdadero)

1. Los criterios de aceptación 1-13, 19 y 20 de la sección 31 de
   requerimientos_portal_academico_nuevo_ingreso_cobaem.md pasan mediante
   feature tests automatizados que los cubren explícitamente.
2. php artisan test pasa completo, sin tests saltados ni marcados incompletos.
3. php artisan migrate:fresh --seed termina sin errores.
4. ./vendor/bin/pint --test no reporta problemas.
5. Existe un commit por cada tarea del backlog (1.1 ... 1.15), mensajes en
   español, formato "Fase 1.N: descripción".

FUENTES DE VERDAD (leer antes de codificar; ante conflicto, este orden)

1. requerimientos_portal_academico_nuevo_ingreso_cobaem.md (funcional)
2. docs/02-modelo-datos.md (esquema exacto de BD: no inventes tablas ni
   columnas; si necesitas desviarte, actualiza el doc en el mismo commit)
3. docs/03-modulos-roles-flujos.md (flujos y niveles de acceso del alumno)
4. docs/04-endpoints.md (nombres de rutas), docs/06-csv-pdf.md (CSV y PDF)
5. AGENTS.md / CLAUDE.md (convenciones y reglas críticas, obligatorias)

PLAN DE HITOS (respetar orden y dependencias)

H1  Tarea 1.1: migraciones de docs/02 §2 (alumnos, procesos_ingreso,
    datos_contacto, familiares, otros_datos_alumno, documentos_alumno,
    modulos_ciclo, descargas_formato) + modelos Eloquent con relaciones,
    casts y activitylog + factories con CURPs SINTÉTICAS válidas.
H2  Tarea 1.3: acceso del alumno (landing CURP, verificación por segundo
    dato, sesión con alumno_proceso_id/alumno_ciclo_id/alumno_nivel_sensible,
    selector de ciclo si hay varios procesos, recordar CURP vía
    window.curpStorage con consentimiento, rate limiting 10/min).
H3  Tarea 1.4: asistente de registro Livewire de 6 pasos (campos y
    obligatoriedad EXACTOS de §8 del requerimiento; catálogos dependientes
    entidad→municipio; borrador por paso con estatus registro_incompleto;
    folio SOLO vía FolioService; aviso de privacidad obligatorio).
H4  Tarea 1.5: PDF de inscripción con dompdf, plantilla
    resources/views/pdf/inscripcion/v2026/ (secciones §9.2), descarga
    alumno (nivel sensible) y admin, registro en descargas_formato.
H5  Tarea 1.6: tablero "Mi proceso" (§7.4) + aplicación real del middleware
    modulo.publicado a las secciones + ruta alumno.verificacion.
H6  Tareas 1.7 y 1.8: panel admin de alumnos (búsqueda por CURP/nombre/
    folios/estatus/ciclo, detalle, edición con permisos, bloqueo de edición)
    y gestión de documentación (estados de §11.2, observaciones).
H7  Tareas 1.9 y 1.10: exportación CSV (streaming, auditada) e importación
    CSV de alumnos y documentación (vista previa, job encolado, reglas
    §23.3 con llave CURP+ciclo, reporte en importaciones_csv).
H8  Tareas 1.11-1.14: dashboard de registros (§10.2), avisos (CRUD +
    segmentación + leído), publicación de módulos por ciclo, vista de
    auditoría.
H9  Tarea 1.15: suite final que mapea 1:1 los criterios de aceptación,
    prueba de concurrencia de folios, revisión de textos en español y
    verificación móvil (viewport 360px en vistas del alumno).

RESTRICCIONES DURAS (romperlas = goal fallido aunque los tests pasen)

- Solo Blade + Livewire 3 + Tailwind; nada de Vue/React/Inertia/API pública.
- Nada de Redis, workers persistentes, paquetes con binarios ni servicios
  externos nuevos. Colas: driver database.
- Consultas del alumno SIEMPRE filtradas por la sesión, jamás por id/CURP en
  URL. Cache-Control: no-store en vistas con datos personales.
- BD en español snake_case plural; código de dominio según CLAUDE.md.
- Ningún dato real: CURPs de prueba sintéticas, sin credenciales en código.
- No modificar migraciones ya aplicadas de Fase 0; crear nuevas.

AUTOVERIFICACIÓN (después de CADA hito)

php artisan test && php artisan migrate:fresh --seed && ./vendor/bin/pint
Si algo falla, corrígelo antes de pasar al siguiente hito. Commit por tarea.

ESCALAR EN VEZ DE INVENTAR (pausar el goal y preguntar si ocurre)

- Texto legal del aviso de privacidad: usar el placeholder existente, NO
  redactar texto legal definitivo.
- Diseño visual exacto del formato oficial de inscripción: implementar la
  estructura de §9.2 con encabezado institucional genérico y dejar TODO
  claramente marcado para ajuste cuando el plantel entregue el formato.
- Cualquier ambigüedad entre requerimientos y docs que no puedas resolver
  con el orden de precedencia indicado.
```

---

## Después del goal

1. Revisar el diff completo commit por commit (especialmente seguridad de
   sesión del alumno y reglas de importación §23.3).
2. Correr la app localmente y hacer el recorrido manual: registro completo →
   PDF → reingreso con CURP → panel admin → export/import → bloqueo.
3. Actualizar CLAUDE.md/AGENTS.md ("Estado actual" → Fase 1 construida).
4. Desplegar a staging/producción con ./deploy/deploy.sh y repetir el
   recorrido en el servidor.

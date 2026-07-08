# Prompt de construcción — Endurecimiento 2: Robustez de datos, catálogos y pruebas (Codex, modo Goal)

**Cómo usarlo**: abre Codex (≥ 0.128) en la raíz del repositorio, escribe
`/goal` y pega el bloque de abajo.

**Prerrequisito**: **Endurecimiento 1** mergeado y CI en verde; árbol Git
limpio. Topología: repo en la RAÍZ, app Laravel en `portal/`.

**Origen**: Auditoría técnica integral (bloque B del roadmap, antes de
piloto). Cierra H-005 (import CSV robusto), H-008 (catálogos administrables),
H-010 (cobertura de pruebas), fidelidad del PDF y H-007 (IDOR menor de avisos).

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Hacer robustos los flujos de datos operados por control escolar del Portal
Académico de Nuevo Ingreso, cerrando en orden los hitos H1–H5 (hallazgos
H-005, H-008, H-010, H-007 y validación del PDF). La app Laravel vive en
portal/ (Git en la raíz). NO cambies el comportamiento funcional del MVP ni
lo endurecido en Endurecimiento 1; los tests existentes deben seguir pasando.

CONDICIÓN DE TÉRMINO (todo verdadero)

1. La importación CSV valida encabezados por tipo y RECHAZA el archivo si no
   coinciden; las filas de alumno sin campos mínimos reales se reportan como
   error (nunca se crean con placeholders "Alumno"/"Importado"/fecha ficticia)
   y el reporte por fila distingue creados/actualizados/omitidos/errores.
2. Catálogos administrables desde el panel: alta, edición, activar/inactivar,
   orden y dependencias entidad→municipio→localidad, sin perder integridad de
   registros que ya referencian un valor inactivado.
3. El PDF de inscripción v2026 está verificado contra el formato oficial
   (campos, folios, aviso, caracteres especiales y campos largos) por un test
   y una lista de verificación en docs/06.
4. marcarAviso valida que el aviso esté dirigido al alumno en sesión.
5. cd portal && php artisan test pasa completo con NUEVOS tests para: import
   CSV con encabezado inválido y filas incompletas, catálogos (inactivar sin
   romper histórico), render del PDF (status 200 + folios presentes) y
   segmentación de avisos. ./vendor/bin/pint --test limpio. Un commit por
   hito ("Endurecimiento 2.N: ..."). Push a origin main y CI en verde.

FUENTES DE VERDAD (en este orden ante conflicto)

1. requerimientos_portal_academico_nuevo_ingreso_cobaem.md (§23.3 reglas de
   importación CSV; catálogos §; formato PDF §)
2. docs/06-csv-pdf.md (pipeline import/export y plantilla PDF) y
   docs/02-modelo-datos.md (catalogos: tipo, clave, parent_id, activo, orden)
3. docs/03-modulos-roles-flujos.md (permisos: csv.importar,
   catalogos.administrar) y CLAUDE.md / AGENTS.md

PLAN DE HITOS

H1  Import CSV robusto (H-005). En app/Jobs/ProcesarImportacionCsv.php:
    valida encabezados esperados por tipo_importacion ANTES de procesar y
    aborta el archivo con estado "error" y motivo si no coinciden. Para
    alumnos: exige campos mínimos reales (curp válida, nombres, apellidos,
    fecha_nacimiento); si faltan, cuenta como error de fila, NO crees alumno
    con valores por defecto. Reporte por fila con categoría clara. Respeta la
    llave CURP+ciclo y las reglas §23.3. Preserva el patrón job encolado +
    importaciones_csv.
H2  Vista previa de import (si docs/06 la define): antes de confirmar, mostrar
    conteo de válidos/errores para que control escolar decida. Si ya existe,
    solo refuérzala; si no, impleméntala mínima sin romper el pipeline.
H3  Catálogos administrables (H-008). Amplía CatalogoController y su vista:
    editar, activar/inactivar (soft, sin borrar), reordenar y capturar
    parent_id para la cadena entidad→municipio→localidad. Los formularios del
    alumno deben seguir mostrando solo activos, pero un valor inactivado que
    ya esté referenciado por un proceso NO debe romper vistas ni exportes.
    Permiso catalogos.administrar. Sin hardcodear listas.
H4  PDF (fidelidad). Revisa resources/views/pdf/inscripcion/v2026/formato
    contra el formato oficial (docs/06): verifica todos los campos, folio de
    examen, folio interno, aviso/leyenda, y prueba con nombres largos y
    caracteres especiales (ñ, acentos, apóstrofos). Añade feature test que
    genere el PDF y afirme status y presencia de folios. NO edites plantillas
    de ciclos pasados; si el formato oficial exige cambios estructurales,
    versiona conforme a la regla de plantillas por ciclo y documenta.
H5  IDOR menor de avisos (H-007). En MiProcesoController::marcarAviso valida
    que el aviso corresponda a la segmentación del alumno (todos/ciclo/alumno)
    antes de marcar leído; test que lo cubra.

RESTRICCIONES DURAS (romperlas = goal fallido)

- Solo Blade + Livewire 3 + Tailwind. Importaciones SIEMPRE job encolado +
  registro en importaciones_csv. Sin binarios ni servicios nuevos.
- No mezclar datos entre ciclos; llave CURP+ciclo en importaciones.
- Esquema conforme a docs/02 (usa catalogos.activo/orden/parent_id
  existentes); toda migración nueva se documenta en el mismo commit. No
  editar migraciones ya commiteadas ni plantillas PDF de ciclos pasados.
- Exportaciones y catálogos con permiso y auditoría; textos y commits en
  español; CURPs de prueba sintéticas válidas.

AUTOVERIFICACIÓN (tras CADA hito)

cd portal && php artisan test && php artisan migrate:fresh --seed \
  && ./vendor/bin/pint
Corregir antes de avanzar. Commit por hito.

ESCALAR EN VEZ DE INVENTAR

- Estructura exacta de encabezados oficiales por tipo si docs/06 no la fija:
  usa las plantillas de CsvController::plantilla como contrato y documenta.
- Formato PDF oficial definitivo (pendiente docs/09 §6): si difiere del
  v2026 actual de forma estructural, pausa y pregunta antes de re-versionar.
- Cualquier conflicto requerimientos vs docs no resoluble por precedencia.
```

---

## Después del goal (rol coordinador/auditor)

1. Revisar diff commit por commit. Foco: que ninguna fila de alumno se cree
   con datos placeholder, y que inactivar un catálogo referenciado no rompa
   el detalle del alumno ni las exportaciones.
2. Verificación manual: importar un CSV con encabezado equivocado (debe
   rechazar), otro con una fila incompleta (debe reportar error, no crear);
   inactivar un municipio en uso y abrir un alumno que lo referencia;
   descargar el PDF de un alumno con nombre largo y acentos.
3. Comparar el PDF generado contra el formato físico oficial con el plantel.
4. Con E2 en verde, el sistema queda apto para **piloto controlado**.
   Continuar con **Endurecimiento 3** (calidad, CI/CD y datos) en segunda
   etapa.

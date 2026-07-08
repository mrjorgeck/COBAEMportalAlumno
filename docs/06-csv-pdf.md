# 06 — Importación/Exportación CSV y Generador PDF

---

## 1. CSV — principios generales

- Librería: `league/csv`. Codificación UTF-8 con BOM (compatibilidad Excel). Separador `,` (auto-detección de `;`).
- Las exportaciones neutralizan inyección de fórmulas: cualquier celda de texto libre que, tras espacios iniciales, comience con `=`, `+`, `-` o `@` se prefija de forma segura antes de enviarse a Excel/CSV.
- Toda importación corre como **job encolado** con procesamiento por lotes (chunks de 100 filas) y genera registro en `importaciones_csv` con reporte fila a fila (RNF-29).
- Plantillas CSV descargables desde el panel para cada tipo de importación (encabezados exactos + fila de ejemplo).
- Llave de actualización: **CURP + ciclo de ingreso** (reglas §23.3 del requerimiento, implementadas en `Services/Csv/`).

## 2. Tipos de importación

| Tipo | Llave | Efecto |
|---|---|---|
| `alumnos` | CURP + ciclo | Alta/actualización de alumno + proceso (reglas §23.3: crea alumno, crea proceso o actualiza; CURP inválida → error; folio examen duplicado en ciclo → alerta) |
| `documentacion` | CURP/folio + ciclo + tipo_documento | Actualiza estados de documentos |
| `resultados_examen` | folio_examen + examen | Resultados calculados externamente (puntaje/porcentaje por área) |
| `respuestas_examen` | folio_examen + examen | Respuestas crudas (fallback del OMR); dispara cálculo |
| `clave_respuestas` | examen + pregunta | Clave, respuestas válidas, área, materia, ponderación |
| `grupo_propedeutico` | CURP/folio + ciclo | Asignación de grupo |
| `grupo_escolar` | CURP/folio + ciclo | Asignación de grupo escolar |
| `matriculas` | CURP/folio + ciclo | Carga de matrícula (valida unicidad) |
| `horarios` | grupo + día + hora | Horario por grupo escolar |
| `catalogos` | tipo + clave | Alta/actualización de valores (CAT-05) |

Flujo de importación: subir archivo → validación de estructura (encabezados, tipos) → **vista previa** (primeras 20 filas + errores detectados) → confirmar → job → reporte (creados, actualizados, sin cambios, errores con motivo y nº de fila, descargable como CSV de errores).

### 2.1 Estructuras provisionales de Fase 2

Pendiente de confirmacion del formato federal real (docs/09 seccion 6.4), Fase 2 usa estas columnas:

- `clave_respuestas`: `examen_id,pregunta,respuesta_correcta,area_clave,materia_clave,competencia,ponderacion`. `respuesta_correcta` acepta una opción (`A`) o varias opciones válidas separadas por coma, diagonal o sin separador (`B,C`, `B/C`, `BC`); se normaliza a coma.
- `respuestas_examen`: `examen_id,folio_examen,1,2,3...N`, donde cada columna numerica representa la respuesta del alumno a esa pregunta.
- `resultados_examen`: `examen_id,folio_examen,puntaje_total,porcentaje_total,nivel_riesgo_clave,nivel_desempeno_clave` y, por cada area de catalogo, columnas opcionales `{AREA}_puntaje,{AREA}_porcentaje,{AREA}_riesgo,{AREA}_recomendacion`.
- `grupo_propedeutico`: `ciclo,curp,folio_examen,grupo`.

## 3. Tipos de exportación

| Exportación | Contenido | Permiso |
|---|---|---|
| Base de alumnos | Todos los campos del proceso + alumno + contacto + familiares, filtrable por ciclo/estatus/grupo/secundaria | csv.exportar |
| Documentación | Estado por documento por alumno | csv.exportar |
| Resultados | Resultados totales y por área, ambos exámenes | csv.exportar |
| Archivo enriquecido | Folio examen, CURP, nombre completo, datos requeridos + respuestas 1..N (RF-18, para plataforma federal; estructura de columnas configurable en `config/portal.php`) | csv.exportar |
| Catálogos | Por tipo | catalogos.administrar |
| Reporte de importación | Errores de una importación | csv.importar |

Exportaciones grandes: streaming (`streamDownload`) para no agotar memoria. Toda exportación se registra en auditoría (SEG-05, §25.3).

---

## 4. Generador PDF — formato de inscripción

- Librería: `barryvdh/laravel-dompdf` (HTML/CSS Blade → PDF). Suficiente para un formato de una hoja tamaño carta; sin dependencias binarias (compatible con hosting compartido).
- Plantillas **versionadas por ciclo**: `resources/views/pdf/inscripcion/v2026/formato.blade.php`. El proceso guarda `plantilla_pdf_version`; regenerar usa siempre su versión (consistencia histórica). Un nuevo formato oficial = nueva carpeta `v2027` sin tocar la anterior.
- Generación **on-demand** (no se almacena el PDF): se renderiza al descargar, con los datos vigentes. Si la edición está bloqueada el contenido ya es estable. Cada generación/descarga se registra en `descargas_formato` (§9.1).

### Secciones (espejo del formato físico, §9.2)
1. Encabezado institucional (logos COBAEM, periodo escolar, plantel y clave, folio de examen, folio interno).
2. Datos del estudiante. 3. Dirección. 4. Contacto. 5. Escuela de procedencia. 6. Tutor. 7. Madre. 8. Otros datos. 9. Aviso de privacidad (texto + fecha de aceptación). 10. Lugar y fecha, nombre completo y línea de firma.

### Reglas técnicas
- Hoja carta, márgenes 12 mm, fuente DejaVu Sans (acentos/ñ garantizados en dompdf).
- Campos vacíos se imprimen con línea en blanco (como el formato físico).
- Nombre de archivo: `{folio_registro}_{CURP}.pdf`.
- Descarga alumno: requiere sesión nivel sensible. Descarga admin: permiso `formatos.descargar`. Ambas auditadas.

### Checklist de verificación v2026

- Encabezado institucional: nombre COBAEM, plantel, clave del plantel y periodo escolar visibles.
- Folios: `folio_examen` impreso como No. de Ficha y `folio_registro` impreso en la traza del portal.
- Secciones: datos del estudiante, dirección, contacto, escuela de procedencia, tutor, madre, otros datos, aviso de privacidad, lugar/fecha y firma.
- Fidelidad de datos: CURP, nombre completo, tipo de estudiante, paraescolar, secundaria, beca y datos de contacto presentes.
- Caracteres especiales: la plantilla usa DejaVu Sans y el test cubre acentos, ñ y apóstrofos escapados en HTML.
- Campos largos: el test de `FormatoPdfInscripcionTest` usa nombres y secundaria largos para validar render sin perder contenido.
- Historial: no editar plantillas de ciclos cerrados; un cambio oficial estructural debe crear una nueva carpeta `v{año}`.

# 09 — Plan de Construcción

Alineado a la priorización §32 del requerimiento. Estimaciones en tallas (S ≈ ≤1 día, M ≈ 2-3 días, L ≈ 4-5 días) asumiendo un desarrollador con apoyo de IA.

---

## 1. Fases

| Fase | Objetivo | Duración estimada |
|---|---|---|
| 0 — Cimientos | Infraestructura, proyecto base, despliegue funcionando | 1 semana |
| 1 — MVP Registro | Alumno se registra, descarga PDF; panel de control escolar completo | 3-4 semanas |
| 2 — Evaluación | Resultados, áreas de mejora, materiales, propedéutico, comparativo | 2-3 semanas |
| 3 — Escolar | Grupo escolar, matrícula, horario, SICOBaEM, reportes avanzados | 1-2 semanas |
| 4 — Piloto OMR | Servicio externo + integración + revisión manual | 2-3 semanas (paralelizable) |

**Hito crítico de calendario**: la Fase 1 debe estar en producción antes de la fecha del examen diagnóstico del ciclo 2026-2027 (el alumno se registra al salir del examen). Confirmar fecha con el plantel y planear hacia atrás.

## 2. Backlog priorizado

### Fase 0 — Cimientos
| # | Tarea | Talla |
|---|---|---|
| 0.1 | Crear proyecto Laravel (composer acepta `^12\|^13` con plataforma PHP 8.3: Laravel 13.3+ requiere 8.4), auth propia mínima, Livewire, Tailwind, spatie/permission, activitylog, dompdf, league/csv | M |
| 0.2 | Configurar subdominio, SSL, SSH con llave, BD, cron en Hostinger | S |
| 0.3 | Script `deploy.sh` + primer despliegue "hola mundo" en producción | S |
| 0.4 | Migraciones núcleo: catalogos, ciclos, planteles, users/roles + seeders (entidades, municipios Michoacán, catálogos base, plantel ARIO, ciclo 2026, admin) | M |
| 0.5 | Layouts base: alumno (mobile-first) y admin; identidad institucional | M |

### Fase 1 — MVP Registro (criterios de aceptación §31: 1-13, 19, 20)
| # | Tarea | Talla | Depende |
|---|---|---|---|
| 1.1 | Migraciones: alumnos, folio_secuencias, procesos_ingreso, datos_contacto, familiares, otros_datos_alumno, documentos_alumno, modulos_ciclo, descargas_formato | M | 0.4 |
| 1.2 | `CurpValidator` (regex + dígito verificador) y `FolioService` con tests | S | 1.1 |
| 1.3 | Acceso alumno: landing CURP, verificación segundo dato, sesión, selector de ciclo, recordar CURP (localStorage + consentimiento) | M | 1.1 |
| 1.4 | Asistente de registro multipaso (6 pasos, borrador, catálogos dependientes, validaciones) | L | 1.2, 1.3 |
| 1.5 | Generador PDF v2026 + descargas auditadas (alumno y admin) | M | 1.4 |
| 1.6 | "Mi proceso": tablero de estados + middleware modulo.publicado + mensajes "no disponible" | M | 1.4 |
| 1.7 | Panel admin: búsqueda/listado de alumnos con filtros, detalle, edición, bloqueo de edición | L | 1.1 |
| 1.8 | Documentación: gestión admin de estados + vista alumno | M | 1.7 |
| 1.9 | Exportación CSV (base de alumnos, documentación) con streaming y auditoría | M | 1.7 |
| 1.10 | Importación CSV `alumnos` y `documentacion`: vista previa, job, reglas CURP+ciclo, reporte | L | 1.9 |
| 1.11 | Dashboard de registros (indicadores §10.2) | M | 1.7 |
| 1.12 | Avisos: CRUD, segmentación, vista alumno, marca de leído | M | 1.6 |
| 1.13 | Publicación de módulos por ciclo (UI admin) | S | 1.6 |
| 1.14 | Auditoría: activitylog en modelos + vista admin | S | 1.7 |
| 1.15 | Pruebas Feature del flujo completo + prueba con datos reales anonimizados + UAT con control escolar | M | todo |

### Fase 2 — Evaluación (criterios §31: 14-17)
| # | Tarea | Talla |
|---|---|---|
| 2.1 | Migraciones: examenes, claves_respuesta, resultados, resultados_area (+ plantillas_omr, hojas_respuesta, respuestas para fase 4) | M |
| 2.2 | Carga de clave de respuestas (CSV) + CRUD exámenes | M |
| 2.3 | Importación de resultados y de respuestas crudas; `CalculoResultadosService` (puntajes, áreas, niveles de riesgo configurables) | L |
| 2.4 | Vista alumno: resultados, áreas de mejora, recomendaciones | M |
| 2.5 | Materiales recomendados: CRUD + vista alumno por nivel/área | M |
| 2.6 | Grupos propedéuticos: CRUD, asignación (individual + CSV), vista alumno | M |
| 2.7 | Segunda evaluación + comparativo (alumno y dashboard) | M |
| 2.8 | Dashboard académico (indicadores §24, vistas por rol, gráficas Chart.js, caché) | L |

### Fase 3 — Escolar (criterio §31: 18)
| # | Tarea | Talla |
|---|---|---|
| 3.1 | Grupos escolares + horarios: CRUD, carga CSV, vista alumno | M |
| 3.2 | Matrículas: carga, unicidad, vista alumno | S |
| 3.3 | SICOBaEM: configuración por ciclo + vista alumno | S |
| 3.4 | Regularización (placeholder §15.2) + exportaciones/reportes restantes | S |
| 3.5 | Cierre de ciclo y prueba de reutilización: crear ciclo 2027 de prueba, verificar aislamiento histórico | M |

### Fase 4 — Piloto OMR (paralelizable desde Fase 2)
| # | Tarea | Talla |
|---|---|---|
| 4.1 | Diseño de hoja de respuestas con fiduciales + plantilla JSON | M |
| 4.2 | Servicio FastAPI + pipeline OpenCV + tests con imágenes reales | L |
| 4.3 | Despliegue del servicio (VPS/Docker) + API key + healthcheck | S |
| 4.4 | Portal: subida de hojas, `OmrClient`, job, estados | M |
| 4.5 | Cola de revisión manual (imagen vs respuestas, corrección, validación) | L |
| 4.6 | Vinculación por folio, exportación enriquecida, reporte de hojas | M |
| 4.7 | Piloto con ~30 hojas reales → medir precisión vs criterios (05-omr §6) | M |

## 3. Definición de terminado (DoD) por tarea

Código revisado, migraciones reversibles, validaciones server-side, pruebas Feature de la ruta feliz + errores clave, textos en español revisados, funciona en móvil (viewport 360px), sin datos sensibles en logs, desplegable con `deploy.sh`.

## 4. Criterios de aceptación del MVP

Los 20 puntos de §31 del requerimiento son el contrato de aceptación de las Fases 0-3. El OMR (Fase 4) tiene criterios propios en `05-omr-servicio.md` §6 y no bloquea la aceptación del MVP.

## 5. Riesgos técnicos (adicionales a §33 del requerimiento)

| Riesgo | Mitigación |
|---|---|
| Laravel 13 incompatible con PHP 8.3 de Hostinger | Verificar en tarea 0.1; usar Laravel 12.x sin cambio de diseño |
| Límites de CPU/memoria en compartido durante importaciones | Jobs por lotes de 100 filas; `max-time` en queue:work |
| Symlink de subdominio no permitido | Alternativas documentadas en 08-despliegue §1.3 |
| Pico de registro simultáneo al terminar el examen (~300 alumnos en 1-2 h) | Prueba de carga previa; páginas ligeras; sesión en BD; considerar escalonar por grupos |
| Servicio OMR sin infraestructura definida (VPS pendiente) | El MVP no depende de él; decidir hosting del servicio al iniciar Fase 4 |
| Cambio del formato oficial de inscripción | Plantillas PDF versionadas (ADR-07) |
| Personal no técnico administrando catálogos | UI simple + import CSV + capacitación en UAT |

## 6. Pendientes por definir con el plantel

1. Fecha del examen diagnóstico (fija el deadline de Fase 1).
2. Texto legal del aviso de privacidad.
3. Formato físico oficial de inscripción (para la plantilla PDF v2026) y hoja de respuestas real (para plantilla OMR).
4. Estructura exacta del archivo de carga masiva de la plataforma federal (columnas del "archivo enriquecido").
5. Hosting del servicio OMR (VPS propio, PC del plantel, u otro).
6. Reglas de generación de matrícula (¿la genera el portal o se importa de SICOBaEM?).

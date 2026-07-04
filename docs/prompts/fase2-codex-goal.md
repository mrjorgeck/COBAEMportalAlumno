# Prompt de construcción — Fase 2 (Codex, modo Goal)

**Cómo usarlo**: abre Codex (≥ 0.128) en la raíz del repositorio, escribe
`/goal` y pega el bloque de abajo. Codex leerá `AGENTS.md` automáticamente.

**Prerrequisito**: Fase 1 verificada (`php artisan test` en verde, incluido
`BloqueoEscrituraAlumnoTest`) y árbol Git limpio.

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Construir la Fase 2 (Evaluación y resultados) del Portal Académico de Nuevo
Ingreso, ejecutando en orden las tareas 2.1 a 2.8 del backlog en
docs/09-plan-construccion.md §2 "Fase 2". Trabaja dentro de portal/.
Las Fases 0 y 1 están construidas y en verde: NO modifiques su comportamiento
(los 33 tests existentes deben seguir pasando sin editarlos, salvo que un
test contradiga los requerimientos, en cuyo caso documenta el porqué en el
commit).

CONDICIÓN DE TÉRMINO (el goal se cumple cuando TODO esto sea verdadero)

1. Los criterios de aceptación 14, 15, 16 y 17 de la sección 31 de
   requerimientos_portal_academico_nuevo_ingreso_cobaem.md pasan mediante
   feature tests que los cubren explícitamente, más tests para: cálculo de
   resultados por área (RF-12), comparativo evaluación 1 vs 2 (RF-13, RF-27)
   y visibilidad por modulos_ciclo de las nuevas secciones del alumno.
2. php artisan test pasa completo (suite previa + nueva), sin tests saltados.
3. php artisan migrate:fresh --seed termina sin errores.
4. ./vendor/bin/pint --test no reporta problemas.
5. Un commit por tarea: "Fase 2.N: descripción" (en español).

FUENTES DE VERDAD (en este orden ante conflicto)

1. requerimientos_portal_academico_nuevo_ingreso_cobaem.md
   (§13 evaluación, §14 resultados alumno, §15 materiales, §16 propedéutico,
   §17 segunda evaluación, §24 dashboard académico)
2. docs/02-modelo-datos.md §3 y §4 (esquema EXACTO: examenes,
   claves_respuesta, resultados, resultados_area, grupos_propedeuticos;
   también crea ya hojas_respuesta, respuestas y plantillas_omr aunque la
   lógica OMR sea de Fase 4 — solo tablas y modelos)
3. docs/03-modulos-roles-flujos.md (§5 flujos de coordinación, §7 dashboards)
4. docs/06-csv-pdf.md §2 (importaciones: clave_respuestas, resultados_examen,
   respuestas_examen, grupo_propedeutico) y AGENTS.md / CLAUDE.md

PLAN DE HITOS (respetar orden)

H1  Tarea 2.1: migraciones de docs/02 §3 (examenes, claves_respuesta,
    resultados, resultados_area + plantillas_omr, hojas_respuesta,
    respuestas) y §4 (grupos_propedeuticos) + modelos con relaciones,
    casts, activitylog y factories. Respetar unicidades:
    (examen_id, pregunta), (proceso_ingreso_id, examen_id),
    (resultado_id, area_id), (ciclo_ingreso_id, nombre).
H2  Tarea 2.2: CRUD de exámenes (tipos: diagnostico_inicial,
    evaluacion_posterior; permiso resultados.cargar) + importación CSV de
    clave de respuestas (tipo clave_respuestas, plantilla descargable,
    vista previa, job, reporte en importaciones_csv como en Fase 1).
H3  Tarea 2.3: CalculoResultadosService — a partir de respuestas validadas
    o de un CSV de respuestas crudas (tipo respuestas_examen, llave
    folio_examen + examen), calcula puntaje y porcentaje total y por área
    usando ponderacion de claves_respuesta; asigna nivel_riesgo leyendo los
    rangos del catálogo nivel_riesgo (metadata min/max — NUNCA hardcodear);
    también soporta importación directa de resultados ya calculados (tipo
    resultados_examen). Registrar origen (calculado|importado). Recalculable
    de forma idempotente (updateOrCreate por proceso+examen).
H4  Tarea 2.4: secciones del alumno "Evaluación diagnóstica" (resultado
    general, por área, nivel de desempeño) y "Áreas de mejora"
    (áreas con riesgo alto/crítico + recomendaciones §14), gobernadas por
    modulos_ciclo (resultados, areas_mejora) y nivel de sesión sensible.
H5  Tarea 2.5: materiales_recomendados (tabla docs/02 §5): CRUD admin
    (permiso catalogos.administrar o resultados.cargar según docs/03) +
    sección del alumno "Materiales recomendados" filtrada por sus áreas
    débiles y nivel de desempeño; módulo materiales en modulos_ciclo.
H6  Tarea 2.6: grupos propedéuticos: CRUD (permiso grupos.asignar),
    asignación individual desde el detalle del alumno e importación CSV
    (tipo grupo_propedeutico), y sección del alumno "Curso propedéutico"
    (formato §16.2) con módulo propedeutico.
H7  Tarea 2.7: segunda evaluación (examen tipo evaluacion_posterior sobre el
    mismo proceso) + sección "Mi avance": comparativo por área y total con
    diferencia en puntos (formato §17.2), módulos evaluacion_posterior y
    avance.
H8  Tarea 2.8: dashboard académico (/admin/dashboard-academico, permiso
    dashboard.academico): indicadores de §24.2 con filtros por ciclo y
    examen; vistas por rol de §24.3 (dirección, académica, propedéutico);
    gráficas con Chart.js desde CDN cloudflare; consultas agregadas con
    cache de 10 minutos (store database) e invalidación al recalcular.
H9  Cierre: suite de aceptación Fase2AcceptanceTest (criterios 14-17 + los
    tests de la condición de término), revisión de textos en español,
    verificación móvil de las nuevas vistas del alumno (360px), pint.

RESTRICCIONES DURAS (romperlas = goal fallido aunque los tests pasen)

- Solo Blade + Livewire 3 + Tailwind; Chart.js permitido vía CDN. Nada de
  SPA, Redis, workers persistentes ni paquetes con binarios.
- Consultas del alumno SIEMPRE por sesión (alumno_proceso_id); un alumno
  jamás puede ver resultados de otro (SEG-03). Cache-Control: no-store.
- Los resultados NUNCA se muestran al alumno si el módulo no está publicado
  en modulos_ciclo, aunque existan en BD.
- Rangos de riesgo, áreas y niveles vienen de catálogos (metadata), no de
  constantes en código. config/portal.php solo como fallback documentado.
- Esquema de BD conforme a docs/02; si necesitas desviarte, actualiza
  docs/02-modelo-datos.md en el mismo commit explicando el cambio.
- No editar migraciones ya commiteadas; solo migraciones nuevas.
- CURPs y datos de prueba sintéticos. Sin credenciales en código.

AUTOVERIFICACIÓN (después de CADA hito)

php artisan test && php artisan migrate:fresh --seed && ./vendor/bin/pint
Corregir antes de avanzar. Commit por tarea.

ESCALAR EN VEZ DE INVENTAR (pausar el goal y preguntar)

- Estructura oficial del CSV de resultados de la plataforma federal: si el
  formato real no está definido (pendiente docs/09 §6.4), define una
  estructura propia documentada en docs/06-csv-pdf.md y márcala como
  provisional.
- Número de preguntas y áreas del examen 2026: usar catálogos sembrados y
  factories; no asumir un examen específico en código.
- Cualquier conflicto requerimientos vs docs no resoluble por precedencia.
```

---

## Después del goal

1. Revisar diff commit por commit; foco en: aislamiento por sesión en las
   nuevas secciones del alumno, idempotencia del recálculo y que los rangos
   de riesgo se lean del catálogo.
2. Recorrido manual: cargar clave CSV → importar respuestas → calcular →
   publicar módulo resultados → verlos como alumno → asignar grupo
   propedéutico → cargar segunda evaluación → comparativo → dashboard.
3. Actualizar "Estado actual" en CLAUDE.md y AGENTS.md (Fase 2 construida).
4. Desplegar con ./deploy/deploy.sh y repetir el recorrido en producción.

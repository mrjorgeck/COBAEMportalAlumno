# Prompt de construcción — Fase 3 (Codex, modo Goal)

**Cómo usarlo**: abre Codex (≥ 0.128) en la raíz del repositorio, escribe
`/goal` y pega el bloque de abajo.

**Prerrequisito**: Fases 0-2 verificadas (CI en verde) y árbol Git limpio.
Nota de topología: el repo vive en la RAÍZ; la app Laravel está en `portal/`.

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Construir la Fase 3 (Grupo escolar, matrícula, horario y SICOBaEM) del
Portal Académico de Nuevo Ingreso, ejecutando en orden las tareas 3.1 a 3.5
del backlog en docs/09-plan-construccion.md §2 "Fase 3". La app Laravel vive
en portal/ (el repo Git está en la raíz del proyecto). Las Fases 0-2 están
verificadas en CI: NO alteres su comportamiento; los tests existentes deben
seguir pasando sin editarlos.

CONDICIÓN DE TÉRMINO (todo verdadero)

1. El criterio de aceptación 18 de §31 del requerimiento pasa por feature
   tests explícitos, más tests para: unicidad de matrícula (RF-29, §22.4),
   horario mostrado según el grupo escolar del alumno (RF-30), instrucciones
   SICOBaEM administrables por ciclo (RF-31), y prueba de reutilización
   multiciclo (tarea 3.5: crear ciclo 2027 y verificar aislamiento total de
   datos históricos — un alumno del ciclo 2026 no ve nada del 2027 y
   viceversa; RF-39/40, RNF-24).
2. cd portal && php artisan test pasa completo, sin tests saltados.
3. cd portal && php artisan migrate:fresh --seed sin errores.
4. cd portal && ./vendor/bin/pint --test limpio.
5. Un commit por tarea: "Fase 3.N: descripción" (en español). Push a origin
   main y CI de GitHub en verde como verificación final.

FUENTES DE VERDAD (en este orden ante conflicto)

1. requerimientos_portal_academico_nuevo_ingreso_cobaem.md
   (§18 grupo/matrícula/horario, §19 SICOBaEM, §22 identificadores/ciclos)
2. docs/02-modelo-datos.md §4 (esquema EXACTO: grupos_escolares, horarios,
   sicobaem_config) y §5 (regularizacion_alumno)
3. docs/03-modulos-roles-flujos.md y docs/06-csv-pdf.md §2 (importaciones:
   grupo_escolar, matriculas, horarios)
4. AGENTS.md / CLAUDE.md (convenciones y reglas críticas)

PLAN DE HITOS

H1  Tarea 3.1a: migraciones de docs/02 §4 (grupos_escolares, horarios,
    sicobaem_config) + regularizacion_alumno de §5 + modelos con
    relaciones, casts, activitylog y factories. Conectar los FK ya
    existentes de procesos_ingreso (grupo_escolar_id) con constraint real
    si quedó como unsignedBigInteger suelto.
H2  Tarea 3.1b: grupos escolares y horarios: CRUD admin (permiso
    grupos.asignar), asignación individual desde el detalle del alumno,
    importación CSV (tipos grupo_escolar y horarios, mismo pipeline de
    vista previa + job + reporte), y secciones del alumno "Grupo escolar"
    y "Horario" (horario del grupo del alumno, §18.2) con módulos
    grupo_escolar y horario en modulos_ciclo.
H3  Tarea 3.2: matrículas: captura individual e importación CSV (tipo
    matriculas), validando unicidad global y auditando el cambio; sección
    del alumno "Matrícula" (módulo matricula). La matrícula se IMPORTA de
    SICOBaEM, no se genera (pendiente docs/09 §6.6: si encuentras
    ambigüedad, implementa solo importación/captura y documenta).
H4  Tarea 3.3: SICOBaEM: configuración por ciclo (sicobaem_config, permiso
    modulos.publicar): URL, fecha de disponibilidad, pasos de activación,
    contacto y mensaje (§19); sección del alumno con módulo sicobaem.
H5  Tarea 3.4: sección "Regularización autodirigida" del alumno como
    placeholder funcional (§15.2, tabla regularizacion_alumno) con módulo
    regularizacion; exportaciones CSV restantes de docs/06 §3 que falten
    (documentación, resultados) con streaming y auditoría.
H6  Tarea 3.5: cierre y reutilización multiciclo: comando o UI admin para
    crear el siguiente ciclo (2027) con sus modulos_ciclo en estado inicial
    de §27; feature test de aislamiento histórico (dos ciclos con datos,
    verificar folios independientes, consultas del alumno limitadas a su
    proceso/ciclo en sesión, dashboards filtrados por ciclo).
H7  Cierre: Fase3AcceptanceTest, textos en español, vistas del alumno en
    360px, pint, push y CI en verde.

RESTRICCIONES DURAS (romperlas = goal fallido)

- Solo Blade + Livewire 3 + Tailwind. Nada de SPA, Redis, workers
  persistentes ni binarios.
- Consultas del alumno SIEMPRE por sesión; matrícula/horario/grupo son
  datos sensibles: exigir nivel de sesión sensible y Cache-Control:
  no-store como en las secciones existentes.
- Nada visible para el alumno si su módulo no está publicado en
  modulos_ciclo, aunque el dato exista en BD.
- Matrícula única global (constraint + validación + reporte en import).
- Esquema conforme a docs/02; desviaciones = actualizar el doc en el mismo
  commit. No editar migraciones ya commiteadas.
- Datos de prueba sintéticos; sin credenciales en código.

AUTOVERIFICACIÓN (tras CADA hito)

cd portal && php artisan test && php artisan migrate:fresh --seed \
  && ./vendor/bin/pint
Corregir antes de avanzar. Commit por tarea.

ESCALAR EN VEZ DE INVENTAR

- Contenido real de las instrucciones SICOBaEM del ciclo 2026: usar texto
  de ejemplo de §19 marcado como provisional (lo administra el plantel).
- Reglas de generación de matrícula si algo sugiere que el portal debe
  generarla (pendiente docs/09 §6.6): NO la generes; pausa y pregunta.
- Cualquier conflicto requerimientos vs docs no resoluble por precedencia.
```

---

## Después del goal

1. Revisar diff commit por commit; foco en: aislamiento multiciclo (3.5),
   unicidad de matrícula y gating de módulos.
2. Recorrido manual: asignar grupo escolar → cargar horario CSV → cargar
   matrículas → configurar SICOBaEM → publicar módulos → verlo como alumno
   → crear ciclo 2027 y confirmar que el portal queda listo para reuso.
3. Actualizar "Estado actual" en CLAUDE.md y AGENTS.md.
4. Con Fase 3 en verde el MVP completo (§31: 20 criterios) queda cubierto:
   sigue el despliegue a producción (deploy/hostinger-checklist.md +
   ./deploy/deploy.sh) y después el piloto OMR (Fase 4).

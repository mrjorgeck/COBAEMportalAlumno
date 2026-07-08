# Reporte técnico — Metodología de desarrollo asistido por agentes de IA
**Caso: Portal Académico de Nuevo Ingreso, COBAEM Plantel Ario de Rosales (México)**
Repositorio: https://github.com/mrjorgeck/COBAEMportalAlumno · Producción: https://registrocobaemario.ariocentro.com

---

## 1. Resumen ejecutivo

Un sistema web completo de gestión de nuevo ingreso escolar — registro de aspirantes por CURP, generación de PDF de inscripción, seguimiento de documentación, resultados de evaluación diagnóstica, grupos, matrícula, horarios y dashboards — pasó **de documento de requerimientos a producción en 4 días calendario** (3 al 6 de julio de 2026), con aproximadamente **8 horas reales de trabajo orquestado**, operado por **una sola persona** que coordinó dos agentes de IA con roles diferenciados, sin escribir código manualmente en el camino crítico.

El resultado no es un prototipo: es una aplicación Laravel con 43 pruebas automatizadas que mapean criterios de aceptación formales, CI en GitHub Actions, auditoría de cambios, control de acceso por roles, y una fase de UX específica para su usuario real (adolescentes con celulares básicos y baja experiencia digital).

## 2. La solución (qué se construyó)

| Dimensión | Detalle |
|---|---|
| Dominio | Proceso de ingreso escolar: registro digital, formato PDF oficial, documentación, evaluación diagnóstica (con OMR futuro), curso propedéutico, comparativo de avance, grupo/matrícula/horario, avisos |
| Usuarios | Alumnos (~300/ciclo, acceso por CURP sin contraseña), control escolar, coordinación académica, dirección, técnico, admin |
| Stack | Laravel (PHP 8.3) · MariaDB · Blade + Livewire 3 + Tailwind · sin SPA |
| Infraestructura | Hosting compartido (Hostinger): colas por cron, assets compilados localmente, despliegue por SSH con script |
| Requerimientos | 40 funcionales + 30 no funcionales + 20 criterios de aceptación formales (`requerimientos_portal_academico_nuevo_ingreso_cobaem.md`, §29-31) |

Restricción rectora: debía ser **operable y mantenible por personal no técnico de un plantel público** con recursos limitados. Esa restricción explica la mayoría de las decisiones de arquitectura.

## 3. Decisiones de arquitectura (registradas como ADRs en `docs/01`)

1. **Monolito modular**, no microservicios: 300 usuarios/ciclo y equipo mínimo no justifican complejidad distribuida. La única excepción es el OMR (lectura óptica de exámenes), diseñado como servicio externo con contrato REST y *fallback* CSV — el portal funciona sin él.
2. **Alumno sin cuenta**: acceso por CURP + segundo dato para secciones sensibles. Cero fricción de contraseñas para el usuario menos digital.
3. **Separación identidad/proceso** (`alumnos` ≠ `procesos_ingreso`): el portal es multiciclo por diseño; el aislamiento histórico 2026/2027 se probó con tests antes de necesitarse.
4. **Catálogos híbridos**: tabla genérica administrable para listas (32 entidades, 113 municipios, dependencias entidad→municipio→localidad) + tablas propias para entidades con comportamiento. Reglas de negocio variables (rangos de riesgo) viven en metadata de catálogo, nunca en código.
5. **Diseñado para el hosting real**: driver de colas `database` + cron (no hay workers en compartido), PDF con dompdf (sin binarios), plantillas PDF versionadas por ciclo para sobrevivir cambios del formato oficial.

## 4. La metodología: tres roles, un contrato

El modelo de trabajo separa tres responsabilidades que tradicionalmente ejecuta un equipo:

| Rol | Quién | Responsabilidad |
|---|---|---|
| **Orquestador** (humano) | 1 persona | Decisiones de producto, lanzar/supervisar agentes, gestionar pendientes institucionales, aprobar cada fase |
| **Arquitecto / revisor de calidad** (agente A — Claude) | IA | Diseño técnico (9 documentos), redacción de los *prompts de construcción* por fase, revisión estática de cada entrega contra reglas críticas, auditoría continua |
| **Constructor** (agente B — Codex, modo `/goal`) | IA | Ejecución autónoma de cada fase: código, migraciones, tests, commits, hasta cumplir una condición de término verificable |

El **contrato** entre los tres es un conjunto de artefactos versionados en el repo:

- `requerimientos_*.md` — fuente de verdad funcional (regla: requerimientos > docs > código).
- `docs/01..09` — diseño técnico aprobado: arquitectura, modelo de datos, roles/flujos, endpoints, OMR, CSV/PDF, seguridad, despliegue, plan de construcción con backlog estimado.
- `CLAUDE.md` / `AGENTS.md` — instrucciones operativas para cualquier agente: convenciones, reglas de negocio críticas "no romper", seguridad, comandos, estado actual del proyecto. Es la memoria compartida del proyecto.
- `docs/prompts/` — los prompts de cada fase, versionados como artefactos de ingeniería.

### Anatomía de un prompt de construcción (ver `docs/prompts/fase1-codex-goal.md`)

Cada fase se lanza con un prompt que contiene: **objetivo acotado** (tareas numeradas del backlog), **condición de término verificable** (criterios de aceptación cubiertos por tests + suite en verde + linter limpio + un commit por tarea), **fuentes de verdad con orden de precedencia**, **hitos ordenados con dependencias**, **restricciones duras** (romperlas invalida el goal aunque los tests pasen), **autoverificación** tras cada hito, y — la pieza clave — una sección de **"escalar en vez de inventar"**: la lista de decisiones donde el agente debe detenerse y preguntar (textos legales, formatos oficiales, reglas institucionales pendientes). Esto último evitó, por ejemplo, que el agente redactara un aviso de privacidad inventado o generara matrículas que institucionalmente vienen de otro sistema.

## 5. Prácticas de ingeniería aplicadas

- **Fases con criterios de aceptación formales**: los 20 criterios del §31 del requerimiento se convirtieron en suites de aceptación (`Fase1AcceptanceTest` … `Fase3AcceptanceTest`, `UxAcceptanceTest`) que los citan explícitamente.
- **Un commit por tarea del backlog**, en español, con historia lineal sin reescritura (los errores se corrigen con commits `fixup` hacia adelante, visibles).
- **CI obligatorio** (GitHub Actions, PHP 8.3): tests + Pint en cada push; ninguna fase se declaró terminada sin CI en verde.
- **Revisión cruzada entre agentes**: cada entrega del constructor pasó por revisión estática del arquitecto contra las reglas críticas (aislamiento por sesión, unicidades, permisos por ruta, secretos fuera del repo).
- **Seguridad desde el diseño**: consultas del alumno siempre por sesión (jamás por parámetro de URL), rate limiting contra enumeración de CURPs, mensajes que no revelan existencia de registros, `Cache-Control: no-store` en datos personales, archivos sensibles fuera del webroot, datos de prueba 100% sintéticos.
- **Datos reales de operación como restricción**: pico de ~300 registros simultáneos al terminar el examen → folios con transacción + lock pesimista, probados con test de generación masiva.

## 6. Auditoría continua: hallazgos reales

La auditoría no fue un evento final sino un ciclo por fase. Hallazgos concretos (verificables en la historia del repo):

| Hallazgo | Severidad | Detección | Corrección |
|---|---|---|---|
| `RegistroAlumnoService` no verificaba bloqueo de edición ni ventana del ciclo en re-envíos del alumno (violaba RF-15/SEG-06 en escritura) | Alta | Revisión estática post-Fase 1, con greps dirigidos a reglas críticas | Guardas en el servicio + `BloqueoEscrituraAlumnoTest` + throttle en POST /registro (commit `Fase 1.16`) |
| Permiso de creación de ciclos asignado a `modulos.publicar` en vez de reservarse al admin (contradecía `docs/03`) | Media | Revisión estática post-Fase 3 | Corregido como primer hito de la fase UX (commit `UX.0`) con test de 403 |
| Verificación post-despliegue | — | Fetch remoto a producción | `/up` sano, HTTPS forzado, sin stack traces en errores, ninguna llave SSH en el repo (`cert/` ignorado) |

Lección: los agentes constructores producen código que **pasa sus propios tests**; la revisión independiente contra las *reglas de negocio* (no contra los tests) es donde aparecen las brechas. Por eso los roles están separados.

## 7. Métricas reales (extraídas del repositorio)

| Métrica | Valor |
|---|---|
| Calendario requerimientos → producción | 4 días (3–6 julio 2026) |
| Trabajo real de orquestación | ~8 horas efectivas distribuidas en esos 4 días |
| Ventana de commits de construcción | 3-jul 19:14 → 4-jul 21:47 (~27 h, no equivalente a trabajo efectivo) |
| Commits | 45 (24 el día 1, 21 el día 2), uno por tarea/hito |
| Fases | 0 (cimientos), 1 (MVP registro), 2 (evaluación), 3 (escolar/multiciclo), UX (0–8) |
| Tiempo efectivo medido del agente constructor (Fase 1 completa, 15 tareas) | 45 min 52 s (479,854 tokens) |
| Tokens estimados en sesiones Codex del proyecto | ~3.67M tokens incrementales (aprox. sin entrada cacheada) / ~87.4M tokens procesados brutos incluyendo caché |
| Código de aplicación | ~8,500 líneas en 154 archivos (sin dependencias) |
| Cobertura funcional | 43 métodos de prueba; 20/20 criterios de aceptación del requerimiento |
| Base de datos | 17 migraciones, 27 modelos Eloquent |
| Interfaz | 42 vistas Blade (alumno mobile-first + panel admin) |
| Documentación de diseño | 9 documentos + requerimientos ≈ 2,900 líneas, versionadas en el repo |
| Incidencias de auditoría | 2 encontradas, 2 corregidas con test de regresión |

Nota honesta sobre el tiempo: los 4 días incluyen pausas, fricción de entorno real (PHP 8.2 vs 8.3 en Windows, reestructura del repositorio, preparación de Hostinger) y verificación. La ventana de commits no debe leerse como 27 horas trabajadas: el estimado honesto de trabajo real de orquestación es ~8 horas. En tokens, los logs locales de Codex registran ~87.4M tokens procesados brutos, pero esa cifra incluye entrada cacheada recontada; como aproximación incremental para costo/uso se usa ~3.67M tokens.

## 8. Lecciones aprendidas (lo que le diríamos a un equipo que empieza)

1. **El diseño previo es el multiplicador.** Las ~2,900 líneas de docs no son burocracia: son lo que permite que un agente construya 15 tareas sin desviarse. Basura entra, basura sale — con agentes, amplificado.
2. **Condición de término verificable o no hay goal.** "Mejora la UX" no es un objetivo para un agente; "estos 4 tests nuevos pasan y los 46 previos no se tocan" sí.
3. **"Escalar en vez de inventar" es la cláusula más valiosa.** Los agentes rellenan huecos con ficción plausible si no se les prohíbe explícitamente.
4. **Verificación independiente del constructor.** El mismo agente que escribe el código no debe ser la única línea de defensa; revisión cruzada (otro agente u humano) contra reglas de negocio, no contra tests.
5. **El entorno es parte del sistema.** La mitad de los tropiezos fueron de entorno (versiones PHP, PATH, worktrees, topología del repo). CI como verificador neutral resolvió lo que las máquinas locales no podían garantizar.
6. **El estado del proyecto vive en el repo** (`CLAUDE.md`/`AGENTS.md` § Estado actual), no en la memoria de nadie: cualquier agente o persona nueva se incorpora leyendo dos archivos.

## 9. Cómo replicarlo en el siguiente proyecto

1. Escribir (o destilar con IA) el documento de requerimientos con criterios de aceptación numerados.
2. Generar el diseño técnico revisable (arquitectura, datos, seguridad, plan por fases con backlog estimado) — 1 día.
3. Crear `AGENTS.md` con convenciones y reglas críticas; configurar repo + CI antes de la primera línea de código de negocio.
4. Redactar el prompt de la Fase 1 con la anatomía de la sección 4 y lanzar el agente constructor.
5. Por cada fase: revisión independiente → correcciones con test de regresión → actualizar "Estado actual" → siguiente prompt.
6. Desplegar temprano (fase 0/1) para descubrir la fricción de infraestructura con calma.

---
*Todo dato de este reporte es verificable en el repositorio (historia de commits, tests, workflows de CI) o en la conversación de orquestación del proyecto. No contiene cifras estimadas salvo donde se indica "≈".*

# Script de presentación — Desarrollo orquestado con agentes de IA
Audiencia: equipo de desarrolladores expertos (Bolivia) · Duración sugerida: 40 min + 20 de preguntas
Material en pantalla: el repositorio (compartido previamente), GitHub Actions, y el sitio en producción.

---

## 0. Preparación previa (checklist del presentador)

- [ ] Compartir el repo 2-3 días antes con este mensaje: *"Les comparto el repositorio de un proyecto real en producción. Antes de la sesión, les pido 20 minutos: lean `docs/presentacion/reporte-metodologia.md`, hojeen `docs/prompts/fase1-codex-goal.md` y saquen sus propias conclusiones del `git log`. Vengan con escepticismo."*
- [ ] Tener abiertas 5 pestañas: (1) el sitio en producción, (2) `git log --oneline` local o red de commits en GitHub, (3) GitHub Actions, (4) `docs/prompts/fase1-codex-goal.md`, (5) `docs/09-plan-construccion.md`.
- [ ] Probar el flujo de registro en el celular (por si piden demo en vivo).
- [ ] Tener a la mano los dos hallazgos de auditoría (commits `Fase 1.16` y `UX.0`) — son tu mejor argumento de honestidad.

---

## 1. Apertura — el gancho (3 min)

> "Les voy a mostrar un sistema que está en producción hoy: un portal de ingreso escolar para un plantel público en Michoacán, México. Registro de aspirantes, PDF oficial, documentación, resultados de examen, grupos, matrícula, horarios, dashboards, control de acceso por seis roles. **[Mostrar el sitio en producción en el celular o pantalla.]**
>
> La pregunta que quiero que se hagan durante los próximos 40 minutos no es *'¿la IA escribió este código?'* — eso ya lo asumen. La pregunta es: **¿qué proceso de ingeniería hace falta para que el código que escribe una IA llegue a producción con pruebas, auditoría y sin sorpresas?** Porque el código es la parte fácil. El proceso es lo que les vengo a mostrar."

Dato de impacto (real, verificable en el repo): *"Del documento de requerimientos a producción: 4 días calendario, pero no fueron 4 días de teclear: fueron aproximadamente 8 horas reales de trabajo orquestado. Una sola persona coordinando dos agentes. Y el commit log está ahí para que lo auditen ustedes mismos."*

## 2. El problema y los requerimientos (5 min)

**[Pantalla: `requerimientos_portal_academico_nuevo_ingreso_cobaem.md`, saltar a §29-31.]**

> "Todo empezó con este documento: 40 requerimientos funcionales, 30 no funcionales y — esto es lo importante — **20 criterios de aceptación numerados**. No empezamos con código ni con prompts: empezamos con un contrato verificable.
>
> El contexto importa: usuarios de ~15 años con celulares básicos y poca experiencia digital, personal administrativo no técnico, hosting compartido barato, y una restricción dura: el sistema debe reutilizarse cada año escolar sin mezclar datos históricos. Ese contexto tomó todas las decisiones de arquitectura por nosotros."

## 3. Arquitectura: decisiones aburridas a propósito (5 min)

**[Pantalla: `docs/01-arquitectura.md`, tabla de ADRs.]**

> "Van a notar que la arquitectura es deliberadamente conservadora: monolito Laravel, Blade + Livewire, MariaDB, sin SPA, sin Redis, sin microservicios — salvo uno: el módulo de lectura óptica de exámenes, que es un servicio externo con contrato REST y fallback CSV, porque OpenCV no corre en hosting compartido.
>
> Con agentes de IA, la arquitectura aburrida es una ventaja competitiva: menos superficie donde el agente pueda tomar decisiones creativas equivocadas. La creatividad la gastamos en el diseño; la construcción debe ser predecible.
>
> Dos decisiones que sí son interesantes: alumno **sin cuenta** — accede por CURP más un segundo dato para lo sensible, cero contraseñas para el usuario menos digital — y separación identidad/proceso que hace el sistema multiciclo por diseño. El aislamiento entre ciclos 2026 y 2027 se probó con tests antes de que exista el ciclo 2027."

## 4. La metodología: tres roles y un contrato (8 min) — el corazón de la charla

**[Pantalla: `docs/prompts/fase1-codex-goal.md`.]**

> "Aquí está el núcleo de lo que vengo a proponerles. Trabajamos con dos agentes de IA con roles **separados a propósito**, más un humano:
>
> - Un agente **arquitecto y revisor** (Claude) que escribió el diseño técnico — 9 documentos, unas 2,900 líneas — y redactó esto que ven: el *prompt de construcción* de cada fase.
> - Un agente **constructor** (Codex, en modo goal: un bucle autónomo que trabaja hasta cumplir un objetivo) que ejecutó cada fase completa: código, migraciones, tests, un commit por tarea.
> - Y yo, que no escribí código: tomé decisiones de producto, aprobé fases y gestioné lo institucional.
>
> Miren la anatomía de este prompt, porque esto es lo replicable: **[señalar en pantalla]**
> 1. Objetivo acotado a tareas numeradas de un backlog que ya existía en `docs/09`.
> 2. **Condición de término verificable**: criterios de aceptación cubiertos por tests con nombre y apellido, suite en verde, linter limpio. No 'haz un buen trabajo' — condiciones que una máquina puede comprobar.
> 3. Restricciones duras que invalidan el goal *aunque los tests pasen* — seguridad, convenciones, prohibición de dependencias nuevas.
> 4. Y mi cláusula favorita: **'escalar en vez de inventar'**. La lista explícita de cosas donde el agente debe detenerse y preguntar: textos legales, formatos oficiales, reglas institucionales pendientes. Sin esta cláusula, los agentes rellenan los huecos con ficción plausible. Con ella, el agente se detuvo dos veces en este proyecto — y las dos veces tuvo razón en detenerse."

**[Pantalla: `AGENTS.md`.]**

> "Y este archivo es la memoria del proyecto: convenciones, reglas de negocio que no se pueden romper, y el estado actual. Cualquier agente — o cualquier desarrollador nuevo — se incorpora leyendo esto. El conocimiento no vive en la cabeza de nadie."

## 5. El proceso en acción: recorrido por la historia (7 min)

**[Pantalla: `git log --oneline --reverse` o la lista de commits en GitHub.]**

> "Léanlo de abajo hacia arriba: Fase 1.1 a 1.16, Fase 2.1 a 2.9, Fase 3.1 a 3.5, UX.0 a UX.8. Cuarenta y cinco commits, uno por tarea, en español, historia lineal, sin squash ni reescritura. Cuando algo falló — aquí, en UX.4 — hay dos commits `fixup` visibles. No escondemos el proceso: lo auditamos. Y algo importante: la ventana de commits no equivale a horas trabajadas; varias sesiones incluyen espera, entorno o revisiones abiertas.
>
> Cada fase terminó igual: suite completa en verde, linter limpio, push, y CI en GitHub Actions como juez neutral. **[Pantalla: pestaña Actions, runs en verde.]** Esto importó más de lo esperado: cuando mi máquina local tenía PHP 8.2 y el proyecto exigía 8.3, el CI fue el verificador imparcial mientras arreglábamos el entorno."

Anécdota del tiempo (dato real): *"La Fase 1 completa — 15 tareas: wizard de registro de 6 pasos, PDF, panel admin, importación/exportación CSV, avisos, auditoría — la construyó el agente en 45 minutos con 52 segundos. Lo tengo medido porque el modo goal lo reporta. Mi trabajo de ese día fue revisar, no teclear."*

## 6. La auditoría: donde se gana la confianza (6 min)

**[Pantalla: commit `Fase 1.16` y su test `BloqueoEscrituraAlumnoTest`.]**

> "Esta es la parte que le presento con más orgullo, y es donde quiero su atención de expertos. Después de cada fase, el agente revisor hizo auditoría estática contra las **reglas de negocio** — no contra los tests del constructor, que siempre pasan. Encontró dos brechas reales:
>
> Una de severidad alta: el servicio de registro no verificaba el bloqueo de edición cuando un alumno re-enviaba el formulario. Los 31 tests de la fase estaban en verde — porque el constructor probó lo que construyó, no lo que no construyó. La regla de negocio decía 'el bloqueo impide escribir, nunca consultar', y la mitad de escritura tenía un hueco. Se corrigió con guardas en el servicio y un test de regresión: este commit.
>
> Y una media: un permiso de creación de ciclos más laxo de lo que el diseño exigía. Detectada, anotada como deuda, corregida como primer hito de la fase siguiente, con test de 403.
>
> La lección para el equipo: **el agente que construye no puede ser la única línea de defensa.** La revisión independiente — otro agente, u ustedes — contra las reglas del dominio es donde aparece lo que los tests no ven. Tras el despliegue verificamos también producción: health check, HTTPS forzado, sin stack traces, ninguna llave en el repo."

## 7. Los números, sin maquillaje (3 min)

**[Pantalla: tabla de métricas del reporte.]**

> "Números verificables en el repo: ~8,500 líneas de aplicación en 154 archivos, 43 pruebas cubriendo los 20 criterios de aceptación, 17 migraciones, 42 vistas, 2 hallazgos de auditoría corregidos.
>
> Y el número honesto: 8 horas reales de trabajo orquestado distribuidas en 4 días calendario. En tokens, los logs locales de Codex dan dos lecturas: ~87.4M tokens procesados brutos incluyendo caché, y ~3.67M tokens incrementales aproximados quitando entrada cacheada. ¿El resto del calendario? Decisiones humanas, verificación, y fricción de entorno real — versiones de PHP, la topología del repositorio, la preparación del hosting. Con agentes, el cuello de botella se mueve del teclado a la cabeza. Esa es exactamente la propuesta."

## 8. Cierre y enrolamiento (3 min)

> "Lo que les propongo para el siguiente proyecto no es 'usar IA para programar'. Es adoptar el proceso: requerimientos con criterios de aceptación → diseño técnico revisable → contrato de agentes (`AGENTS.md`) → prompts de fase con condición de término → construcción autónoma → revisión independiente → CI como juez. Ustedes ponen la experiencia de dominio y el criterio; los agentes ponen el volumen.
>
> Mi invitación concreta: en el siguiente desarrollo, tomemos una fase — una sola — y ejecutémosla con esta mecánica, con ustedes como revisores. Comparen su experiencia contra su proceso actual y decidan con datos. El repositorio queda abierto para ustedes: auditen el log, corran los tests, busquen las costuras. Las preguntas incómodas son bienvenidas — yo también las tenía hace un mes."

---

## Apéndice A — Preguntas difíciles esperadas (y respuestas honestas)

**"¿El código tiene la calidad que escribiría un senior?"**
Es consistente y convencional (Laravel idiomático, un linter lo garantiza), y donde importaba — concurrencia de folios, aislamiento de sesión — hay tests específicos. Pero encontramos 2 brechas de lógica de negocio que un senior atento quizá no habría cometido. Por eso el proceso incluye revisión independiente: la calidad no viene del generador, viene del sistema de verificación.

**"¿Qué pasa con nuestros puestos como desarrolladores?"**
En este proyecto el humano no tecleó código, pero tomó ~30 decisiones que ningún agente podía tomar: arquitectura vs presupuesto, qué escalar al plantel, cuándo detener una fase, qué hallazgo era severidad alta. El rol se desplaza a arquitecto/revisor/orquestador — que es trabajo más senior, no menos.

**"¿Y la seguridad de compartir código/datos con los agentes?"**
Los datos de prueba son 100% sintéticos (regla del contrato), las credenciales nunca tocaron el repo ni los prompts, y la revisión final verificó que no hay secretos commiteados. Para proyectos con requisitos más estrictos, la misma metodología funciona con modelos desplegados en infraestructura propia.

**"¿Cuánto costó?"**
El dato medido: la Fase 1 consumió ~480 mil tokens incrementales del constructor (~46 minutos). Sumando sesiones Codex del proyecto, el estimado incremental es ~3.67M tokens; el contador bruto procesado marca ~87.4M porque incluye entrada cacheada recontada. El costo total en APIs/suscripciones depende del proveedor y del tratamiento de caché, pero se compara contra semanas-persona del enfoque tradicional. Traigan su propia calculadora de tarifas.

**"¿Esto escala a un equipo, o solo funciona con una persona?"**
Los artefactos son los mismos que usaría un equipo: backlog, ADRs, criterios de aceptación, CI, revisión por pares. La diferencia es que el "par" puede ser un agente. Con varios humanos, cada quien orquesta fases distintas contra el mismo `AGENTS.md` — de hecho el proceso obliga a una disciplina de documentación que muchos equipos humanos no tienen.

**"¿Qué NO harían igual?"**
(1) Verificar el entorno local (versiones, extensiones) antes de la primera fase, no durante. (2) Decidir la topología del repositorio el día cero. (3) Desplegar el "hola mundo" a producción en la fase 0, no después del MVP.

## Apéndice B — Material de acompañamiento recomendado

1. **El repositorio** (ya compartido) — es el material principal; la presentación solo lo recorre.
2. **Este reporte** (`reporte-metodologia.md`) — enviarlo antes como lectura previa.
3. **Demo en vivo** del sitio en producción desde un celular (el flujo CURP → registro) — 2 minutos, más convincente que cualquier slide.
4. **Deck opcional de 8-10 slides** solo con: portada, diagrama de los 3 roles, anatomía del prompt, timeline de commits, tabla de auditoría, tabla de métricas, propuesta de piloto. (Si se quiere, se genera a partir de este script.)
5. **Un handout de una página**: la "anatomía del prompt de construcción" (sección 4) — es lo que el equipo va a querer copiar al día siguiente.

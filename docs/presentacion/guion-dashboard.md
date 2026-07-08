# Guion recomendado para `dashboard-nanoforge.html`

Duración sugerida: 40 minutos + Q&A.  
Audiencia: equipo de desarrolladores expertos en Bolivia.  
Formato: abrir el HTML y usar `Modo presentación` o tecla `F`. Navegación con flechas, espacio, PageUp/PageDown, `Esc` para salir.

## Preparación

- Enviar el repo 2 o 3 días antes con una invitación explícita a auditarlo.
- Tener abiertas estas pestañas: sitio en producción, GitHub commits, GitHub Actions, `docs/prompts/fase1-codex-goal.md`, `AGENTS.md` y `docs/09-plan-construccion.md`.
- Probar el HTML a 1920x1080 y también en el monitor real de la reunión.
- Tener ubicados los commits o cambios asociados a `Fase 1.16` y `UX.0`; son la prueba de que la auditoría encontró problemas reales.

## 01 · Portada (2 min)

Frase de apertura:

> “Les voy a mostrar un sistema real en producción, no una demo. La presentación resume el caso, pero el material principal es el repositorio: todo lo que diga hoy se puede verificar ahí. El dato honesto no es solo que llegó a producción en 4 días calendario: fueron aproximadamente 8 horas reales de trabajo orquestado.”

Qué mostrar: la portada y, si el contexto lo permite, el sitio de producción en un celular durante 30 segundos.

## 02 · Tesis (3 min)

Enfatizar que la conversación no es “IA sí o no”, sino proceso de ingeniería.

Frase clave:

> “Pedir código a un modelo es fácil. Lo difícil es diseñar un sistema donde ese código pueda pasar por pruebas, auditoría y despliegue sin depender de fe.”

No vender perfección. El punto fuerte es la trazabilidad.

## 03 · Solución (4 min)

Recorrer las cuatro tarjetas: dominio, contexto, contrato funcional y stack.

Detenerse en el usuario real:

> “La UX no fue un lujo; si el alumno no puede completar el registro desde un celular básico, el sistema fracasa aunque la arquitectura sea elegante.”

Señalar los 20 criterios de aceptación como el puente entre requerimientos y pruebas.

## 04 · Arquitectura (4 min)

Tono: sobriedad deliberada.

Frase clave:

> “La arquitectura es aburrida a propósito. Con agentes, eso reduce el espacio para decisiones creativas equivocadas.”

Destacar dos decisiones: alumno sin cuenta y separación `Alumno` / `ProcesoIngreso`.

## 05 · Tres roles (6 min)

Esta es la sección central. Presentar cada rol sin prisa:

- Orquestador humano: decide y valida.
- Arquitecto/revisor: diseña y audita.
- Constructor: ejecuta con condición de término.

Frase clave:

> “El que construye no se audita solo. Esa separación fue lo que permitió encontrar brechas aunque los tests estuvieran en verde.”

Abrir `AGENTS.md` brevemente para mostrar que el contrato vive en archivos.

## 06 · Prompt replicable (6 min)

Abrir `docs/prompts/fase1-codex-goal.md`.

Recorrer los cinco elementos de la pantalla. Pausar en “escalar en vez de inventar”.

Frase clave:

> “Sin esta cláusula, los agentes rellenan huecos con ficción plausible. Con ella, el agente se detuvo dos veces y las dos tuvo razón.”

Este es el elemento que el equipo debería poder reutilizar al día siguiente.

## 07 · Historia (4 min)

Mostrar `git log --oneline --reverse` o la vista de commits.

Frase clave:

> “No hay squash para esconder tropiezos. La historia lineal permite auditar cómo se llegó a producción. Ojo: la ventana de commits no es trabajo efectivo; varias fases quedan agrupadas o separadas por espera, entorno y revisión.”

Mencionar los 45m52s de la Fase 1 como dato medido de esa fase, no como promesa universal.

## 08 · Auditoría (6 min)

Esta sección debe sonar honesta, no defensiva.

Frase clave:

> “Estos hallazgos son mi mejor argumento. Demuestran que el proceso no dependía de creerle al constructor.”

Explicar el hallazgo de bloqueo de edición: tests verdes, regla rota, corrección con regresión. Luego el permiso de ciclos. Cerrar con la idea de revisión independiente contra reglas de negocio.

## 09 · Métricas (3 min)

No leer todos los números. Dejar que respiren y marcar cuatro:

- 43 tests y 20/20 criterios.
- ~8,500 líneas en 154 archivos.
- 8 horas reales de trabajo orquestado en 4 días calendario.
- ~3.67M tokens incrementales estimados; ~87.4M tokens procesados si se incluye entrada cacheada recontada por sesión.

Frase clave:

> “El cuello de botella se mueve del teclado a la cabeza: decisiones, verificación y entorno. Por eso separo 4 días calendario, 8 horas reales y tokens procesados; cada métrica responde una pregunta distinta.”

## 10 · Piloto (4 min)

Cierre con invitación concreta, no venta.

Frase clave:

> “No les propongo adoptar una religión. Les propongo un experimento controlado: una fase, ustedes como revisores, y comparamos con datos.”

Terminar:

> “Preguntas difíciles primero. Son las que más sirven.”

## Q&A: respuestas cortas recomendadas

**¿Tiene calidad senior?**  
Tiene consistencia, pruebas y arquitectura convencional. También tuvo dos brechas reales. La calidad viene del sistema de verificación, no del generador.

**¿Esto reemplaza desarrolladores?**  
Reemplaza parte del tecleo, no el criterio. El rol se desplaza hacia arquitectura, revisión, producto y operación.

**¿Qué pasa con datos sensibles?**  
Datos de prueba sintéticos, secretos fuera del repo, archivos sensibles privados y políticas explícitas. En contextos más estrictos, el mismo proceso puede ejecutarse con infraestructura controlada.

**¿Cuánto cuesta?**  
La Fase 1 consumió alrededor de 480k tokens incrementales y 45m52s medidos. Sumando sesiones Codex del proyecto, el estimado incremental es ~3.67M tokens; el contador bruto procesado marca ~87.4M porque incluye entrada cacheada recontada en cada sesión. El costo exacto depende del proveedor, pero el punto de comparación debe ser tiempo, defectos y retrabajo, no solo tarifa por token.

**¿Qué no repetirías?**  
Validar entorno local desde el día cero, definir topología del repo antes de construir y desplegar un “hola mundo” en Fase 0.

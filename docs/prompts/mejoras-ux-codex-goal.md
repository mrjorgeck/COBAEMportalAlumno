# Prompt de construcción — Mejoras de UX (Codex, modo Goal)

**Cómo usarlo**: abre Codex (≥ 0.128) en la raíz del repositorio, escribe
`/goal` y pega el bloque de abajo.

**Contexto**: MVP completo y verificado (Fases 0-3, CI en verde). Esta fase
no agrega funcionalidad: pule la experiencia para el usuario real — alumnos
de secundaria con celulares básicos y baja experiencia digital (RNF-01/02/
11/12/14/27), y personal administrativo no técnico.

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Mejorar la experiencia de usuario del Portal de Nuevo Ingreso (app Laravel
en portal/; el repo Git está en la raíz): errores amigables, campos
obligatorios claramente marcados, y mecanismos de usabilidad estándar del
mercado. El usuario objetivo es un alumno de ~15 años con celular básico y
poca experiencia digital, y personal administrativo no técnico. NO agregar
funcionalidad de negocio nueva ni alterar reglas existentes.

CONDICIÓN DE TÉRMINO (todo verdadero)

1. Los 46 tests existentes siguen pasando SIN editarlos (solo se permite
   ajustar asserts de texto si un mensaje cambió, documentándolo en el
   commit).
2. Tests nuevos que verifican: páginas de error personalizadas (404, 419,
   429, 500, 503) responden con la vista amigable y status correcto; los
   campos obligatorios del wizard se renderizan con el marcador visual y
   aria-required; los mensajes de validación salen en español; el permiso
   de crear ciclos quedó restringido a usuarios.administrar.
3. cd portal && php artisan test && ./vendor/bin/pint --test en verde.
4. Un commit por hito: "UX.N: descripción" (en español). Push y CI en verde.

REGLA DE ORO DE ESTA FASE

Antes de codificar, inventaría lo existente: recorre TODAS las vistas de
resources/views (alumno y admin) y lista formularios, campos y estados de
error actuales. Aplica las mejoras de forma CONSISTENTE en todo el portal
mediante componentes Blade reutilizables — no parches vista por vista.

PLAN DE HITOS

H0  Deuda pendiente: cambiar el permiso de POST /admin/ciclos de
    modulos.publicar a usuarios.administrar (docs/03 lo reserva al admin)
    + test. Commit "UX.0: permiso de creación de ciclos solo admin".

H1  Sistema de formularios unificado: componentes Blade x-campo (label +
    input/select + ayuda + error inline), x-obligatorio (asterisco rojo +
    aria-required) y leyenda única "Los campos marcados con * son
    obligatorios" al inicio de cada formulario. Los campos opcionales
    muestran "(opcional)" en el label. Aplicarlo a TODOS los formularios
    (wizard de registro, acceso, verificación, y formularios admin).
    La obligatoriedad visual debe derivarse de las reglas de validación
    reales (RegistroAlumnoRules), no duplicarse a mano.

H2  Mensajes en español: instalar laravel-lang (composer require
    laravel-lang/lang + publicar es) para validación/errores del framework;
    personalizar :attribute con nombres humanos ("primer apellido", no
    "primer_apellido") vía lang/es/validation.php attributes. Mensajes
    específicos ya existentes (CURP, folio) revisados en tono amable y
    accionable: qué pasó + qué hacer.

H3  Páginas de error amigables en resources/views/errors/ para 403, 404,
    419, 429, 500 y 503, con el layout del alumno: lenguaje simple, sin
    jerga técnica ni stack traces, icono, y acción sugerida por código:
    419 "tu sesión expiró, vuelve a entrar con tu CURP" + botón al inicio;
    429 "demasiados intentos, espera un minuto"; 500 "algo salió mal de
    nuestro lado, intenta más tarde o acude a control escolar"; 503 vista
    de mantenimiento institucional. En producción NUNCA se muestra detalle
    técnico (verificar APP_DEBUG=false rendering). Los errores 500 se
    siguen registrando completos en logs (no tocar el logging).

H4  Wizard de registro (la pieza crítica):
    - Indicador de progreso: "Paso X de 6" + barra visual + nombre del paso.
    - Botón enviar con estado de carga (wire:loading: spinner + disabled +
      texto "Guardando...") para evitar dobles envíos en conexiones lentas.
    - Al fallar validación: scroll automático al primer campo con error y
      resumen de errores al inicio del paso con anclas a cada campo.
    - Aviso visible de borrador: "Tu avance se guarda en cada paso; puedes
      volver después con tu CURP".
    - Ayuda contextual: bajo el campo CURP, enlace "¿No conoces tu CURP?
      Consúltala en gob.mx" (https://www.gob.mx/curp/) target _blank; bajo
      folio de examen, texto de dónde encontrarlo en la hoja de respuestas;
      confirmación doble del folio ya existente se mantiene.
    - Pantalla de éxito con folio interno en grande, botón claro de
      descarga del PDF e indicación del siguiente paso físico (imprimir y
      entregar a control escolar).

H5  Entradas optimizadas para móvil (estándar del mercado):
    - type/inputmode/autocomplete correctos en todos los campos: tel +
      inputmode=numeric para teléfonos, email para correo, date nativo
      para fechas, inputmode=decimal para promedio/estatura/peso,
      autocomplete (name, family-name, tel, email, bday, postal-code).
    - CURP: mayúsculas automáticas al escribir (CSS text-transform +
      normalización server-side ya existente), maxlength 18 con contador
      visual de caracteres, autocapitalize=characters, autocorrect=off.
    - Selects largos (municipios, secundarias, localidades): búsqueda por
      texto con Alpine.js (filtrado client-side sobre el select existente)
      — SIN nuevas dependencias JS; Alpine ya viene con Livewire.
    - Áreas táctiles mínimas de 44px en botones y controles del alumno.

H6  Retroalimentación y navegación:
    - Toasts/banners de éxito consistentes tras cada acción (guardado,
      importación encolada, aviso publicado) con auto-cierre.
    - Confirmación antes de acciones destructivas o de alto impacto en
      admin (bloquear edición, eliminar horario, publicar/despublicar
      módulo) con texto de consecuencias.
    - Estados vacíos amables en listados sin datos ("Aún no hay avisos")
      en lugar de tablas vacías.
    - En "Mi proceso": cada etapa del tablero con color/icono por estado y
      texto de qué sigue ("Tu documentación está en revisión; te avisaremos
      aquí").

H7  Accesibilidad y cierre: labels asociados (for/id) en el 100% de los
    campos, foco visible, contraste AA en los colores institucionales
    (ajustar tonos si algún par falla), lang=es ya presente, revisión de
    todas las vistas del alumno a 360px sin scroll horizontal. Suite
    UxAcceptanceTest con los tests de la condición de término. Actualizar
    docs/03 §"UX" (crear subsección breve con las convenciones adoptadas:
    marcado de obligatorios, componentes x-campo, catálogo de páginas de
    error).

RESTRICCIONES DURAS (romperlas = goal fallido)

- Cero dependencias JS nuevas (solo Livewire/Alpine/Tailwind existentes) y
  cero dependencias composer salvo laravel-lang. Nada de SPA, CDN de
  frameworks UI, ni librerías de componentes.
- No cambiar reglas de negocio, validaciones de fondo, esquema de BD ni
  rutas existentes (excepto H0). No tocar textos legales (aviso de
  privacidad) ni la plantilla PDF.
- Mantener seguridad intacta: mensajes amigables NUNCA revelan si una CURP
  existe o no (SEG-03), ni detalles internos; Cache-Control: no-store se
  conserva; el marcado de obligatorios no debilita la validación server-side.
- Español en toda la UI, con acentos correctos (revisar los textos ASCII
  existentes tipo "inscripcion" → "inscripción" en vistas, sin tocar claves
  de BD ni rutas).
- Mobile-first: cualquier mejora se diseña primero para 360px.

AUTOVERIFICACIÓN (tras CADA hito)

cd portal && php artisan test && ./vendor/bin/pint
Corregir antes de avanzar. Commit por hito.

ESCALAR EN VEZ DE INVENTAR

- Colores/logotipo institucionales definitivos: si un ajuste de contraste
  cambia la identidad visual de forma notoria, proponerlo y pausar.
- Cualquier mejora que requiera dependencia nueva o cambio de reglas de
  negocio: proponer y pausar, no implementar.
```

---

## Después del goal

1. Recorrido manual en celular real (o DevTools a 360px): registro completo
   con errores provocados a propósito — evaluar si un alumno de 15 años
   entendería cada mensaje.
2. Provocar cada página de error (404, 419 con formulario viejo, 429 con
   intentos repetidos) y verificar tono y botón de salida.
3. Revisión de contraste con el plantel si hubo ajustes de color.
4. Actualizar "Estado actual" en CLAUDE.md y AGENTS.md.

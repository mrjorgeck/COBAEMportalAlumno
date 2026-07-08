# Prompt de construcción — Formato PDF oficial de inscripción (Codex, modo Goal)

**Cómo usarlo**: abre Codex (≥ 0.128) en la raíz del repositorio, escribe
`/goal` y pega el bloque de abajo.

**Prerrequisito**: árbol Git limpio y tests en verde. Topología: el repo vive
en la RAÍZ; la app Laravel está en `portal/`. Si se cuenta con el logo
institucional (CB/COBAEM), colocarlo ANTES en
`portal/resources/images/pdf/logo-cobaem.png`; si no existe, el goal usa un
placeholder y lo documenta.

**Origen**: el PDF actual (`pdf/inscripcion/v2026/formato.blade.php`) es un
borrador genérico. Control escolar requiere que reproduzca la "Solicitud de
Inscripción" oficial en papel del plantel Ario de Rosales (formato 26-2).

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Rediseñar la plantilla del PDF de inscripción del alumno
(portal/resources/views/pdf/inscripcion/v2026/formato.blade.php, renderizada
con barryvdh/laravel-dompdf desde
portal/app/Http/Controllers/Alumno/FormatoController.php) para que reproduzca
fielmente el formato oficial en papel "SOLICITUD DE INSCRIPCIÓN" del COBAEM
Plantel Ario de Rosales, descrito en la sección MAQUETA de abajo. Es SOLO un
cambio de presentación: no cambies rutas, modelo de datos ni flujo de
descarga.

CONDICIÓN DE TÉRMINO (todo verdadero)

1. El PDF descargado por alumno y por admin reproduce la MAQUETA: una sola
   hoja tamaño carta, encabezado institucional, línea de tipo de estudiante
   con casillas, y las 7 secciones con barra de título verde en este orden:
   DATOS DEL ESTUDIANTE, DIRECCIÓN PARTICULAR ACTUAL, DATOS DE CONTACTO
   ACTUAL, ESCUELA DE PROCEDENCIA, DATOS DE TUTOR, DATOS DE MADRE, OTROS
   DATOS; seguidas de lugar/fecha, leyenda de privacidad y línea de firma.
2. Todos los campos del formulario se llenan con los datos reales del
   proceso (mapeo en CAMPOS Y MAPEO). Los campos sin dato se imprimen con la
   línea vacía (como en el formato en papel), nunca con "null" ni errores.
3. Los valores de catálogo (sexo, estado civil, nacionalidad, entidad y
   municipio de nacimiento, ocupación, estudios, beca, tipo de sangre,
   paraescolar, tipo de estudiante, municipio/localidad, datos de la
   secundaria) se imprimen con su nombre legible, cargando las relaciones
   necesarias en FormatoController (eager loading; sin N+1 masivo).
4. La casilla del tipo de estudiante correspondiente (Regular / Repetidor /
   Condicionado / Debe Materias Sec.) aparece marcada con X según
   tipo_estudiante_id; las demás quedan vacías.
5. La leyenda de aceptación del aviso de privacidad usa el texto de la
   MAQUETA (ya no el placeholder "pendiente de validación institucional").
6. cd portal && php artisan test pasa completo, incluyendo un feature test
   nuevo (o ampliado) que: genera un proceso completo con factories,
   descarga el PDF por la ruta del alumno y por la de admin (200 y
   content-type PDF), y renderiza la vista Blade verificando que contiene
   nombre, CURP, folio, nombres de catálogo resueltos y los títulos de las
   7 secciones. ./vendor/bin/pint --test limpio. Commits en español
   ("Formato PDF N: ..."). Push a origin main y CI en verde.

FUENTES DE VERDAD (en este orden ante conflicto)

1. La MAQUETA de este prompt (transcripción del formato oficial en papel).
2. requerimientos_portal_academico_nuevo_ingreso_cobaem.md (RF del PDF).
3. docs/06 (CSV/PDF) y docs/02 (modelo de datos).
4. CLAUDE.md / AGENTS.md (convenciones y reglas críticas).

MAQUETA (formato oficial en papel, hoja carta, una página)

ENCABEZADO (tres columnas):
- Izquierda: logo institucional. Si existe
  portal/resources/images/pdf/logo-cobaem.png, incrústalo en base64 (dompdf
  no debe depender de URLs); si no existe, deja un recuadro placeholder de
  ~70x70px con el texto "LOGO" y documéntalo en el commit. Debajo del logo:
  "Periodo escolar" y en grande el periodo (ej. "26-2").
- Centro: "SOLICITUD DE INSCRIPCIÓN" (negritas), debajo "Colegio de
  Bachilleres del Estado de Michoacán", debajo "PLANTEL: {nombre en
  mayúsculas}", debajo la clave del plantel (ej. "16ECB0027Z").
- Derecha: "No. De Ficha" con el valor sobre una línea.

LÍNEA DE TIPO DE ESTUDIANTE:
"Solicito Inscripción a 1° Semestre como estudiante:" seguida de casillas
apiladas: Regular ( ), Repetidor ( ), Condicionado ( ), Debe Materias
Sec. ( ). A la derecha: "Paraescolar: ______". El "1°" sale de
semestre_solicitado (ordinal).

SECCIONES (cada título en una barra horizontal de fondo verde claro
aproximado #C7D9A3, texto oscuro centrado, mayúsculas; los campos son
etiqueta + valor subrayado con línea inferior, imitando renglones del
formato en papel):

1. DATOS DEL ESTUDIANTE
   Renglón 1: Nombre(s) | Primer Ap. | Segundo Ap.
   Renglón 2: Estado Civil | Fecha de Nacimiento (Día - Mes - Año)
   Renglón 3: Edad | Sexo | Nacionalidad
   Renglón 4: Entidad de Nacimiento | Municipio de Nacimiento
   Renglón 5: CURP (renglón completo)
2. DIRECCIÓN PARTICULAR ACTUAL
   Renglón 1: Municipio | Localidad | C.P.
   Renglón 2: Domicilio (Calle y no.) | Colonia
3. DATOS DE CONTACTO ACTUAL
   Renglón 1: Teléfono | Celular | correo electrónico
4. ESCUELA DE PROCEDENCIA
   Renglón 1: Entidad | Municipio
   Renglón 2: Nombre de la Escuela | Promedio
5. DATOS DE TUTOR
   Renglón 1: Nombre(s) | Primer Ap. | Segundo Ap.
   Renglón 2: Teléfono | Celular | Ocupación | Estudios
6. DATOS DE MADRE
   Renglón 1: Nombre(s) | Primer Ap. | Segundo Ap.
   Renglón 2: Teléfono | Celular | Ocupación
   Renglón 3: Máximo Grado de Estudios (renglón completo)
7. OTROS DATOS
   Renglón 1: No. Seguro Médico | Becas
   Renglón 2: Estatura | Peso | Tipo Sangre RH

CIERRE:
- "Lugar y Fecha Actual: Ario de Rosales, Michoacán a {día} de {mes} de
  {año}." — usa el municipio del plantel si el modelo lo tiene; si no,
  ponlo configurable en config/portal.php (no hardcodear en la vista). La
  fecha es la de generación del PDF.
- Párrafo: "He leído y acepto las políticas de protección de datos
  personales del aviso de privacidad del Colegio de Bachilleres del Estado
  de Michoacán".
- Línea de firma centrada con el nombre completo del alumno encima de la
  leyenda "Nombre completo".
- Pie discreto (fuente pequeña): folio del portal (folio_registro), versión
  de plantilla y fecha/hora de generación, para trazabilidad.

CAMPOS Y MAPEO (esquema en docs/02 y migraciones 000005–000007)

- Periodo escolar: $proceso->ciclo->periodo_escolar. Plantel: nombre y
  clave de $proceso->plantel.
- No. De Ficha: folio_examen; si aún no hay folio de examen, línea vacía.
- Tipo de estudiante y paraescolar: catálogos tipo_estudiante_id /
  paraescolar_id.
- Estudiante: alumnos (nombres, apellidos, fecha_nacimiento d/m/Y, CURP) +
  catálogos sexo, estado civil, nacionalidad, entidad y municipio de
  nacimiento. Edad: calculada a la fecha de generación.
- Dirección/contacto: datos_contacto (municipio, localidad, colonia,
  domicilio, codigo_postal, telefono, celular, correo).
- Escuela de procedencia: entidad_secundaria_id, municipio_secundaria_id,
  secundaria_procedencia_id (nombre de la escuela), promedio_secundaria.
- Tutor y madre: familiares por tipo_familiar (relaciones tutor/madre ya
  existentes) con ocupación y estudios de catálogo. Para madre, "Máximo
  Grado de Estudios" = su estudios_id.
- Otros datos: otros_datos_alumno (no_seguro_medico, beca_id, estatura,
  peso, tipo_sangre_id).
- Si un campo de la maqueta NO existe en el esquema, imprime la línea vacía
  y lista el faltante en el commit; NO agregues columnas ni migraciones.

RESTRICCIONES DURAS (romperlas = goal fallido)

- Motor: dompdf tal como está (barryvdh/laravel-dompdf). No agregar
  paquetes, binarios ni dependencias JS. Fuente DejaVu Sans (acentos/ñ).
  CSS inline/embebido compatible con dompdf (tablas y estilos simples; sin
  flexbox/grid).
- Debe caber en UNA página carta con datos reales de longitud típica.
- Editar SOLO la versión v2026 (ciclo vigente); regla 7 de CLAUDE.md:
  plantillas de ciclos pasados no se tocan. plantilla_pdf_version sigue
  funcionando igual.
- No cambiar rutas, permisos, registro de descargas (DescargaFormato) ni el
  nombre del archivo descargado (folio_registro.pdf).
- No exponer datos de otros procesos; el controlador sigue filtrando por el
  proceso en sesión.
- Textos del PDF, commits y mensajes en español. Datos de prueba sintéticos
  (CURPs de factories, jamás reales).

AUTOVERIFICACIÓN (tras cada commit)

cd portal && php artisan test && ./vendor/bin/pint
Además genera un PDF real en local (tinker o test que guarde el binario en
storage temporal) y verifica que dompdf no arroje warnings y que el
contenido quepa en una página.

ESCALAR EN VEZ DE INVENTAR

- Si "No. De Ficha" no debe ser folio_examen (p. ej. debe ser
  folio_registro), deja el mapeo a folio_examen, coméntalo en el commit y
  pausa para confirmar antes de dar por cerrado el goal.
- Si el nombre/clave del plantel o el periodo no coinciden con el sembrado
  local, usa siempre los datos del modelo (nunca literales) y señálalo.
- Cualquier campo de la maqueta imposible de mapear con el esquema actual.
```

---

## Después del goal (rol coordinador/auditor)

1. Revisar el diff: que solo cambien la vista v2026, FormatoController
   (eager loading), config/portal.php (si aplica) y tests.
2. Verificación manual: registrar un alumno de prueba completo, descargar su
   PDF y compararlo lado a lado con el formato en papel (orden de secciones,
   etiquetas, casillas, leyenda de privacidad, firma). Probar también un
   proceso con campos opcionales vacíos (sin madre, sin folio de examen).
3. Confirmar que cabe en una página con nombres largos (apellidos compuestos,
   escuela de nombre largo).
4. Si se consigue el logo oficial en buena resolución, colocarlo en
   `portal/resources/images/pdf/logo-cobaem.png` y regenerar para validar.

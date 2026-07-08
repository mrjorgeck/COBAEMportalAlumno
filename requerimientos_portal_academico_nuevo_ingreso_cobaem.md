# Requerimientos del Sistema  
# Portal Académico de Nuevo Ingreso COBAEM

**Proyecto:** Portal Académico de Nuevo Ingreso  
**Plantel:** Colegio de Bachilleres del Estado de Michoacán, Plantel Ario de Rosales  
**Propósito del documento:** Definir requerimientos funcionales, no funcionales, modelo de operación, datos, módulos y criterios de aceptación para que Claude prepare el diseño técnico del sistema.  
**Versión:** 1.0  
**Estado:** Borrador funcional para diseño técnico  

---

## 1. Instrucción para Claude

Actúa como arquitecto de software senior, analista funcional y diseñador técnico.  
A partir de este documento, prepara un **diseño técnico completo** para construir el sistema descrito.

El diseño técnico debe incluir:

1. Arquitectura general del sistema.
2. Stack tecnológico recomendado.
3. Modelo de datos relacional.
4. Diagrama lógico de módulos.
5. Diseño de roles y permisos.
6. Flujos principales de usuario.
7. Endpoints/API sugeridos.
8. Diseño de importación/exportación CSV.
9. Diseño del generador de PDF.
10. Diseño del módulo OMR para extracción de respuestas desde fotografías.
11. Diseño de dashboards.
12. Diseño de catálogos administrables.
13. Seguridad, privacidad y auditoría.
14. Estrategia de implementación por fases.
15. Backlog técnico priorizado.
16. Criterios de aceptación.
17. Riesgos técnicos y mitigaciones.
18. Recomendación para MVP.

Restricciones importantes:

- El sistema debe ser usable desde celular.
- Debe ser simple para alumnos de nuevo ingreso.
- Debe funcionar con recursos limitados del plantel.
- No debe depender de comprar muchos equipos nuevos.
- Debe proteger datos personales.
- Debe poder reutilizarse en ciclos escolares futuros.
- Debe permitir exportar e importar información mediante CSV.
- Debe separar edición de datos y consulta posterior.
- Debe permitir que el alumno consulte su proceso aunque ya no pueda editar.
- Debe considerar que el examen diagnóstico se aplicará físicamente, no dentro del portal.
- El portal debe servir como precursor de un sistema más amplio de gestión de desempeño académico.

---

## 2. Contexto general

Cada año, el plantel aplica a los alumnos de nuevo ingreso una evaluación diagnóstica federal para estudiantes que ingresarán a primer semestre.  
Aunque la evaluación está diseñada para contestarse en línea, por condiciones operativas se decidió mantener la aplicación bajo el esquema tradicional:

1. Se imprime el cuadernillo del examen.
2. Se imprime la hoja de respuestas.
3. El alumno responde físicamente.
4. El personal recoge las hojas.
5. Posteriormente se capturan o procesan las respuestas para alimentar la plataforma de evaluación.

Actualmente, además del examen, los alumnos llenan formatos físicos con datos personales y escolares para control escolar.  
Después, el personal captura manualmente esos datos en SICOBaEM o en los sistemas administrativos correspondientes.

El nuevo sistema busca digitalizar el proceso alrededor del examen, aunque el examen se mantenga físico.

---

## 3. Objetivo general del sistema

Crear un **Portal Académico de Nuevo Ingreso** que permita:

1. Registrar digitalmente los datos personales, escolares y de contacto del alumno.
2. Asociar cada registro con el folio de su hoja de respuestas del examen diagnóstico.
3. Generar automáticamente el formato de inscripción/control escolar en PDF, prellenado con los datos capturados.
4. Permitir que el alumno pueda descargar posteriormente su formato usando su CURP.
5. Dar seguimiento al proceso de ingreso mediante autoservicio del alumno.
6. Registrar y consultar el estado de entrega de documentación.
7. Extraer respuestas del examen desde fotografías o escaneos de hojas de respuesta mediante un módulo OMR.
8. Enriquecer el archivo de respuestas con datos del alumno.
9. Generar archivos exportables para carga masiva en la plataforma correspondiente.
10. Cargar resultados del examen diagnóstico.
11. Calcular indicadores de desempeño por alumno, área, materia, secundaria de procedencia, grupo propedéutico y grupo escolar.
12. Preparar una base de análisis para docentes, directivos y responsables del curso propedéutico.
13. Permitir una segunda evaluación después del curso propedéutico y comparar avances.
14. Mostrar al alumno resultados, áreas de mejora, materiales recomendados y avisos.
15. Publicar grupo propedéutico, grupo escolar, matrícula, horario de clases e instrucciones de activación de SICOBaEM.
16. Conservar histórico por ciclo de ingreso para reutilizar el portal en futuros años.

---

## 4. Alcance del sistema

### 4.1 Incluido en el alcance inicial

El sistema debe incluir:

1. Portal del alumno.
2. Registro digital de datos personales.
3. Captura de folio de examen.
4. Generación de formato PDF de inscripción.
5. Descarga posterior del formato usando CURP.
6. Panel administrativo de control escolar.
7. Consulta de alumnos por CURP, nombre, folio de examen y ciclo de ingreso.
8. Exportación CSV de la base de alumnos.
9. Importación CSV para actualización o alta de registros.
10. Administración de catálogos.
11. Identificación por ciclo/año de ingreso.
12. Dashboard administrativo de registros.
13. Dashboard académico de resultados.
14. Estado de documentación del alumno.
15. Carga/importación de resultados de evaluación diagnóstica.
16. Consulta de resultados por alumno.
17. Identificación de áreas de mejora.
18. Materiales y recomendaciones académicas.
19. Espacio para conexión futura con plataforma de regularización autodirigida.
20. Asignación y consulta de grupo propedéutico.
21. Registro y consulta de segunda evaluación posterior al curso propedéutico.
22. Comparativo entre evaluación inicial y evaluación posterior.
23. Asignación y consulta de grupo escolar.
24. Consulta de matrícula escolar.
25. Consulta de horario de clases.
26. Instrucciones para activación/acceso a SICOBaEM.
27. Avisos y notificaciones internas.
28. Bloqueo de edición sin bloquear consulta.
29. Opción para recordar CURP en el navegador, con consentimiento.
30. Módulo piloto para extracción automatizada de respuestas desde fotografía o escaneo.

### 4.2 Fuera del alcance inicial

Queda fuera del alcance inicial:

1. Aplicar el examen dentro del portal.
2. Sustituir completamente la plataforma federal.
3. Integración automática directa con SICOBaEM, salvo que exista API o mecanismo autorizado.
4. OCR completo de texto manuscrito.
5. App móvil nativa.
6. Seguimiento académico completo de todos los semestres.
7. Expediente digital completo con carga de documentos por parte del alumno.
8. Envío de notificaciones por WhatsApp/SMS, salvo que se defina en una fase posterior.
9. Plataforma de regularización autodirigida completa.
10. Portal docente completo para seguimiento semestral.

---

## 5. Actores del sistema

| Actor | Descripción |
|---|---|
| Alumno | Aspirante o alumno de nuevo ingreso que registra información, consulta avances y descarga formatos. |
| Control escolar | Personal responsable de validar datos, documentación, formatos y registros. |
| Coordinación académica | Personal que consulta resultados, asigna grupos propedéuticos y analiza desempeño. |
| Dirección | Consulta indicadores generales, avance del proceso y resultados académicos. |
| Docente | En fases posteriores, consulta diagnóstico de alumnos o grupos asignados. |
| Responsable técnico | Administra configuración técnica, importaciones, catálogos y procesamiento OMR. |
| Administrador del sistema | Gestiona usuarios, permisos, ciclos, catálogos, avisos, publicación de módulos y configuración general. |

---

## 6. Flujo general del proceso

```text
1. El plantel imprime examen y hoja de respuestas.
2. El alumno presenta el examen diagnóstico físicamente.
3. El personal recoge la hoja de respuestas.
4. Al terminar el examen, el alumno entra al portal.
5. El alumno ingresa CURP.
6. El alumno captura el folio de su hoja de respuestas.
7. El alumno llena sus datos personales, escolares, familiares y de contacto.
8. El sistema valida la información.
9. El sistema guarda el registro y genera folio interno.
10. El sistema genera formato PDF de inscripción.
11. El alumno descarga el formato.
12. El alumno imprime y entrega el formato a control escolar junto con documentación requerida.
13. Control escolar valida documentación y actualiza estado.
14. El personal fotografía o escanea hojas de respuesta.
15. El sistema procesa las hojas mediante OMR.
16. El sistema extrae respuestas y las vincula con el alumno mediante folio de examen.
17. El sistema genera archivo enriquecido para carga o análisis.
18. Se cargan o calculan resultados de evaluación diagnóstica.
19. El alumno consulta resultados, áreas de mejora y recomendaciones.
20. Coordinación asigna grupo propedéutico.
21. El alumno consulta grupo propedéutico e indicaciones.
22. Se aplica curso propedéutico.
23. Se registra segunda evaluación.
24. El alumno consulta resultados posteriores y avance.
25. Control escolar carga matrícula, grupo escolar y horario.
26. El alumno consulta grupo, matrícula, horario e instrucciones de SICOBaEM.
27. El sistema conserva histórico por ciclo de ingreso.
```

---

## 7. Portal del alumno

### 7.1 Acceso del alumno

El alumno debe poder acceder al portal usando su CURP.

Requerimientos:

- El campo CURP debe aceptar 18 caracteres.
- Debe validarse formato básico de CURP.
- El portal debe permitir localizar el proceso de ingreso vigente del alumno.
- Si el alumno tiene más de un proceso, deberá seleccionarse el ciclo correspondiente.
- Debe existir opción para recordar la CURP en el navegador, con consentimiento.

### 7.2 Recordar CURP en navegador

El portal debe incluir una opción:

> “Recordar mi CURP en este dispositivo”

Condiciones:

- Debe estar desactivada por defecto.
- Debe mostrar advertencia de privacidad.
- Solo debe usarse en dispositivos personales.
- Debe permitir borrar la CURP guardada.
- No debe guardar resultados sensibles en caché.
- No debe mantener una sesión administrativa.
- Para información sensible se recomienda solicitar un segundo dato, como fecha de nacimiento o folio de examen.

Texto sugerido:

> Puedes recordar tu CURP en este dispositivo para entrar más rápido. Activa esta opción solo si el celular es tuyo o de confianza. No la actives en equipos compartidos.

### 7.3 Secciones del portal del alumno

| Sección | Descripción |
|---|---|
| Mi proceso | Resumen visual del avance del alumno. |
| Mis datos | Consulta y edición de datos mientras el periodo esté abierto. |
| Formato de inscripción | Descarga del PDF prellenado. |
| Documentación | Estado de documentos entregados, pendientes o rechazados. |
| Evaluación diagnóstica | Folio, estado del examen y resultados. |
| Áreas de mejora | Materias o áreas que requieren refuerzo. |
| Materiales recomendados | Recursos sugeridos según desempeño. |
| Regularización autodirigida | Espacio preparado para conexión futura con plataforma externa. |
| Curso propedéutico | Grupo, aula, horario, responsable e indicaciones. |
| Evaluación posterior | Resultado después del curso propedéutico. |
| Mi avance | Comparativo entre diagnóstico inicial y evaluación posterior. |
| Grupo escolar | Grupo definitivo asignado. |
| Matrícula | Nueva matrícula escolar. |
| Horario | Horario de clases. |
| SICOBaEM | Instrucciones para activar o consultar su acceso. |
| Avisos | Notificaciones institucionales. |

### 7.4 Tablero de avance del alumno

Al ingresar, el alumno debe ver el estado general del proceso.

Ejemplo:

| Etapa | Estado |
|---|---|
| Registro de datos | Completado |
| Formato de inscripción | Disponible |
| Documentación | Pendiente de validación |
| Evaluación diagnóstica | Aplicada |
| Resultados diagnóstico | Disponibles |
| Curso propedéutico | Grupo asignado |
| Evaluación posterior | Pendiente |
| Grupo escolar | Por asignar |
| Matrícula | Por asignar |
| Horario | Por publicar |
| Acceso SICOBaEM | Pendiente |

Estados sugeridos:

- Pendiente.
- En proceso.
- Completado.
- Requiere corrección.
- Validado.
- Rechazado.
- Disponible.
- No disponible todavía.
- Publicado.

---

## 8. Registro digital del alumno

### 8.1 Información que debe capturar el alumno

El formulario debe capturar información equivalente al formato físico de solicitud de inscripción.

### 8.2 Encabezado / datos administrativos

| Campo | Requerido | Tipo |
|---|---:|---|
| Periodo escolar | Sí | Catálogo |
| Ciclo de ingreso | Sí | Catálogo |
| Plantel | Sí | Catálogo |
| Clave del plantel | Sí | Catálogo/automático |
| No. de ficha / folio de examen | Sí | Texto/número |
| Folio interno del portal | Sí | Generado |
| Semestre solicitado | Sí | Catálogo |
| Tipo de estudiante | Sí | Catálogo |
| Paraescolar | No | Catálogo/texto |

Tipo de estudiante:

- Regular.
- Repetidor.
- Condicionado.
- Debe materias secundaria.

### 8.3 Datos del estudiante

| Campo | Requerido | Tipo |
|---|---:|---|
| Nombre(s) | Sí | Texto |
| Primer apellido | Sí | Texto |
| Segundo apellido | No | Texto |
| Estado civil | Sí | Catálogo |
| Fecha de nacimiento | Sí | Fecha |
| Edad | Sí | Calculado |
| Sexo | Sí | Catálogo |
| Nacionalidad | Sí | Catálogo |
| Entidad de nacimiento | Sí | Catálogo |
| Municipio de nacimiento | Sí | Catálogo dependiente |
| CURP | Sí | Texto validado |

### 8.4 Dirección particular actual

| Campo | Requerido | Tipo |
|---|---:|---|
| Municipio | Sí | Catálogo |
| Localidad | Sí | Catálogo dependiente |
| Código postal | No | Texto/número |
| Domicilio, calle y número | Sí | Texto |
| Colonia | No | Catálogo/texto |

### 8.5 Datos de contacto actual

| Campo | Requerido | Tipo |
|---|---:|---|
| Teléfono | No | Teléfono |
| Celular | Sí | Teléfono |
| Correo electrónico | No | Email |

### 8.6 Escuela de procedencia

| Campo | Requerido | Tipo |
|---|---:|---|
| Entidad | Sí | Catálogo |
| Municipio | Sí | Catálogo dependiente |
| Nombre de la escuela | Sí | Catálogo/texto |
| Tipo de secundaria | No | Catálogo |
| Turno de secundaria | No | Catálogo |
| Promedio | Sí | Decimal |

### 8.7 Datos del tutor

| Campo | Requerido | Tipo |
|---|---:|---|
| Nombre(s) | Sí | Texto |
| Primer apellido | Sí | Texto |
| Segundo apellido | No | Texto |
| Teléfono | No | Teléfono |
| Celular | Sí | Teléfono |
| Ocupación | No | Catálogo/texto |
| Estudios | No | Catálogo |

### 8.8 Datos de madre

| Campo | Requerido | Tipo |
|---|---:|---|
| Nombre(s) | No | Texto |
| Primer apellido | No | Texto |
| Segundo apellido | No | Texto |
| Teléfono | No | Teléfono |
| Celular | No | Teléfono |
| Ocupación | No | Catálogo/texto |
| Máximo grado de estudios | No | Catálogo |

### 8.9 Otros datos

| Campo | Requerido | Tipo |
|---|---:|---|
| No. de seguro médico | No | Texto |
| Becas | No | Catálogo/texto |
| Estatura | No | Decimal |
| Peso | No | Decimal |
| Tipo de sangre RH | No | Catálogo |
| Lugar y fecha actual | Sí | Generado/capturado |
| Aceptación de aviso de privacidad | Sí | Checkbox |
| Nombre completo para firma | Sí | Generado |

---

## 9. Formato PDF de inscripción

El sistema debe generar un PDF similar al formato físico institucional.

### 9.1 Características del PDF

- Debe incluir encabezado institucional.
- Debe incluir periodo escolar.
- Debe incluir plantel y clave.
- Debe incluir No. de ficha/folio de examen.
- Debe incluir secciones visuales del formato físico.
- Debe mostrar los datos capturados por el alumno.
- Debe dejar espacio para firma si se requiere.
- Debe incluir aceptación de aviso de privacidad.
- Debe poder descargarse desde el portal del alumno.
- Debe poder descargarse desde el panel de control escolar.
- Debe poder regenerarse si se corrigen datos antes del bloqueo.
- Debe registrar fecha de generación y descarga.

### 9.2 Secciones del PDF

1. Encabezado.
2. Datos del estudiante.
3. Dirección particular actual.
4. Datos de contacto actual.
5. Escuela de procedencia.
6. Datos de tutor.
7. Datos de madre.
8. Otros datos.
9. Aviso de privacidad.
10. Nombre completo y firma.

---

## 10. Panel administrativo de control escolar

### 10.1 Funciones principales

Control escolar debe poder:

1. Ver dashboard de registros.
2. Consultar alumnos por CURP, nombre, folio de examen, folio interno, ciclo y estado.
3. Ver detalle del alumno.
4. Descargar formato PDF individual.
5. Editar información si tiene permisos.
6. Marcar documentación como recibida, validada, rechazada o pendiente.
7. Agregar observaciones.
8. Exportar CSV con base completa.
9. Importar CSV para actualizar datos.
10. Consultar historial de cambios.
11. Filtrar por ciclo de ingreso, generación y periodo.
12. Bloquear edición de alumnos.
13. Publicar módulos visibles para alumnos.
14. Ver estado de descarga de formatos.

### 10.2 Dashboard de control escolar

Indicadores sugeridos:

| Indicador |
|---|
| Total de alumnos registrados |
| Registros completos |
| Registros incompletos |
| Alumnos sin folio de examen |
| Folios duplicados |
| CURP duplicadas |
| Formatos generados |
| Formatos descargados |
| Documentación pendiente |
| Documentación validada |
| Documentación rechazada |
| Alumnos con grupo propedéutico asignado |
| Alumnos con grupo escolar asignado |
| Alumnos con matrícula publicada |

---

## 11. Documentación del alumno

El sistema debe permitir registrar el estado de documentación entregada físicamente.

### 11.1 Documentos iniciales sugeridos

| Documento |
|---|
| Acta de nacimiento |
| CURP |
| Certificado de secundaria |
| Comprobante de domicilio |
| Fotografías |
| Solicitud de inscripción firmada |
| Comprobante de pago, si aplica |
| Otros documentos definidos por control escolar |

### 11.2 Estados de documento

- Pendiente.
- Recibido.
- Validado.
- Rechazado.
- Requiere corrección.
- No aplica.

### 11.3 Vista para alumno

El alumno debe ver:

| Documento | Estado | Observación |
|---|---|---|
| Acta de nacimiento | Validado |  |
| Certificado de secundaria | Pendiente | Entregar copia |
| Comprobante de domicilio | Rechazado | Debe ser reciente |

---

## 12. Módulo de extracción automatizada de respuestas OMR

### 12.1 Objetivo

Automatizar la lectura de respuestas marcadas en hojas físicas a partir de fotografía o escaneo.

### 12.2 Alcance técnico

El sistema debe priorizar OMR, no OCR.

- OMR: lectura de círculos o marcas de respuesta.
- OCR manuscrito: fuera del alcance inicial, salvo como referencia visual o validación manual.

La información manuscrita de la hoja no debe ser la fuente principal de datos personales.  
La fuente principal será el registro digital del alumno.

### 12.3 Flujo OMR

```text
1. Personal sube imagen de hoja de respuestas.
2. Sistema almacena imagen original.
3. Sistema detecta bordes de hoja.
4. Sistema corrige perspectiva e inclinación.
5. Sistema identifica zonas de respuestas.
6. Sistema detecta marcas por pregunta.
7. Sistema detecta folio si está en formato leíble o permite capturarlo manualmente.
8. Sistema calcula nivel de confianza.
9. Sistema marca casos dudosos para revisión manual.
10. Personal valida o corrige.
11. Sistema guarda respuestas validadas.
12. Sistema cruza con registro del alumno mediante folio de examen.
13. Sistema exporta respuestas.
```

### 12.4 Estados de procesamiento

| Estado | Descripción |
|---|---|
| Pendiente | Imagen subida, sin procesar. |
| Procesada | Respuestas detectadas. |
| Requiere revisión | Lectura dudosa, imagen borrosa, doble marca o pregunta sin marca. |
| Validada | Personal confirmó lectura. |
| Exportada | Respuestas incluidas en archivo final. |
| Error | No fue posible procesar. |

### 12.5 Requerimientos del módulo OMR

| ID | Requerimiento |
|---|---|
| OMR-01 | El sistema permitirá subir fotografías o escaneos de hojas de respuesta. |
| OMR-02 | El sistema almacenará imagen original y versión procesada. |
| OMR-03 | El sistema permitirá configurar plantilla de zonas de respuesta. |
| OMR-04 | El sistema detectará marcas por pregunta. |
| OMR-05 | El sistema identificará preguntas sin respuesta. |
| OMR-06 | El sistema identificará preguntas con doble marca. |
| OMR-07 | El sistema calculará nivel de confianza por respuesta. |
| OMR-08 | El sistema permitirá corrección manual. |
| OMR-09 | El sistema permitirá validar una hoja procesada. |
| OMR-10 | El sistema exportará respuestas por folio. |
| OMR-11 | El sistema vinculará respuestas con alumno mediante folio de examen. |
| OMR-12 | El sistema generará reporte de hojas procesadas, dudosas y con error. |

---

## 13. Evaluación diagnóstica y resultados

### 13.1 Datos de evaluación

El sistema debe permitir cargar o calcular resultados de evaluación diagnóstica.

Debe manejar:

1. Evaluación inicial.
2. Evaluación posterior al curso propedéutico.
3. Comparativo de avance.

### 13.2 Clave de respuestas

El sistema debe permitir cargar clave de respuestas por examen. Una pregunta puede tener una o más respuestas válidas cuando el instrumento lo indique; para importación se aceptan opciones separadas por coma, diagonal o sin separador, normalizadas internamente como lista (`B,C`).

Campos mínimos:

| Campo |
|---|
| Examen |
| Pregunta |
| Respuesta correcta o respuestas válidas |
| Área |
| Materia |
| Competencia |
| Ponderación |

### 13.3 Áreas del examen

El sistema debe permitir administrar áreas o materias del examen mediante catálogo.

Ejemplos:

- Matemáticas.
- Comprensión lectora.
- Ciencias.
- Ciencias sociales.
- Comunicación.
- Habilidades socioemocionales.
- Otra área definida por la evaluación.

### 13.4 Resultados por alumno

El sistema debe calcular o importar:

| Campo |
|---|
| Puntaje total |
| Porcentaje total |
| Resultado por área |
| Nivel de desempeño |
| Nivel de riesgo |
| Áreas de mejora |
| Recomendaciones |

### 13.5 Niveles de riesgo sugeridos

| Nivel | Criterio inicial sugerido |
|---|---|
| Bajo | >= 80% |
| Medio | 60% a 79% |
| Alto | 40% a 59% |
| Crítico | < 40% |

Los rangos deben ser configurables.

---

## 14. Resultados y recomendaciones para alumno

El alumno debe poder consultar:

1. Resultado general.
2. Resultado por área.
3. Nivel de desempeño.
4. Áreas de mejora.
5. Materiales recomendados.
6. Recomendaciones para el curso propedéutico.
7. Comparativo con segunda evaluación cuando exista.

Ejemplo de visualización:

| Área | Resultado | Diagnóstico | Recomendación |
|---|---:|---|---|
| Matemáticas | 48% | Requiere refuerzo | Repasar operaciones básicas, fracciones y álgebra inicial. |
| Comprensión lectora | 62% | Nivel medio | Practicar lectura de textos breves y preguntas inferenciales. |
| Ciencias | 71% | Adecuado | Reforzar conceptos clave antes del curso. |

---

## 15. Materiales y regularización autodirigida

### 15.1 Módulo de materiales recomendados

El sistema debe permitir cargar materiales por:

- Área.
- Materia.
- Nivel de desempeño.
- Tipo de recurso.
- URL.
- Archivo, si aplica.
- Estado activo/inactivo.

Tipos de recurso:

- PDF.
- Video.
- Guía.
- Actividad.
- Sitio web.
- Curso externo.
- Plataforma de regularización.

### 15.2 Espacio para plataforma de regularización

Debe dejarse preparada una sección para conexión futura con una plataforma de regularización autodirigida.

Campos sugeridos:

| Campo |
|---|
| ruta_regularizacion_id |
| plataforma_externa_url |
| materiales_recomendados |
| estatus_regularizacion |
| fecha_asignacion |
| fecha_ultima_consulta |

---

## 16. Curso propedéutico

### 16.1 Datos del grupo propedéutico

El sistema debe permitir publicar:

| Campo |
|---|
| Grupo propedéutico |
| Aula |
| Horario |
| Fecha de inicio |
| Fecha de fin |
| Responsable |
| Indicaciones |
| Materiales requeridos |

### 16.2 Consulta del alumno

El alumno debe ver:

> Tu grupo propedéutico asignado es: P-03  
> Horario: 8:00 a 10:00 a.m.  
> Aula: Laboratorio 1  
> Inicio: lunes 12 de agosto  
> Indicaciones: presentarse con cuaderno, lápiz y comprobante de registro.

---

## 17. Segunda evaluación posterior al propedéutico

El sistema debe permitir cargar una segunda evaluación posterior al curso propedéutico.

### 17.1 Funciones

1. Cargar resultados de segunda evaluación.
2. Relacionar resultados con el mismo alumno y ciclo.
3. Comparar contra evaluación inicial.
4. Calcular avance por área.
5. Calcular avance total.
6. Mostrar comparativo al alumno.
7. Mostrar dashboard a docentes y dirección.

### 17.2 Ejemplo de comparativo

| Área | Diagnóstico inicial | Después del propedéutico | Avance |
|---|---:|---:|---:|
| Matemáticas | 48% | 68% | +20 |
| Lectura | 62% | 74% | +12 |
| Ciencias | 71% | 78% | +7 |
| Total | 60% | 73% | +13 |

---

## 18. Grupo escolar, matrícula y horario

### 18.1 Datos a publicar

| Campo |
|---|
| Matrícula |
| Grupo escolar |
| Turno |
| Aula base |
| Horario |
| Fecha de inicio de clases |
| Indicaciones |

### 18.2 Horario

El horario puede cargarse por grupo y mostrarse al alumno según su grupo escolar.

Campos sugeridos:

| Campo |
|---|
| Día |
| Hora inicio |
| Hora fin |
| Materia |
| Docente |
| Aula |

---

## 19. Instrucciones de acceso a SICOBaEM

El portal debe tener una sección administrable con instrucciones para activar o consultar el acceso a SICOBaEM.

Debe permitir configurar:

- URL del sistema.
- Fecha de disponibilidad.
- Usuario sugerido, si aplica.
- Contraseña inicial, si aplica.
- Pasos de activación.
- Contacto de soporte.
- Horario de atención.
- Mensajes por ciclo.

Ejemplo de texto:

```text
Tu acceso a SICOBaEM estará disponible cuando control escolar publique tu matrícula.

Para ingresar:
1. Entra al portal oficial indicado por el plantel.
2. Usa tu matrícula como usuario, si así se indica.
3. Sigue las instrucciones de activación.
4. Cambia tu contraseña en el primer acceso.
5. Si tienes problemas, acude a control escolar.
```

---

## 20. Avisos y notificaciones internas

### 20.1 Funciones

El administrador debe poder crear avisos visibles para alumnos.

Campos:

| Campo |
|---|
| Título |
| Mensaje |
| Tipo |
| Prioridad |
| Fecha de inicio |
| Fecha de fin |
| Dirigido a todos / grupo / ciclo / alumno |
| Visible |
| Archivo o enlace opcional |

Tipos sugeridos:

- General.
- Documentación.
- Académico.
- Propedéutico.
- Control escolar.
- Horario.
- SICOBaEM.

Prioridades:

- Informativo.
- Importante.
- Urgente.

### 20.2 Lectura de avisos

El sistema debe registrar si un alumno leyó un aviso, cuando sea posible.

---

## 21. Administración de catálogos

### 21.1 Objetivo

El sistema debe usar listas desplegables en todos los campos donde sea posible, alimentadas por catálogos administrables desde la plataforma.

Esto busca:

1. Reducir errores de captura.
2. Evitar variaciones en nombres.
3. Facilitar filtros.
4. Mejorar reportes.
5. Hacer el sistema reutilizable en futuros ciclos.
6. Evitar depender de cambios en código.

### 21.2 Catálogos prioritarios para MVP

| Catálogo | Uso |
|---|---|
| Ciclos de ingreso | Separar generaciones. |
| Periodos escolares | Documentos y filtros. |
| Planteles | Datos institucionales. |
| Sexo | Datos del alumno. |
| Estado civil | Formato de inscripción. |
| Nacionalidad | Datos del alumno. |
| Entidades federativas | Nacimiento y escuela de procedencia. |
| Municipios | Nacimiento, domicilio y escuela. |
| Localidades | Domicilio. |
| Secundarias de procedencia | Análisis académico. |
| Tipo de secundaria | Análisis académico. |
| Turnos | Escuela de procedencia y grupo. |
| Tipo de sangre RH | Otros datos. |
| Estado de documentación | Control escolar. |
| Grupos propedéuticos | Asignación. |
| Grupos escolares | Asignación final. |
| Áreas de evaluación | Dashboard. |
| Niveles de desempeño | Resultados. |
| Niveles de riesgo | Indicadores. |
| Tipos de aviso | Notificaciones. |
| Prioridad de aviso | Notificaciones. |

### 21.3 Catálogos deseables para segunda etapa

| Catálogo |
|---|
| Colonias |
| Ocupaciones |
| Niveles de estudios |
| Becas |
| Materiales recomendados |
| Rutas de regularización |
| Horarios |
| Materias |
| Docentes |

### 21.4 Requerimientos de catálogos

| ID | Requerimiento |
|---|---|
| CAT-01 | El administrador podrá crear, editar, activar e inactivar valores de catálogo. |
| CAT-02 | El sistema deberá conservar valores históricos aunque se inactive un catálogo. |
| CAT-03 | El sistema permitirá catálogos dependientes, por ejemplo entidad → municipio → localidad. |
| CAT-04 | Los formularios deberán usar desplegables donde exista catálogo. |
| CAT-05 | Los catálogos deberán poder exportarse e importarse por CSV si es viable. |
| CAT-06 | El sistema deberá evitar duplicados evidentes dentro de un catálogo. |
| CAT-07 | Los catálogos deberán tener orden de visualización. |
| CAT-08 | Los catálogos deberán permitir valores activos/inactivos. |

---

## 22. Identificadores y ciclos de ingreso

### 22.1 Identificadores obligatorios

El sistema debe manejar al menos cuatro identificadores:

| Identificador | Uso |
|---|---|
| CURP | Identidad principal del alumno. |
| Ciclo de ingreso | Separar generaciones. |
| Folio interno del portal | Control administrativo del sistema. |
| Folio de examen | Cruce con hoja física de respuestas. |
| Matrícula | Identificador escolar posterior. |

### 22.2 Ciclo de ingreso

Cada registro debe estar asociado a:

| Campo | Ejemplo |
|---|---|
| ciclo_ingreso | 2026 |
| periodo_escolar | 26-2 |
| generacion | Nuevo ingreso 2026 |
| plantel_id | ARIO |
| folio_registro | NI-2026-ARIO-0001 |
| folio_examen | 000345 |

### 22.3 Folio interno

Formato sugerido:

```text
NI-{AÑO}-{PLANTEL}-{CONSECUTIVO}
```

Ejemplos:

```text
NI-2026-ARIO-0001
NI-2026-ARIO-0002
NI-2027-ARIO-0001
```

### 22.4 Reglas de unicidad

| Campo | Regla |
|---|---|
| CURP | Única como identidad del alumno. |
| CURP + ciclo_ingreso | Único para proceso de ingreso. |
| Folio interno | Único global o único por plantel/ciclo. |
| Folio de examen + ciclo_ingreso | Único, salvo corrección autorizada. |
| Matrícula | Única cuando se asigne. |

---

## 23. Importación y exportación CSV

### 23.1 Exportación CSV

El panel administrativo debe permitir exportar:

1. Base completa de alumnos registrados.
2. Base filtrada por ciclo, grupo, estatus, secundaria, etc.
3. Resultados de evaluación.
4. Estado de documentación.
5. Archivo enriquecido con datos personales y respuestas.
6. Archivo para carga masiva a plataforma externa, si se define estructura.

### 23.2 Importación CSV

El sistema debe permitir importar CSV para:

1. Alta o actualización de alumnos.
2. Actualización de documentación.
3. Carga de resultados de examen.
4. Asignación de grupo propedéutico.
5. Carga de segunda evaluación.
6. Asignación de grupo escolar.
7. Carga de matrícula.
8. Carga de horarios.
9. Actualización de catálogos, si se habilita.

### 23.3 Llave de actualización

Regla principal:

> La CURP será la llave principal del alumno, pero los procesos de importación, consulta y análisis deberán considerar también el ciclo de ingreso.

Reglas:

| Caso | Acción |
|---|---|
| CURP + ciclo de ingreso existen | Actualizar proceso. |
| CURP existe, pero ciclo diferente | Crear nuevo proceso asociado al alumno. |
| CURP no existe | Crear alumno y proceso de ingreso. |
| Folio de examen duplicado en mismo ciclo | Marcar alerta. |
| Folio de examen repetido en diferente ciclo | Permitido con advertencia. |
| CURP inválida | Mandar a errores. |
| Registro incompleto | Marcar como pendiente. |

### 23.4 Reporte de importación

Cada importación debe generar reporte:

| Métrica |
|---|
| Total de filas recibidas |
| Registros creados |
| Registros actualizados |
| Registros sin cambios |
| Registros con error |
| CURP inválidas |
| CURP duplicadas |
| Folios duplicados |
| Campos faltantes |
| Archivo original |
| Usuario que importó |
| Fecha de importación |

---

## 24. Dashboard académico

### 24.1 Objetivo

Brindar información para diagnóstico oportuno de nuevo ingreso, curso propedéutico y planeación académica.

### 24.2 Indicadores principales

| Indicador |
|---|
| Total de alumnos registrados |
| Total de alumnos evaluados |
| Promedio general del examen |
| Promedio por área |
| Alumnos en riesgo alto |
| Alumnos en riesgo crítico |
| Comparación promedio secundaria vs resultado diagnóstico |
| Resultados por secundaria de procedencia |
| Resultados por tipo de secundaria |
| Resultados por grupo propedéutico |
| Resultados por grupo escolar |
| Avance entre evaluación inicial y posterior |
| Preguntas con menor porcentaje de acierto |
| Áreas con mayor rezago |
| Alumnos sin resultado |
| Alumnos sin grupo asignado |

### 24.3 Vistas sugeridas

#### Vista dirección

- Cobertura de registro.
- Cobertura de evaluación.
- Promedio general.
- Distribución de niveles de riesgo.
- Comparativo por secundaria de procedencia.
- Avance del propedéutico.
- Indicadores por grupo.

#### Vista académica/docente

- Resultados por área.
- Resultados por grupo.
- Preguntas con menor acierto.
- Competencias con mayor rezago.
- Alumnos que requieren apoyo.

#### Vista propedéutico

- Diagnóstico inicial.
- Priorización de temas.
- Comparación evaluación 1 vs evaluación 2.
- Avance individual.
- Avance grupal.
- Alumnos sin mejora.

#### Vista control escolar

- Registros completos.
- Registros incompletos.
- Documentación pendiente.
- Formatos generados.
- Alumnos sin folio.
- Duplicados.
- Exportaciones.

---

## 25. Seguridad, privacidad y auditoría

### 25.1 Datos sensibles

El sistema manejará datos personales, incluyendo:

- CURP.
- Nombre.
- Fecha de nacimiento.
- Domicilio.
- Teléfono.
- Correo.
- Datos familiares.
- Datos escolares.
- Datos médicos básicos, como tipo de sangre, estatura, peso o seguro médico.
- Resultados académicos.

### 25.2 Requerimientos de seguridad

| ID | Requerimiento |
|---|---|
| SEG-01 | El panel administrativo deberá requerir autenticación. |
| SEG-02 | El alumno accederá mediante CURP y, para secciones sensibles, segundo dato de validación si se define. |
| SEG-03 | El sistema debe evitar mostrar información de otros alumnos. |
| SEG-04 | El sistema debe registrar auditoría de cambios relevantes. |
| SEG-05 | Las exportaciones CSV deben estar disponibles solo para roles autorizados. |
| SEG-06 | La edición de datos debe poder bloquearse por ciclo, fecha o validación. |
| SEG-07 | El sistema debe incluir aviso de privacidad. |
| SEG-08 | El sistema debe permitir respaldos periódicos. |
| SEG-09 | Las imágenes de hojas de respuesta no deben ser públicas. |
| SEG-10 | No se deben guardar datos sensibles innecesarios en caché. |
| SEG-11 | La opción de recordar CURP debe requerir consentimiento. |
| SEG-12 | Debe existir control de roles y permisos. |

### 25.3 Auditoría

Eventos a registrar:

| Evento |
|---|
| Registro creado |
| Registro actualizado |
| Formato generado |
| Formato descargado |
| Documento validado |
| CSV importado |
| CSV exportado |
| Resultado cargado |
| Hoja procesada por OMR |
| Respuesta corregida manualmente |
| Alumno bloqueado/desbloqueado |
| Aviso publicado |
| Módulo publicado/despublicado |
| Inicio de sesión administrativo |

---

## 26. Roles y permisos

| Función | Alumno | Control escolar | Coordinación | Dirección | Técnico | Admin |
|---|---:|---:|---:|---:|---:|---:|
| Registrar datos propios | Sí | No | No | No | No | Sí |
| Editar datos propios antes del cierre | Sí | Sí | No | No | No | Sí |
| Descargar formato propio | Sí | Sí | No | No | No | Sí |
| Consultar documentación propia | Sí | Sí | No | No | No | Sí |
| Validar documentación | No | Sí | No | No | No | Sí |
| Exportar CSV | No | Sí | Sí | Sí | No | Sí |
| Importar CSV | No | Sí | Sí | No | Técnico | Sí |
| Procesar OMR | No | No | No | No | Sí | Sí |
| Corregir respuestas OMR | No | No | Sí | No | Sí | Sí |
| Ver dashboard académico | No | No | Sí | Sí | No | Sí |
| Ver resultados propios | Sí | Sí | Sí | Sí | No | Sí |
| Publicar avisos | No | Sí | Sí | Sí | No | Sí |
| Administrar catálogos | No | No | No | No | Técnico | Sí |
| Administrar usuarios | No | No | No | No | No | Sí |

---

## 27. Configuración por etapas

El administrador debe poder activar o desactivar módulos según el calendario.

| Módulo | Estado inicial recomendado |
|---|---|
| Registro de datos | Activo |
| Descarga de formato | Activo |
| Documentación | Activo |
| Resultados diagnóstico | Oculto hasta carga de resultados |
| Áreas de mejora | Oculto hasta cálculo |
| Materiales recomendados | Oculto hasta resultados |
| Grupo propedéutico | Oculto hasta asignación |
| Evaluación posterior | Oculto hasta aplicación |
| Comparativo de avance | Oculto hasta segunda evaluación |
| Grupo escolar | Oculto hasta asignación |
| Matrícula | Oculto hasta publicación |
| Horario | Oculto hasta publicación |
| SICOBaEM | Oculto hasta publicación |
| Avisos | Activo |
| Edición de datos | Activa solo durante periodo definido |

---

## 28. Modelo de datos conceptual sugerido

Claude debe convertir este modelo conceptual en un diseño técnico normalizado.

### 28.1 alumnos

Datos de identidad relativamente permanentes.

| Campo |
|---|
| id |
| curp |
| nombres |
| primer_apellido |
| segundo_apellido |
| nombre_completo |
| fecha_nacimiento |
| sexo_id |
| nacionalidad_id |
| estado_civil_id |
| entidad_nacimiento_id |
| municipio_nacimiento_id |
| created_at |
| updated_at |

### 28.2 procesos_ingreso

Datos específicos del ciclo de ingreso.

| Campo |
|---|
| id |
| alumno_id |
| ciclo_ingreso_id |
| periodo_escolar_id |
| generacion |
| plantel_id |
| folio_registro |
| folio_examen |
| semestre_solicitado |
| tipo_estudiante_id |
| paraescolar_id |
| promedio_secundaria |
| secundaria_procedencia_id |
| grupo_propedeutico_id |
| grupo_escolar_id |
| matricula |
| horario_id |
| estatus_proceso |
| estatus_documentacion |
| edicion_bloqueada |
| fecha_registro |
| fecha_validacion |
| created_at |
| updated_at |

### 28.3 datos_contacto

| Campo |
|---|
| id |
| alumno_id |
| proceso_ingreso_id |
| telefono |
| celular |
| correo |
| municipio_id |
| localidad_id |
| colonia |
| domicilio |
| codigo_postal |
| created_at |
| updated_at |

### 28.4 familiares

| Campo |
|---|
| id |
| alumno_id |
| proceso_ingreso_id |
| tipo_familiar |
| nombres |
| primer_apellido |
| segundo_apellido |
| telefono |
| celular |
| ocupacion_id |
| estudios_id |
| created_at |
| updated_at |

Tipos:

- Tutor.
- Madre.
- Padre.
- Otro.

### 28.5 otros_datos_alumno

| Campo |
|---|
| id |
| alumno_id |
| proceso_ingreso_id |
| no_seguro_medico |
| beca_id |
| estatura |
| peso |
| tipo_sangre_id |
| created_at |
| updated_at |

### 28.6 documentos_alumno

| Campo |
|---|
| id |
| proceso_ingreso_id |
| tipo_documento_id |
| estado_documento_id |
| observacion |
| fecha_recepcion |
| validado_por |
| fecha_validacion |
| created_at |
| updated_at |

### 28.7 examenes

| Campo |
|---|
| id |
| ciclo_ingreso_id |
| nombre |
| tipo_examen |
| fecha_aplicacion |
| version |
| activo |
| created_at |
| updated_at |

Tipos:

- Diagnóstico inicial.
- Evaluación posterior propedéutico.

### 28.8 hojas_respuesta

| Campo |
|---|
| id |
| examen_id |
| proceso_ingreso_id |
| folio_examen |
| imagen_original_path |
| imagen_procesada_path |
| estado_procesamiento |
| confianza_lectura |
| observaciones |
| fecha_subida |
| procesado_por |
| created_at |
| updated_at |

### 28.9 respuestas

| Campo |
|---|
| id |
| hoja_respuesta_id |
| pregunta |
| respuesta_detectada |
| respuesta_validada |
| confianza |
| requiere_revision |
| corregida_manualmente |
| created_at |
| updated_at |

### 28.10 claves_respuesta

| Campo |
|---|
| id |
| examen_id |
| pregunta |
| respuesta_correcta |
| area_id |
| materia_id |
| competencia |
| ponderacion |
| created_at |
| updated_at |

### 28.11 resultados

| Campo |
|---|
| id |
| proceso_ingreso_id |
| examen_id |
| folio_examen |
| puntaje_total |
| porcentaje_total |
| nivel_riesgo_id |
| nivel_desempeno_id |
| fecha_calculo |
| created_at |
| updated_at |

### 28.12 resultados_area

| Campo |
|---|
| id |
| resultado_id |
| area_id |
| puntaje |
| porcentaje |
| nivel_riesgo_id |
| recomendacion_id |
| created_at |
| updated_at |

### 28.13 grupos_propedeuticos

| Campo |
|---|
| id |
| ciclo_ingreso_id |
| nombre |
| aula |
| horario |
| fecha_inicio |
| fecha_fin |
| responsable |
| indicaciones |
| activo |

### 28.14 grupos_escolares

| Campo |
|---|
| id |
| ciclo_ingreso_id |
| grupo |
| semestre |
| turno_id |
| aula_base |
| activo |

### 28.15 horarios

| Campo |
|---|
| id |
| grupo_escolar_id |
| dia |
| hora_inicio |
| hora_fin |
| materia_id |
| docente |
| aula |

### 28.16 avisos

| Campo |
|---|
| id |
| titulo |
| mensaje |
| tipo_aviso_id |
| prioridad_id |
| fecha_inicio |
| fecha_fin |
| dirigido_a |
| ciclo_ingreso_id |
| grupo_propedeutico_id |
| grupo_escolar_id |
| alumno_id |
| visible |
| created_at |
| updated_at |

### 28.17 alumno_avisos

| Campo |
|---|
| id |
| alumno_id |
| aviso_id |
| leido |
| fecha_lectura |

### 28.18 materiales_recomendados

| Campo |
|---|
| id |
| area_id |
| nivel_desempeno_id |
| titulo |
| descripcion |
| url |
| tipo_material |
| activo |
| created_at |
| updated_at |

### 28.19 importaciones_csv

| Campo |
|---|
| id |
| tipo_importacion |
| archivo_original |
| fecha |
| usuario_id |
| total_filas |
| registros_creados |
| registros_actualizados |
| registros_sin_cambios |
| registros_error |
| resumen |
| created_at |

### 28.20 catalogos

Claude debe proponer si conviene una estructura genérica de catálogos o tablas específicas.

Ejemplo de estructura genérica:

| Campo |
|---|
| id |
| tipo_catalogo |
| clave |
| nombre |
| descripcion |
| parent_id |
| orden |
| activo |
| created_at |
| updated_at |

---

## 29. Requerimientos funcionales consolidados

| ID | Requerimiento |
|---|---|
| RF-01 | El alumno podrá registrarse con CURP y folio de examen. |
| RF-02 | El sistema validará formato de CURP. |
| RF-03 | El sistema guardará datos personales, escolares, familiares y de contacto. |
| RF-04 | El sistema generará PDF de solicitud de inscripción. |
| RF-05 | El alumno podrá descargar nuevamente su formato usando CURP. |
| RF-06 | Control escolar podrá consultar alumnos por CURP, nombre y folio. |
| RF-07 | Control escolar podrá descargar formato individual. |
| RF-08 | Control escolar podrá exportar la base completa en CSV. |
| RF-09 | El sistema permitirá importar CSV y actualizar por CURP + ciclo de ingreso. |
| RF-10 | El sistema permitirá cargar resultados del examen. |
| RF-11 | El sistema permitirá capturar o importar grupo asignado. |
| RF-12 | El sistema calculará resultados por área del examen. |
| RF-13 | El sistema permitirá comparar primera y segunda evaluación. |
| RF-14 | El sistema mostrará dashboard de rendimiento académico. |
| RF-15 | El sistema permitirá bloquear edición sin bloquear consulta. |
| RF-16 | El sistema procesará fotografías de hojas de respuesta para extraer respuestas. |
| RF-17 | El sistema marcará respuestas dudosas para revisión manual. |
| RF-18 | El sistema generará archivo enriquecido para carga masiva. |
| RF-19 | El alumno podrá consultar el avance de su proceso con CURP. |
| RF-20 | El alumno podrá ver estado de documentación. |
| RF-21 | El alumno podrá consultar resultados de evaluación diagnóstica. |
| RF-22 | El alumno podrá consultar áreas de mejora. |
| RF-23 | El alumno podrá consultar materiales recomendados. |
| RF-24 | El sistema dejará espacio para integración futura con plataforma de regularización autodirigida. |
| RF-25 | El alumno podrá consultar grupo propedéutico. |
| RF-26 | El alumno podrá consultar resultados de evaluación posterior. |
| RF-27 | El alumno podrá comparar evaluación inicial contra evaluación posterior. |
| RF-28 | El alumno podrá consultar grupo escolar definitivo. |
| RF-29 | El alumno podrá consultar matrícula escolar. |
| RF-30 | El alumno podrá consultar horario de clases. |
| RF-31 | El alumno podrá consultar instrucciones de activación SICOBaEM. |
| RF-32 | El alumno podrá ver avisos y notificaciones. |
| RF-33 | El sistema permitirá recordar CURP en el dispositivo con autorización. |
| RF-34 | El sistema permitirá activar/desactivar módulos por etapa. |
| RF-35 | El sistema permitirá administrar catálogos. |
| RF-36 | El sistema usará desplegables en campos catalogables. |
| RF-37 | El sistema generará folio interno por ciclo y plantel. |
| RF-38 | El sistema filtrará alumnos por ciclo, generación y periodo. |
| RF-39 | El sistema conservará histórico de ciclos anteriores. |
| RF-40 | El sistema será reutilizable para siguientes ciclos escolares. |

---

## 30. Requerimientos no funcionales consolidados

| ID | Requerimiento |
|---|---|
| RNF-01 | El sistema debe ser usable desde celular. |
| RNF-02 | El formulario debe ser simple y guiado. |
| RNF-03 | El panel administrativo debe requerir usuario y contraseña. |
| RNF-04 | El sistema debe proteger datos personales. |
| RNF-05 | El sistema debe registrar cambios relevantes. |
| RNF-06 | El sistema debe permitir exportar información. |
| RNF-07 | El PDF debe conservar diseño institucional similar al formato físico. |
| RNF-08 | La lectura de hojas debe permitir revisión manual. |
| RNF-09 | El sistema debe soportar al menos 300 registros por generación. |
| RNF-10 | El sistema debe permitir reutilizarse en futuros ciclos escolares. |
| RNF-11 | El portal debe ser mobile-first. |
| RNF-12 | El acceso debe ser simple para alumnos con baja experiencia digital. |
| RNF-13 | El sistema debe evitar exponer información sensible solo con CURP cuando sea posible. |
| RNF-14 | El sistema debe tener mensajes claros de “aún no disponible”. |
| RNF-15 | El sistema debe permitir activar/desactivar módulos por etapa. |
| RNF-16 | El sistema debe recordar CURP solo con consentimiento. |
| RNF-17 | El alumno debe poder borrar la CURP guardada. |
| RNF-18 | El portal debe funcionar correctamente en navegadores móviles comunes. |
| RNF-19 | La consulta debe permanecer disponible después del cierre de edición. |
| RNF-20 | Los avisos deben ser administrables sin modificar código. |
| RNF-21 | Los catálogos deberán poder actualizarse sin intervención técnica. |
| RNF-22 | Los catálogos deberán permitir valores activos e inactivos. |
| RNF-23 | El sistema deberá conservar consistencia histórica aunque un catálogo cambie. |
| RNF-24 | El portal deberá soportar múltiples ciclos de ingreso. |
| RNF-25 | Los reportes deberán poder filtrarse por ciclo, generación y periodo. |
| RNF-26 | El sistema deberá evitar duplicidad de folios dentro del mismo ciclo. |
| RNF-27 | El sistema debe tener diseño claro, institucional y ligero. |
| RNF-28 | El sistema debe permitir respaldos. |
| RNF-29 | Las importaciones deben generar reporte de errores. |
| RNF-30 | El sistema debe ser mantenible por personal técnico limitado. |

---

## 31. Criterios de aceptación del MVP

El MVP se considerará aceptado si cumple:

1. Un alumno puede entrar con CURP, capturar sus datos y folio de examen.
2. El sistema genera folio interno único por ciclo y plantel.
3. El alumno puede descargar su formato PDF prellenado.
4. El alumno puede volver a entrar con CURP y descargar el formato.
5. Control escolar puede buscar al alumno por CURP, nombre y folio.
6. Control escolar puede exportar la base en CSV.
7. Control escolar puede importar CSV y actualizar registros.
8. El sistema maneja ciclo de ingreso.
9. El sistema permite administrar catálogos básicos.
10. El sistema permite bloquear edición y mantener consulta.
11. El alumno puede ver estado general de su proceso.
12. El alumno puede ver estado de documentación.
13. El administrador puede publicar avisos.
14. El sistema permite cargar resultados diagnósticos.
15. El alumno puede consultar resultado y áreas de mejora.
16. El dashboard muestra indicadores básicos.
17. El sistema permite cargar grupo propedéutico.
18. El sistema permite cargar matrícula, grupo escolar y horario.
19. El sistema protege el panel administrativo con autenticación.
20. El sistema registra auditoría mínima de cambios relevantes.

---

## 32. Priorización sugerida del MVP

### Prioridad Alta / Fase 1

1. Registro del alumno.
2. CURP y folio de examen.
3. Ciclo de ingreso.
4. Folio interno.
5. Generación PDF.
6. Descarga por CURP.
7. Panel de control escolar.
8. Exportación CSV.
9. Importación CSV.
10. Catálogos básicos.
11. Estado de documentación.
12. Bloqueo de edición.
13. Avisos.

### Prioridad Alta / Fase 2

1. Carga de resultados.
2. Dashboard académico básico.
3. Resultados por área.
4. Áreas de mejora.
5. Materiales recomendados.
6. Grupo propedéutico.
7. Segunda evaluación.
8. Comparativo de avance.

### Prioridad Media / Fase 3

1. Grupo escolar.
2. Matrícula.
3. Horario.
4. Instrucciones SICOBaEM.
5. Reportes avanzados.
6. Catálogos extendidos.

### Prioridad Técnica / Piloto separado

1. Módulo OMR.
2. Procesamiento de imágenes.
3. Plantillas de hoja de respuesta.
4. Revisión manual.
5. Validación de respuestas.
6. Exportación de respuestas.

---

## 33. Riesgos y mitigaciones

| Riesgo | Impacto | Mitigación |
|---|---|---|
| Captura incorrecta de CURP | Duplicidad o datos mal asociados | Validación de formato y revisión por control escolar. |
| Folio de examen mal capturado | No se puede cruzar con hoja de respuestas | Confirmación doble del folio y búsqueda de duplicados. |
| Alumnos sin acceso a internet/celular | No completan registro | Habilitar laboratorio o mesa de apoyo. |
| Datos sensibles expuestos | Riesgo de privacidad | Autenticación, permisos, no cachear datos sensibles. |
| Catálogos incompletos | Captura inconsistente | Permitir opción “Otro” y administración posterior. |
| OMR con baja precisión | Errores de evaluación | Revisión manual de baja confianza. |
| CSV mal formado | Importaciones incorrectas | Validación previa y reporte de errores. |
| Cambio de formato oficial | PDF queda obsoleto | Diseñar generador configurable o plantilla editable. |
| Uso en siguientes ciclos | Mezcla de datos históricos | Ciclo de ingreso obligatorio y filtros por generación. |
| Baja adopción del personal | Uso parcial | Capacitación y panel simple. |

---

## 34. Preguntas abiertas para diseño técnico

Claude debe identificar y resolver o marcar como pendiente:

1. ¿Cuál será el stack recomendado para construir rápido y mantener fácil?
2. ¿Conviene usar base de datos relacional tradicional?
3. ¿Conviene separar módulo OMR como microservicio?
4. ¿Cómo se debe almacenar la imagen original de hojas de respuesta?
5. ¿Qué librería o enfoque técnico usar para PDF?
6. ¿Qué librería o enfoque técnico usar para OMR?
7. ¿Qué campos deben ser obligatorios en el MVP?
8. ¿Qué datos deben requerir segundo factor además de CURP?
9. ¿Cómo implementar “recordar CURP” sin comprometer privacidad?
10. ¿Cómo configurar catálogos dependientes?
11. ¿Cómo permitir cambios de formato PDF en futuros ciclos?
12. ¿Qué estructura CSV estándar se debe definir para importación/exportación?
13. ¿Cómo diseñar auditoría sin complicar el MVP?
14. ¿Cómo modularizar el sistema para crecer hacia gestión de desempeño académico?
15. ¿Qué se debe dejar como configuración y qué como código?

---

## 35. Entregables esperados del diseño técnico

Claude debe producir:

1. Documento de arquitectura.
2. Modelo entidad-relación.
3. Diseño de base de datos.
4. Diseño de módulos.
5. Diseño de roles y permisos.
6. Diseño de flujos de alumno y admin.
7. Diseño de API/endpoints.
8. Diseño de frontend.
9. Diseño del generador PDF.
10. Diseño del módulo OMR.
11. Diseño de importación/exportación CSV.
12. Diseño de dashboards.
13. Diseño de catálogos.
14. Diseño de seguridad.
15. Plan de implementación por fases.
16. Backlog técnico.
17. Estimación de complejidad por módulo.
18. Criterios de aceptación.
19. Recomendaciones de despliegue.
20. Riesgos técnicos y mitigaciones.

---

## 36. Redacción formal del alcance

El Portal Académico de Nuevo Ingreso funcionará como una plataforma de autoservicio para que los alumnos den seguimiento a su proceso de incorporación al plantel. A través de su CURP, el alumno podrá registrar sus datos, descargar su formato de inscripción, consultar el estado de su documentación, revisar resultados de evaluación diagnóstica, identificar áreas de mejora, acceder a materiales recomendados, consultar su grupo de curso propedéutico, revisar su evaluación posterior, conocer su grupo escolar, matrícula, horario de clases, instrucciones de acceso a SICOBaEM y avisos institucionales.

El sistema permitirá bloquear la edición de datos después del cierre del registro, pero mantendrá disponible la consulta para el alumno durante todo el proceso. Además, el portal servirá como base para un futuro sistema de gestión de desempeño académico, orientado a que docentes y directivos tomen decisiones oportunas con base en datos.

Como criterio deseable de calidad de datos, el sistema deberá utilizar listas desplegables alimentadas por catálogos administrables para todos los campos donde sea posible. Estos catálogos permitirán estandarizar la captura, reducir errores, facilitar filtros y mejorar la calidad de los reportes.

Adicionalmente, cada registro de alumno deberá estar asociado a un identificador de ciclo o año de ingreso, de manera que el portal pueda reutilizarse en futuras generaciones sin mezclar información histórica. Para ello, el sistema deberá generar un folio interno por ciclo y plantel, además de conservar la CURP como identificador principal del alumno y el folio de examen como vínculo con la hoja de respuestas física.

---

## 37. Nombre recomendado del sistema

Nombre recomendado:

# Portal Académico de Nuevo Ingreso

Justificación:

- No limita el proyecto solo a control escolar.
- No limita el proyecto solo al examen.
- Permite crecer hacia desempeño académico.
- Es entendible para alumnos, docentes y directivos.
- Puede reutilizarse por ciclos escolares.

Alternativas:

1. Sistema de Diagnóstico y Registro de Nuevo Ingreso.
2. Portal de Registro y Evaluación Diagnóstica.
3. Sistema de Desempeño Académico Inicial.
4. COBAEM Diagnóstico Inicial.
5. Portal de Seguimiento de Nuevo Ingreso.

---

## 38. Cierre

Este sistema debe entenderse como la primera etapa de una plataforma institucional más amplia.

En el corto plazo resuelve:

- Registro digital.
- Formato de inscripción.
- Seguimiento de documentación.
- Cruce con folio de examen.
- Exportaciones.
- Resultados diagnósticos.
- Dashboard inicial.

En el mediano plazo habilita:

- Evaluación del curso propedéutico.
- Análisis por grupo.
- Intervenciones académicas.
- Seguimiento de desempeño.
- Planeación docente basada en datos.

En el largo plazo puede convertirse en:

- Sistema de gestión de desempeño académico.
- Sistema de alerta temprana.
- Plataforma de acompañamiento académico.
- Herramienta directiva para toma de decisiones.

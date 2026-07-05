# 03 — Módulos, Roles y Flujos de Usuario

---

## 1. Roles y permisos (spatie/laravel-permission)

Roles: `admin`, `control_escolar`, `coordinacion`, `direccion`, `docente`, `tecnico`.
El **alumno no es usuario del sistema** (sin registro en `users`): accede por CURP con sesión ligera propia.

### Permisos (matriz §26 del requerimiento)

| Permiso | control_escolar | coordinacion | direccion | tecnico | admin |
|---|:-:|:-:|:-:|:-:|:-:|
| alumnos.ver / alumnos.editar | ✔ / ✔ | ✔ / – | ✔ / – | – | ✔ |
| alumnos.bloquear_edicion | ✔ | – | – | – | ✔ |
| documentacion.validar | ✔ | – | – | – | ✔ |
| formatos.descargar | ✔ | – | – | – | ✔ |
| csv.exportar | ✔ | ✔ | ✔ | – | ✔ |
| csv.importar | ✔ | ✔ | – | ✔ | ✔ |
| omr.procesar | – | – | – | ✔ | ✔ |
| omr.corregir | – | ✔ | – | ✔ | ✔ |
| resultados.cargar | – | ✔ | – | ✔ | ✔ |
| dashboard.academico | – | ✔ | ✔ | – | ✔ |
| dashboard.registros | ✔ | ✔ | ✔ | – | ✔ |
| grupos.asignar | ✔ | ✔ | – | – | ✔ |
| avisos.publicar | ✔ | ✔ | ✔ | – | ✔ |
| catalogos.administrar | – | – | – | ✔ | ✔ |
| modulos.publicar | ✔ | – | – | – | ✔ |
| usuarios.administrar | – | – | – | – | ✔ |

`docente` (fase posterior): dashboard académico de sus grupos, solo lectura.

## 2. Acceso del alumno (dos niveles)

| Nivel | Requiere | Da acceso a |
|---|---|---|
| Básico | CURP válida existente | Mi proceso (tablero de estados), avisos generales, descarga de formato* |
| Sensible | CURP + fecha de nacimiento (o folio de examen) | Mis datos, resultados, áreas de mejora, comparativo, matrícula, documentación con observaciones |

\* La descarga del PDF contiene datos personales → se decide en diseño: **requiere nivel sensible** (recomendado, cumple RNF-13).

Sesión del alumno: sesión Laravel con `alumno_proceso_id`, expira a los 30 min de inactividad. "Recordar CURP" solo rellena el campo desde `localStorage` con consentimiento (checkbox off por defecto, advertencia, botón "olvidar CURP"); nunca mantiene sesión abierta.

Multiproceso: si la CURP tiene procesos en más de un ciclo, se muestra selector de ciclo.

## 3. Flujo del alumno — registro (Fase 1)

```
/ (landing) → [Ingresa CURP]
  ├─ CURP no existe y registro abierto → asistente de registro (Livewire multipaso):
  │    Paso 1: CURP + confirmación, fecha nacimiento, folio de examen (doble captura)
  │    Paso 2: Datos personales (nombres, sexo, nacionalidad, entidad/municipio nacimiento)
  │    Paso 3: Domicilio y contacto (municipio→localidad dependientes, celular obligatorio)
  │    Paso 4: Escuela de procedencia (entidad→municipio→secundaria, promedio)
  │    Paso 5: Tutor (obligatorio) y madre (opcional)
  │    Paso 6: Otros datos (opcionales) + aviso de privacidad (obligatorio)
  │    Confirmación → genera folio interno → pantalla de éxito + botón descargar PDF
  ├─ CURP existe → verificación segundo dato → "Mi proceso"
  └─ CURP no existe y registro cerrado → mensaje claro "registro no disponible"
```

Reglas: cada paso guarda borrador (estatus `registro_incompleto`); edición permitida mientras `edicion_bloqueada=false` y ventana del ciclo abierta; al editar se puede regenerar PDF.

## 4. Flujo del alumno — seguimiento

"Mi proceso" muestra el tablero §7.4 (etapas y estados). Cada sección del menú (§7.3) respeta `modulos_ciclo`: si el módulo no está publicado → mensaje "aún no disponible" (RNF-14). Secciones: Mis datos, Formato, Documentación, Evaluación, Áreas de mejora, Materiales, Regularización (placeholder), Propedéutico, Evaluación posterior, Mi avance, Grupo escolar, Matrícula, Horario, SICOBaEM, Avisos.

## 5. Flujos administrativos

### Control escolar
1. Login → dashboard de registros (indicadores §10.2).
2. Buscar alumno (CURP/nombre/folios/ciclo/estatus) → detalle → editar / descargar PDF / marcar documentos / observaciones / bloquear edición.
3. Exportar CSV (filtros) / Importar CSV → reporte de importación.
4. Publicar módulos del ciclo (checklist §27).
5. Cargar matrícula, grupo escolar y horarios (captura o CSV) — Fase 3.

### Coordinación académica
1. Cargar clave de respuestas del examen (CSV).
2. Importar/calcular resultados → revisar dashboard académico.
3. Asignar grupos propedéuticos (individual o CSV).
4. Cargar segunda evaluación → comparativo.

### Técnico
1. Administrar catálogos (CRUD con dependencias, orden, activo/inactivo, import/export CSV).
2. Configurar plantilla OMR del examen.
3. Subir lote de hojas → monitorear procesamiento → cola de revisión (hojas `requiere_revision`): pantalla imagen + respuestas detectadas lado a lado → corregir → validar.
4. Exportar archivo enriquecido (respuestas + datos del alumno) para plataforma federal.

### Administrador
Usuarios y roles, ciclos de ingreso (crear ciclo 2027 = reutilización), avisos, configuración general, SICOBaEM por ciclo.

## 6. Estados del proceso de ingreso

```
registro_incompleto → registrado → validado
                          │
                          └→ requiere_correccion → registrado
```
Estados por etapa (tablero alumno) se derivan de datos: formato (generado/descargado), documentación (agregado de documentos_alumno), evaluación (hoja/resultado presente), grupos/matrícula/horario (asignación + módulo publicado).

## 7. Dashboards

### Dashboard de registros (control escolar / dirección)
Total registrados, completos vs incompletos, sin folio de examen, folios duplicados, CURP duplicadas (cross-check), formatos generados/descargados, documentación pendiente/validada/rechazada, con grupo propedéutico, con grupo escolar, con matrícula. Filtro global por ciclo.

### Dashboard académico (coordinación / dirección / propedéutico)
Indicadores §24.2 con filtros por ciclo, examen, área, secundaria de procedencia, grupo. Vistas por rol (§24.3). Gráficas: barras por área, distribución de riesgo (dona), dispersión promedio secundaria vs resultado, top preguntas falladas, comparativo evaluación 1 vs 2 por grupo.

Implementación: consultas agregadas con caché de 10 min (driver database/file); sin herramientas BI externas.
## 8. Convenciones UX del portal

- Formularios: usar componentes Blade reutilizables (`x-campo`, `x-obligatorio`, `x-leyenda-obligatorios`) para label, ayuda, error inline y marcado visual de campos obligatorios. En el wizard de registro, la obligatoriedad visible se deriva de `RegistroAlumnoRules`.
- Campos y accesibilidad: cada control debe tener `id` y label asociado con `for`; los controles tactiles del alumno deben medir al menos 44 px de alto. Usar `type`, `inputmode` y `autocomplete` adecuados para movil.
- Validacion: los mensajes deben estar en espanol, con nombres humanos de atributos en `lang/es/validation.php`, sin revelar si una CURP existe o no.
- Errores HTTP: las vistas `403`, `404`, `419`, `429`, `500` y `503` viven en `resources/views/errors/`, usan lenguaje simple y nunca muestran detalles tecnicos al usuario final.
- Retroalimentacion: los banners de exito usan `x-flash`, con cierre manual y autocierre. Las acciones destructivas o de alto impacto en admin deben pedir confirmacion con una frase que explique la consecuencia.
- Seguimiento del alumno: "Mi proceso" debe mostrar cada etapa con estado visual, texto de siguiente paso y acceso a las secciones publicadas por ciclo.

# 04 — Rutas y Endpoints

Arquitectura server-rendered (Blade + Livewire): la mayoría de la interacción va por componentes Livewire, no por API REST. Se listan las rutas web y la API mínima (OMR).

---

## 1. Rutas públicas — alumno (`routes/web.php`)

| Método | Ruta | Propósito |
|---|---|---|
| GET | `/` | Landing + formulario CURP |
| POST | `/acceso` | Localiza CURP; decide registro / verificación / selector de ciclo |
| GET/POST | `/verificacion` | Segundo dato (fecha nacimiento o folio examen) |
| GET | `/registro` | Asistente de registro (Livewire multipaso) |
| GET | `/mi-proceso` | Tablero de avance (middleware `alumno.sesion`) |
| GET | `/mi-proceso/{seccion}` | Secciones: datos, documentacion, evaluacion, areas-mejora, materiales, propedeutico, evaluacion-posterior, avance, grupo-escolar, matricula, horario, sicobaem, avisos (middleware `alumno.sesion` + `modulo.publicado:{seccion}`) |
| GET | `/mi-proceso/formato/descargar` | PDF de inscripción (registra descarga) |
| POST | `/salir` | Cierra sesión del alumno |
| GET | `/aviso-de-privacidad` | Texto público |

Middleware `alumno.sesion`: exige `alumno_proceso_id` en sesión; nivel `sensible` para secciones con datos personales/resultados.
Middleware `modulo.publicado`: consulta `modulos_ciclo`; si oculto → vista "aún no disponible".

## 2. Rutas administrativas (`/admin`, middleware `auth` + permisos)

| Ruta | Permiso | Contenido |
|---|---|---|
| `/admin` | dashboard.registros | Dashboard de registros |
| `/admin/alumnos` | alumnos.ver | Búsqueda/listado con filtros |
| `/admin/alumnos/{proceso}` | alumnos.ver | Detalle, edición, documentación, bloqueo |
| `/admin/alumnos/{proceso}/formato` | formatos.descargar | Descarga PDF individual |
| `/admin/exportaciones` | csv.exportar | Exportaciones CSV (tipos y filtros) |
| `/admin/importaciones` | csv.importar | Subida CSV, historial y reportes |
| `/admin/catalogos` | catalogos.administrar | CRUD catálogos + dependencias |
| `/admin/ciclos` | usuarios.administrar (admin) | Ciclos de ingreso, ventanas de registro |
| `/admin/modulos` | modulos.publicar | Publicación de módulos por ciclo |
| `/admin/avisos` | avisos.publicar | CRUD avisos |
| `/admin/examenes` | resultados.cargar | Exámenes, claves de respuesta |
| `/admin/resultados` | resultados.cargar | Carga/cálculo, recálculo |
| `/admin/dashboard-academico` | dashboard.academico | Indicadores académicos |
| `/admin/grupos-propedeuticos` | grupos.asignar | CRUD + asignación |
| `/admin/grupos-escolares` | grupos.asignar | CRUD + asignación + horarios |
| `/admin/matriculas` | grupos.asignar | Carga de matrículas |
| `/admin/sicobaem` | modulos.publicar | Config instrucciones por ciclo |
| `/admin/omr` | omr.procesar | Subida de hojas, monitoreo |
| `/admin/omr/revision` | omr.corregir | Cola de revisión manual |
| `/admin/omr/plantillas` | catalogos.administrar | Plantillas de zonas |
| `/admin/usuarios` | usuarios.administrar | Usuarios y roles |
| `/admin/auditoria` | usuarios.administrar | Bitácora (activity log) |
| `/admin/archivos/hojas/{hoja}` | omr.procesar | Sirve imagen privada (SEG-09) |

## 3. API interna (`routes/api.php`, token Sanctum)

Solo para integración con el servicio OMR (contrato completo en `05-omr-servicio.md`):

| Método | Ruta | Uso |
|---|---|---|
| POST | `/api/omr/callback` | El servicio OMR entrega resultados de una hoja (modo asíncrono) |
| GET | `/api/omr/plantillas/{id}` | El servicio OMR descarga definición de plantilla (opcional; normalmente se sube al servicio) |

Sin API pública para alumnos (ADR-10).

## 4. Convenciones

- Nombres de ruta: `alumno.*`, `admin.*` (ej. `admin.alumnos.show`).
- Todas las descargas de archivos privados pasan por controladores con autorización (Policy) — nunca enlaces directos a `storage`.
- Livewire maneja formularios: validación server-side con FormRequests/rules reutilizables (`CurpRule`, `FolioExamenRule`).
- Rate limiting: `/acceso` y `/verificacion` limitados (ej. 10/min por IP) contra enumeración de CURPs (SEG-03).

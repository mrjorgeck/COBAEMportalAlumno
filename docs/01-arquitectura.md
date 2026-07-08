# 01 — Arquitectura del Sistema
**Portal Académico de Nuevo Ingreso — COBAEM Plantel Ario de Rosales**
Versión 1.0 · Basado en `requerimientos_portal_academico_nuevo_ingreso_cobaem.md`

---

## 1. Visión general

Monolito modular en Laravel, con un microservicio OMR externo. Dos frentes de usuario:

- **Portal del alumno** (público, acceso por CURP, mobile-first, sin cuenta/contraseña).
- **Panel administrativo** (autenticado, roles y permisos).

```
┌─────────────────────────────────────────────────────────┐
│  Hostinger compartido — registrocobaemario.ariocentro.com│
│  ┌────────────────────────────────────────────────────┐  │
│  │  Laravel (monolito modular)                        │  │
│  │  Blade + Livewire 3 + Tailwind                     │  │
│  │  ┌──────────┐ ┌───────────┐ ┌────────┐ ┌────────┐  │  │
│  │  │ Alumno   │ │ Admin     │ │ CSV    │ │ PDF    │  │  │
│  │  │ (portal) │ │ (panel)   │ │ imp/exp│ │ dompdf │  │  │
│  │  └──────────┘ └───────────┘ └────────┘ └────────┘  │  │
│  │  Jobs en cola (driver database, cron cada minuto)  │  │
│  └───────────────────┬────────────────────────────────┘  │
│         MariaDB: u132762550_COBAEM                       │
│         Storage privado: hojas de respuesta, PDFs        │
└──────────────────────┼───────────────────────────────────┘
                       │ HTTPS + token (Sanctum/API key)
              ┌────────▼─────────┐
              │ Servicio OMR     │  (externo: VPS o PC del plantel)
              │ FastAPI + OpenCV │  procesa imágenes → respuestas + confianza
              └──────────────────┘
```

## 2. Stack tecnológico

| Capa | Tecnología | Justificación |
|---|---|---|
| Backend | Laravel 12.x (real, PHP 8.3) | Convenciones claras, ecosistema maduro, mantenible por personal limitado |
| Lenguaje | PHP 8.3 | Disponible en Hostinger; requerido por el proyecto |
| BD | MariaDB (utf8mb4) | Disponible en Hostinger; prod: `u132762550_COBAEM` |
| Frontend | Blade + Livewire 3 + Alpine.js + Tailwind CSS | Sin SPA ni API pública para el alumno; formularios reactivos (catálogos dependientes) sin build complejo; mobile-first |
| Auth admin | Login propio mínimo (Auth::attempt) + spatie/laravel-permission | Menos dependencias que Breeze; roles/permisos del §26 de requerimientos |
| PDF | barryvdh/laravel-dompdf | Plantillas Blade versionables por ciclo (RNF-07, riesgo "cambio de formato oficial") |
| CSV | league/csv | Import/export robusto con streaming |
| Auditoría | spatie/laravel-activitylog | Cubre §25.3 sin desarrollo propio |
| Colas | driver `database` + cron (`schedule:run` cada minuto) | Hostinger compartido no permite workers persistentes |
| Caché/sesión | driver `database` / `file` | Sin Redis en compartido |
| OMR | Servicio externo FastAPI (Python 3.12) + OpenCV | OpenCV no corre en hosting compartido; contrato en `05-omr-servicio.md` |
| Gráficas | Chart.js (CDN o npm) | Dashboards ligeros |
| Assets | Vite (build local, se sube `public/build`) | No se compila en el servidor |

## 3. Decisiones de arquitectura (ADR resumidas)

| # | Decisión | Alternativa descartada | Razón |
|---|---|---|---|
| ADR-01 | Monolito modular | Microservicios generales | 300 alumnos/ciclo, equipo pequeño, hosting compartido |
| ADR-02 | Alumno SIN cuenta: acceso por CURP + segundo dato (fecha de nacimiento o folio de examen) para secciones sensibles | Login con contraseña | RNF-12: simplicidad para alumnos; SEG-02 |
| ADR-03 | OMR como servicio externo con contrato REST | OMR embebido en PHP | Decisión del producto; OpenCV/Python fuera del hosting compartido. Fallback: importación CSV de respuestas |
| ADR-04 | Catálogos híbridos: tabla genérica `catalogos` (listas simples, con `parent_id` para dependientes) + tablas propias para entidades con comportamiento (ciclos, planteles, exámenes, grupos) | Todo genérico o todo específico | CAT-01..08 sin explosión de tablas ni pérdida de integridad |
| ADR-05 | `alumnos` (identidad) separado de `procesos_ingreso` (ciclo) | Tabla única | RF-39/40: histórico multiciclo, CURP única + un proceso por ciclo |
| ADR-06 | Publicación de módulos por ciclo en tabla `modulos_ciclo` | Config en código | §27: activar/desactivar etapas sin desarrollador |
| ADR-07 | PDF con plantillas Blade versionadas por ciclo (`plantilla_version` en proceso) | Plantilla única | Riesgo: cambio del formato oficial entre ciclos |
| ADR-08 | "Recordar CURP" en `localStorage` del navegador, con consentimiento explícito; nunca en cookie de sesión del servidor | Cookie persistente | SEG-10/11, RNF-16/17: sin estado sensible en servidor ni caché |
| ADR-09 | Todo archivo sensible (imágenes de hojas, PDFs) en `storage/app/private`, servido vía controlador con autorización | `public/` | SEG-09 |
| ADR-10 | Rutas web con Livewire; API REST mínima solo para el servicio OMR (Sanctum token) | API completa | Menos superficie de ataque y mantenimiento |

## 4. Estructura de carpetas (repositorio)

```
PortalNuevosIngresos/
├── CLAUDE.md                      # Contexto para asistentes de IA
├── README.md
├── docs/                          # Documentación de diseño (estos archivos)
├── requerimientos_portal_academico_nuevo_ingreso_cobaem.md
├── portal/                        # Aplicación Laravel
│   ├── app/
│   │   ├── Enums/                 # EstadoDocumento, EstadoProcesamiento, NivelRiesgo...
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Alumno/        # Acceso CURP, descargas PDF
│   │   │   │   ├── Admin/         # Descargas, archivos privados
│   │   │   │   └── Api/           # OmrCallbackController
│   │   │   └── Middleware/        # VerificarSesionAlumno, ModuloPublicado
│   │   ├── Livewire/
│   │   │   ├── Alumno/            # Registro, MiProceso, Resultados, Avisos...
│   │   │   └── Admin/             # Dashboard, Alumnos, Documentacion, Catalogos,
│   │   │                          #   Importaciones, Resultados, Grupos, Avisos, Omr
│   │   ├── Models/
│   │   ├── Services/
│   │   │   ├── FolioService.php           # NI-{AÑO}-{PLANTEL}-{CONSECUTIVO}
│   │   │   ├── CurpValidator.php
│   │   │   ├── CalculoResultadosService.php
│   │   │   ├── Pdf/FormatoInscripcionPdf.php
│   │   │   ├── Csv/                       # Importers + Exporters por tipo
│   │   │   └── Omr/OmrClient.php          # Cliente HTTP del servicio OMR
│   │   ├── Jobs/                  # ProcesarImportacionCsv, EnviarHojaAOmr, CalcularResultados
│   │   └── Policies/
│   ├── config/portal.php          # Reglas de negocio configurables (rangos riesgo, formatos folio)
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/               # Catálogos iniciales (entidades, municipios, sexo...)
│   ├── resources/views/
│   │   ├── alumno/                # Layout mobile-first
│   │   ├── admin/
│   │   └── pdf/
│   │       └── inscripcion/v2026/ # Plantilla PDF versionada por ciclo
│   ├── routes/
│   │   ├── web.php                # alumno + admin
│   │   └── api.php                # callback OMR
│   └── tests/
│       ├── Feature/
│       └── Unit/
├── omr-service/                   # Microservicio OMR (repo/carpeta aparte)
│   ├── app/                       # FastAPI: detección, perspectiva, marcas, confianza
│   ├── templates/                 # Plantillas de zonas de respuesta (JSON)
│   └── tests/
└── deploy/
    ├── deploy.sh                  # Script de despliegue por SSH
    └── hostinger-notes.md
```

## 5. Módulos lógicos del portal

| Módulo | Contenido | Fase |
|---|---|---|
| Núcleo | Alumnos, procesos de ingreso, folios, ciclos, catálogos | 1 |
| Registro | Formulario guiado multipaso, validación CURP, bloqueo de edición | 1 |
| PDF | Generación/regeneración del formato de inscripción, registro de descargas | 1 |
| Panel admin | Búsqueda, detalle, edición, dashboard de registros | 1 |
| Documentación | Estados de documentos, observaciones | 1 |
| CSV | Exportaciones e importaciones con reporte | 1 |
| Avisos | CRUD, segmentación, lectura | 1 |
| Evaluación | Claves de respuesta, carga/cálculo de resultados, áreas de mejora | 2 |
| Dashboard académico | Indicadores §24, vistas por rol | 2 |
| Materiales | Recomendaciones por área/nivel | 2 |
| Propedéutico | Grupos, asignación, segunda evaluación, comparativo | 2 |
| Escolar | Grupo escolar, matrícula, horario, SICOBaEM | 3 |
| OMR | Subida de hojas, integración con servicio, revisión manual, exportación | 4 (piloto) |

## 6. Flujo de datos clave

1. **Registro**: alumno → Livewire multipaso → validación → `alumnos` + `procesos_ingreso` (+ contacto, familiares, otros) → `FolioService` genera folio interno → PDF disponible.
2. **OMR**: técnico sube foto → `hojas_respuesta` (pendiente) → job `EnviarHojaAOmr` → servicio OMR responde (síncrono o callback) → `respuestas` con confianza → cola de revisión manual → validada → cruce por `folio_examen` + ciclo.
3. **Resultados**: respuestas validadas + `claves_respuesta` → `CalcularResultadosService` → `resultados` + `resultados_area` → niveles de riesgo (rangos en catálogo/config) → visible al alumno cuando el módulo se publique.
4. **Importación CSV**: archivo → validación de estructura → job → upsert por CURP+ciclo (reglas §23.3) → `importaciones_csv` con reporte.

## 7. Notas de verificación (revisor de calidad)

- **Versión de Laravel**: se usa Laravel 12.x real con PHP 8.3 en Hostinger compartido. La arquitectura no depende de Laravel 13.
- **HTTPS**: el subdominio se referenció como `http://`; debe operar exclusivamente en `https://registrocobaemario.ariocentro.com` (SSL gratuito de Hostinger + redirección forzada). El certificado SSL del sitio y la llave SSH de despliegue son cosas distintas; ambas se configuran (ver `08-despliegue-hostinger.md`).
- **Credenciales**: solo en `.env` del servidor (BD `u132762550_COBAEM`, usuario/contraseña). Nunca en el repositorio.
- **Límite de recursos**: importaciones y OMR siempre por jobs encolados con lotes pequeños para respetar límites de CPU/memoria del plan compartido.

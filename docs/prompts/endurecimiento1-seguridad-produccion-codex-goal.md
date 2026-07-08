# Prompt de construcción — Endurecimiento 1: Seguridad y salida a producción (Codex, modo Goal)

**Cómo usarlo**: abre Codex (≥ 0.128) en la raíz del repositorio, escribe
`/goal` y pega el bloque de abajo.

**Prerrequisito**: MVP (Fases 0–3 + UX) verificado, CI en verde y árbol Git
limpio. Topología: el repo vive en la RAÍZ; la app Laravel está en `portal/`.

**Origen**: Auditoría técnica integral (bloque A del roadmap). Cierra los
hallazgos obligatorios antes de producción: H-001, H-002, H-003, H-004, H-006,
H-012 y la discrepancia documental de versión.

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Endurecer el Portal Académico de Nuevo Ingreso para salida a producción
segura, cerrando en orden los 7 hitos H1–H7 de abajo (hallazgos H-001 a
H-012 de la auditoría). La app Laravel vive en portal/ (Git en la raíz).
El MVP (Fases 0–3 + UX) está verificado: NO cambies su comportamiento
funcional; los tests existentes deben seguir pasando sin editarlos salvo que
un hito lo exija explícitamente.

CONDICIÓN DE TÉRMINO (todo verdadero)

1. Existe aviso de privacidad real (integral en su ruta; simplificado en el
   registro con enlace y casilla de consentimiento). Sin placeholders.
2. Cookie de sesión Secure/HttpOnly/SameSite y HTTPS+HSTS forzados en
   producción (sin romper el entorno local http).
3. Las 3 exportaciones CSV neutralizan inyección de fórmulas.
4. La contraseña inicial del admin exige rotación en el primer acceso.
5. Existe respaldo de BD real invocable (comando artisan) usado por el
   deploy y programado a diario; documentado en docs/08.
6. Docs y CLAUDE.md/AGENTS.md declaran Laravel 12 (versión real) y MariaDB.
7. cd portal && php artisan test pasa completo (con tests nuevos de: escape
   CSV, redirección a https en prod, presencia del aviso, rotación de
   contraseña). ./vendor/bin/pint --test limpio. Un commit por hito en
   español ("Endurecimiento 1.N: ..."). Push a origin main y CI en verde.

FUENTES DE VERDAD (en este orden ante conflicto)

1. requerimientos_portal_academico_nuevo_ingreso_cobaem.md (SEG-*, RF-33)
2. docs/07-seguridad-auditoria.md y docs/08-despliegue-hostinger.md
3. docs/aviso-privacidad.md (TEXTO OFICIAL ya aprobado: úsalo literal;
   respeta su encabezado de instrucciones de construcción)
4. CLAUDE.md / AGENTS.md (convenciones y reglas críticas)

PLAN DE HITOS

H1  Aviso de privacidad (H-001). Sustituye el placeholder de
    resources/views/alumno/aviso-privacidad.blade.php por la "Versión
    integral" de docs/aviso-privacidad.md. Coloca la "Versión simplificada"
    en el registro (wizard Livewire y fallback), junto a la casilla
    acepto_privacidad, con enlace a la ruta alumno.privacidad. La fecha de
    actualización vive en config/portal.php (no hardcodear). Las finalidades
    secundarias (imagen/voz) son consentimiento SEPARADO y opcional: no
    condicionan el registro; si no hay campo, no lo inventes, documenta.
H2  Sesión y transporte (H-002). Publica/ajusta config de sesión para que
    SESSION_SECURE_COOKIE y SESSION_SAME_SITE se controlen por env; añade en
    .env.example los valores recomendados para prod (comentados). Fuerza
    https y HSTS SOLO cuando app()->isProduction() (nunca en local). No
    rompas rutas ni assets.
H3  APP_DEBUG (H-003). Añade verificación al flujo de deploy y al checklist
    de docs/08 que falle si en prod APP_DEBUG!=false o APP_ENV!=production
    (p. ej. paso que corra php artisan about y lo valide). .env.example
    conserva APP_DEBUG=true solo para local.
H4  Inyección de fórmulas CSV (H-004). En CsvController (exportarAlumnos,
    exportarDocumentacion, exportarResultados) neutraliza celdas que inicien
    con = + - @ (usa League\Csv EscapeFormula o prefijo seguro). Mantén
    UTF-8 y compatibilidad Excel. Aplica a toda exportación con datos de
    texto libre.
H5  Rotación de admin (H-006). Agrega columna/flag "debe_cambiar_password"
    (migración) y middleware que obligue el cambio en el primer login del
    admin sembrado, sin bloquear el resto. AdminUserSeeder marca el flag.
    Documenta en docs/08.
H6  Respaldo real (H-012). Crea el comando artisan que hoy invoca deploy.sh
    (db:backup-predeploy) haciendo mysqldump a ~/backups con rotación, o
    ajusta deploy.sh + schedule (routes/console.php) para respaldo diario.
    No dejes referencias a comandos inexistentes. Documenta en docs/08.
H7  Discrepancia de versión (H-007 doc). Corrige toda mención "Laravel 13"
    a "Laravel 12.x (real)" y aclara MariaDB en CLAUDE.md, AGENTS.md y docs
    afectados. Actualiza "Estado actual".

RESTRICCIONES DURAS (romperlas = goal fallido)

- Solo Blade + Livewire 3 + Tailwind. Nada de SPA, Redis, workers
  persistentes ni binarios. Producción = Hostinger compartido.
- Jamás credenciales, llaves ni contraseñas en código/commits: todo por env.
- No degradar seguridad existente (throttling, permisos, no-store, filtrado
  por sesión del alumno). No exponer datos personales en logs.
- Esquema conforme a docs/02; toda migración nueva se documenta en el mismo
  commit. No editar migraciones ya commiteadas.
- Textos y commits en español; datos de prueba sintéticos.

AUTOVERIFICACIÓN (tras CADA hito)

cd portal && php artisan test && php artisan migrate:fresh --seed \
  && ./vendor/bin/pint
Corregir antes de avanzar. Commit por hito.

ESCALAR EN VEZ DE INVENTAR

- Fecha oficial de publicación del aviso: usa placeholder configurable y
  documenta que la fija el plantel.
- Si HSTS/preload puede afectar otros subdominios de ariocentro.com: aplica
  HSTS sin preload y pausa para confirmar.
- Cualquier conflicto requerimientos vs docs no resoluble por precedencia.
```

---

## Después del goal (rol coordinador/auditor)

1. Revisar diff commit por commit. Foco: que HTTPS/HSTS solo actúe en prod
   (no romper `php artisan serve`), que el escape CSV no corrompa datos
   legítimos, y que el aviso integral coincida literal con
   `docs/aviso-privacidad.md`.
2. Verificación manual: abrir `/aviso-de-privacidad`; registrar un alumno y
   confirmar consentimiento + enlace; exportar un alumno con nombre `=1+1` y
   abrir en Excel (celda literal); primer login de admin fuerza cambio.
3. Confirmar en Hostinger: cron de `schedule:run`, respaldo diario real y
   `.env` de prod con `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`.
4. Con E1 en verde, el checklist de salida a producción queda sin
   bloqueadores de seguridad. Continuar con **Endurecimiento 2** (robustez
   CSV, catálogos y pruebas) antes del piloto.

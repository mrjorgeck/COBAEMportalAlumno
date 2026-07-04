# Prompt — Reestructurar repo a la raíz y versionar todo (Codex, modo Goal)

**Cómo usarlo**: abre Codex en `C:\Projects\COBAEM\PortalNuevosIngresos`,
escribe `/goal` y pega el bloque de abajo.

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Reestructurar el control de versiones del proyecto para que el repositorio
Git viva en la RAÍZ del proyecto (no en portal/), conservando intacta la
historia existente (commits Fase 1.x y Fase 2.x), e incorporar TODOS los
archivos y cambios pendientes en commits lógicos.

CONDICIÓN DE TÉRMINO

1. git rev-parse --show-toplevel ejecutado en la raíz devuelve la raíz del
   proyecto; NO existe un segundo repo anidado en portal/ (sin gitlinks ni
   submódulos accidentales).
2. git log conserva la historia previa completa (verificar que aparecen los
   commits "Fase 1.1"…"Fase 1.15" y "Fase 2.1"…"Fase 2.9").
3. git status limpio: docs/, deploy/, CLAUDE.md, AGENTS.md, README.md,
   requerimientos_portal_academico_nuevo_ingreso_cobaem.md y todo portal/
   quedan versionados o correctamente ignorados.
4. Ningún archivo sensible commiteado: .env*, vendor/, node_modules/,
   storage/* (salvo .gitignore), llaves o credenciales. Verificar con
   git ls-files antes del commit final.
5. El workflow de CI queda en .github/workflows/ci.yml DE LA RAÍZ y sus
   pasos funcionan con la app en portal/ (working-directory o cd portal).
6. deploy/deploy.sh y docs/08-despliegue-hostinger.md actualizados a la
   nueva topología (el clon en el servidor contiene portal/ como subcarpeta;
   el symlink del subdominio apunta a .../portal/public).
7. Si hay remoto configurado: push exitoso SIN force-push. Si no hay remoto,
   dejarlo documentado y detenerte ahí.

PASOS SUGERIDOS (verifica el estado real antes de asumir nada)

P1  Diagnóstico: dónde está .git (raíz, portal/, o ambos), qué remoto hay
    (git remote -v), estado de working tree en cada repo. Reporta antes de
    mover nada. Haz una copia de seguridad del directorio .git existente
    (cp -r) antes de la reestructura.
P2  Primero deja el repo de portal/ limpio: commitea los cambios pendientes
    que existen dentro de portal/ ANTES de mover el repo:
    - app/Services/RegistroAlumnoService.php +
      tests/Feature/BloqueoEscrituraAlumnoTest.php + throttle en
      routes/web.php → commit "Fase 1.16: guardas de escritura del alumno
      (bloqueo y ventana de ciclo)".
    - portal/.github/workflows/ci.yml → se moverá a la raíz en P3; no lo
      commitees dentro de portal/.
P3  Mover el repo a la raíz: mv portal/.git ./.git ; verificar que git
    detecta el renombrado masivo hacia el prefijo portal/. Mover
    portal/.github/workflows/ci.yml a .github/workflows/ci.yml y ajustar
    sus pasos con working-directory: portal (o cd portal) en composer,
    artisan y pint; el actions/checkout no cambia.
P4  Crear .gitignore de raíz mínimo (los ignores de la app ya viven en
    portal/.gitignore): .DS_Store, Thumbs.db, *.log locales si aplica.
P5  Commits lógicos (en español, en este orden):
    1. "Reestructura: repositorio en la raíz del proyecto" (el rename masivo
       a portal/ + .gitignore raíz).
    2. "Docs: diseño técnico, requerimientos y prompts de construcción"
       (docs/, CLAUDE.md, AGENTS.md, README.md, requerimientos_*.md,
       deploy/ con checklist).
    3. "CI: workflow de verificación en PHP 8.3" (.github/workflows/ci.yml).
    4. "Deploy: rutas ajustadas a la nueva topología del repo"
       (deploy/deploy.sh + docs/08 actualizado).
P6  Verificación final: git log --oneline | head -40 (historia intacta),
    git status (limpio), git ls-files | grep -E "\.env|vendor/|node_modules"
    (vacío), y push si hay remoto.

RESTRICCIONES DURAS

- PROHIBIDO: git filter-repo, rebase, amend de commits ya existentes,
  force-push, o cualquier reescritura de historia.
- PROHIBIDO commitear .env, vendor/, node_modules/, contenido de storage/,
  binarios de PHP temporales o credenciales.
- No modificar código de la aplicación más allá de lo listado (P2 y rutas
  del workflow/deploy). Esta tarea es de infraestructura de repo.
- Si encuentras un repo YA inicializado en la raíz además del de portal/
  (repos anidados), detente y reporta el estado exacto antes de fusionar:
  la historia de portal/ es la que debe sobrevivir.
- Si algo sale mal a medio camino, restaura la copia de seguridad de .git
  y reporta.
```

---

## Después del goal

1. Verificar en el remoto (GitHub) que la historia se ve completa y que el
   workflow CI corrió en verde — esto cierra también la verificación
   ejecutable pendiente de la Fase 2.
2. Si CI falla en tests de Fase 2: reportar la salida para corregir antes
   de continuar con Fase 3.

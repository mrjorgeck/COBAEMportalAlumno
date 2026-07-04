# Continuación — resolución de repos anidados (pegar en la sesión de Codex)

```text
DECISIÓN (continúa el goal con esto)

El repo de la raíz NO tiene commits: su única información valiosa es el
remoto origin (https://github.com/mrjorgeck/COBAEMportalAlumno.git). La
historia que debe sobrevivir es la de portal/. Procede así:

1. Respaldo: copia ambos .git fuera del proyecto
   (cp -r .git /tmp/git-raiz-backup && cp -r portal/.git /tmp/git-portal-backup
   o equivalente en Windows).

2. En portal/: commitea lo pendiente ANTES de mover nada:
   - app/Services/RegistroAlumnoService.php +
     tests/Feature/BloqueoEscrituraAlumnoTest.php + throttle en routes/web.php
     → "Fase 1.16: guardas de escritura del alumno (bloqueo y ventana de ciclo)"
   - NO commitees portal/.github/ (se moverá a la raíz).

3. Verifica que el .git de la raíz no tiene commits en ninguna rama
   (git log --all --oneline vacío). Confirmado eso, elimina SOLO el
   directorio .git de la raíz (no toca archivos de trabajo).

4. Mueve el repo: mv portal/.git .git
   Reconecta el remoto: git remote add origin
   https://github.com/mrjorgeck/COBAEMportalAlumno.git
   (o git remote set-url si ya existiera).

5. Mueve portal/.github/workflows/ci.yml a .github/workflows/ci.yml y
   ajusta composer/artisan/pint con working-directory: portal.

6. Commits (verifica antes con git ls-files que no entra .env, vendor/,
   node_modules/ ni storage/):
   a. "Reestructura: repositorio en la raíz del proyecto"
      (rename masivo a portal/ + .gitignore de raíz)
   b. "Docs: diseño técnico, requerimientos y prompts de construcción"
      (docs/, CLAUDE.md, AGENTS.md, README.md, requerimientos_*.md, deploy/)
   c. "CI: workflow de verificación en PHP 8.3"
   d. "Deploy: rutas ajustadas a la nueva topología del repo"
      (deploy/deploy.sh y docs/08: el clon contiene portal/ como subcarpeta,
      symlink del subdominio → .../portal/public)

7. Push: git fetch origin primero. Si el remoto está vacío:
   git push -u origin main. Si el remoto YA tiene commits (README/licencia
   creados en GitHub): intégralos con
   git merge origin/main --allow-unrelated-histories (commit de merge, sin
   rebase ni force-push) y luego push.

8. Verificación final: git log --oneline muestra Fase 1.1…2.9 + los nuevos;
   git status limpio; en GitHub se ve la historia y el workflow CI corre.
   Reporta el resultado del CI: si los tests de Fase 2 pasan en verde, la
   verificación pendiente de Fase 2 queda cerrada.

Siguen vigentes las restricciones del goal original: sin reescritura de
historia, sin force-push, nada sensible commiteado.
```

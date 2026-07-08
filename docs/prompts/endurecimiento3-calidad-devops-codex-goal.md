# Prompt de construcción — Endurecimiento 3: Calidad, CI/CD e integridad de datos (Codex, modo Goal)

**Cómo usarlo**: abre Codex (≥ 0.128) en la raíz del repositorio, escribe
`/goal` y pega el bloque de abajo.

**Prerrequisito**: **Endurecimiento 2** mergeado y CI en verde; árbol Git
limpio. Topología: repo en la RAÍZ, app Laravel en `portal/`.

**Origen**: Auditoría técnica integral (bloques C y D del roadmap, segunda
etapa). Cierra H-009 (estilo/EOL), H-011 (CI), FKs faltantes en
procesos_ingreso, análisis estático y política de retención por ciclo.

---

## Prompt (pegar después de /goal)

```text
OBJETIVO

Elevar la calidad, la integridad de datos y la automatización del Portal
Académico de Nuevo Ingreso a nivel producto institucional, cerrando en orden
los hitos H1–H5 (hallazgos H-009, H-011, integridad referencial, análisis
estático y retención). La app Laravel vive en portal/ (Git en la raíz). NO
cambies comportamiento funcional ni lo endurecido en E1/E2; los tests
existentes deben seguir pasando.

CONDICIÓN DE TÉRMINO (todo verdadero)

1. ./vendor/bin/pint --test limpio en todo el repo y fin de línea normalizado
   (LF) vía .gitattributes; sin cambios de EOL pendientes.
2. Pipeline CI en GitHub Actions que, en cada PR y push a main, corre pint
   --test y php artisan test con MariaDB/SQLite y falla la build ante error.
3. Los FK grupo_propedeutico_id y grupo_escolar_id de procesos_ingreso tienen
   constraint real con nullOnDelete (migración nueva, sin romper datos ni
   seeders); documentado en docs/02.
4. Análisis estático (PHPStan/Larastan, nivel inicial acordado) corre en CI y
   pasa; los hallazgos reales se corrigen o se anotan con baseline.
5. Existe política de retención/anonimización por ciclo: comando artisan que
   permite anonimizar o purgar datos personales de ciclos cerrados conforme a
   la sección XI del aviso, con dry-run, confirmación y auditoría.
6. cd portal && php artisan test pasa completo con tests nuevos para el
   comando de retención (dry-run no altera; ejecución anonimiza el ciclo
   objetivo y respeta el aislamiento de otros ciclos). Un commit por hito
   ("Endurecimiento 3.N: ..."). Push a origin main y CI en verde.

FUENTES DE VERDAD (en este orden ante conflicto)

1. requerimientos_portal_academico_nuevo_ingreso_cobaem.md (RNF calidad,
   retención; RF-39/40 multiciclo)
2. docs/02-modelo-datos.md (FKs, ciclos), docs/07-seguridad-auditoria.md
   (retención/anonimización), docs/aviso-privacidad.md §XI
3. docs/08-despliegue-hostinger.md (CI/despliegue) y CLAUDE.md / AGENTS.md

PLAN DE HITOS

H1  Estilo y EOL (H-009). Ejecuta ./vendor/bin/pint en todo el repo y añade
    .gitattributes que fije LF para el código (evita el churn de CRLF en
    Windows). Un commit de normalización, sin cambios de lógica.
H2  CI (H-011). Crea .github/workflows con un job que instale PHP 8.3 y deps,
    corra ./vendor/bin/pint --test y php artisan test (usando la BD de test
    del proyecto). La build debe fallar ante estilo o test roto. Documenta el
    badge/uso en docs/08.
H3  Integridad referencial. Migración que convierte grupo_propedeutico_id y
    grupo_escolar_id de procesos_ingreso en foreignId con constrained +
    nullOnDelete (respetando datos existentes; los índices ya existen).
    Verifica que seeders y migrate:fresh --seed sigan corriendo. Actualiza
    docs/02.
H4  Análisis estático. Añade PHPStan/Larastan con un nivel inicial razonable
    (p. ej. 4–5) y baseline para deuda preexistente; intégralo en CI. Corrige
    los hallazgos triviales; el resto al baseline con nota.
H5  Retención/anonimización (aviso §XI, RNF). Comando artisan
    (p. ej. ciclos:anonimizar {ciclo} --dry-run) que, para un ciclo CERRADO,
    disocia/anonimiza datos personales (nombres, contacto, familiares, salud)
    conservando lo estadístico/agregado, con confirmación explícita, dry-run
    por defecto y registro en activitylog. NUNCA debe tocar otros ciclos.
    Documenta el procedimiento en docs/07.

RESTRICCIONES DURAS (romperlas = goal fallido)

- Solo Blade + Livewire 3 + Tailwind. CI compatible con el hosting
  compartido de destino (no exige binarios en el servidor; assets se compilan
  localmente). Sin Redis ni workers persistentes.
- El comando de retención es destructivo: dry-run por defecto, exige
  confirmación, audita, y jamás cruza datos entre ciclos (RF-39/40).
- Esquema conforme a docs/02; toda migración nueva se documenta en el mismo
  commit. No editar migraciones ya commiteadas.
- No degradar seguridad ni comportamiento de E1/E2. Textos y commits en
  español; datos de prueba sintéticos; sin credenciales en código.

AUTOVERIFICACIÓN (tras CADA hito)

cd portal && php artisan test && php artisan migrate:fresh --seed \
  && ./vendor/bin/pint --test
Corregir antes de avanzar. Commit por hito.

ESCALAR EN VEZ DE INVENTAR

- Nivel definitivo de PHPStan y qué considerar "ciclo cerrado": propón un
  valor, aplícalo y documenta; si implica cambio de esquema mayor, pausa.
- Plazos legales de retención: usa lo indicado en el aviso §XI; si falta un
  dato normativo concreto, deja el plazo configurable y documenta.
- Cualquier conflicto requerimientos vs docs no resoluble por precedencia.
```

---

## Después del goal (rol coordinador/auditor)

1. Revisar diff commit por commit. Foco: que la migración de FKs no falle con
   datos existentes y que el comando de retención respete el aislamiento
   multiciclo (probar con dos ciclos poblados).
2. Verificar que la CI efectivamente bloquea un PR con test o estilo roto
   (abrir un PR de prueba que falle a propósito).
3. Ejecutar `ciclos:anonimizar` en dry-run sobre un ciclo cerrado de prueba y
   revisar el reporte antes de una ejecución real.
4. Con E3 en verde, el proyecto alcanza nivel **producto institucional
   inicial** consolidado. Siguiente frente independiente: **Fase 4 piloto
   OMR** (docs/05, docs/09 §2), que decide hosting del servicio externo.

# 07 — Seguridad, Privacidad y Auditoría

Mapea los requerimientos SEG-01..SEG-12 y §25 a decisiones concretas.

---

## 1. Autenticación y sesiones

| Frente | Mecanismo |
|---|---|
| Panel admin | Laravel Breeze: email + contraseña (bcrypt), sesión con expiración 60 min, throttle de login (5 intentos/min), contraseñas ≥ 12 caracteres. 2FA (TOTP) recomendado para rol admin en fase 2. (SEG-01) |
| Alumno | Sin cuenta. Nivel básico: CURP. Nivel sensible: CURP + fecha de nacimiento o folio de examen (SEG-02). Sesión 30 min, regeneración de ID al elevar nivel |
| Servicio OMR | API key en header, HTTPS, rotable desde `.env` |

## 2. Protección contra acceso indebido (SEG-03)

- Rate limiting en `/acceso` y `/verificacion` (10/min por IP + contador por CURP) contra enumeración.
- Respuesta genérica cuando la CURP no existe ("verifica tus datos o acude a control escolar") — no confirma existencia.
- Policies en todas las consultas del alumno: siempre filtradas por `alumno_proceso_id` de la sesión; jamás por parámetro de URL.
- IDs autoincrementales nunca expuestos en rutas del alumno.

## 3. "Recordar CURP" (SEG-10/11, RNF-16/17)

- Checkbox **desactivado por defecto** con el texto de advertencia del requerimiento §7.2.
- Se guarda solo la CURP en `localStorage` (nunca fecha de nacimiento, folio, ni resultados).
- Botón visible "Borrar mi CURP de este dispositivo".
- Cabeceras `Cache-Control: no-store` en todas las vistas con datos personales; sin datos sensibles en sessionStorage/caché del navegador.

## 4. Datos y archivos

- HTTPS forzado (middleware + redirección en servidor); HSTS.
- Imágenes de hojas y cualquier archivo sensible en `storage/app/private`, servidos solo vía controlador con Policy (SEG-09).
- Cifrado at-rest de columnas de mayor sensibilidad no requerido en MVP (BD ya restringida por credenciales); evaluar `encrypted` cast para `no_seguro_medico` en fase 2.
- `.env` fuera del control de versiones; `APP_DEBUG=false` en producción; APP_KEY única.
- Minimización: no se piden datos que el formato físico no requiera; datos médicos opcionales.

## 5. Bloqueo de edición (SEG-06, RF-15)

Tres niveles, cualquiera bloquea: ventana del ciclo (`registro_abierto_hasta`), bandera por alumno (`edicion_bloqueada`), validación de control escolar (estatus `validado`). La consulta permanece siempre disponible (RNF-19).

## 6. Auditoría (SEG-04, §25.3)

spatie/laravel-activitylog con `causer` (user o "alumno:{proceso_id}") y propiedades old/new en modelos: ProcesoIngreso, Alumno, DocumentoAlumno, Resultado, Aviso, Catalogo, ModuloCiclo, User.

Eventos explícitos adicionales: formato generado/descargado (`descargas_formato`), CSV importado/exportado, hoja OMR procesada/corregida/validada, bloqueo/desbloqueo, publicación de módulos y avisos, login admin (evento `Login` de Laravel).

Vista `/admin/auditoria` con filtros (usuario, modelo, fecha). Retención: todo el ciclo + 1 año; depuración documentada.

## 7. Aviso de privacidad (SEG-07)

Página pública con versión integral tomada de `docs/aviso-privacidad.md` + versión simplificada junto al checkbox obligatorio en registro (guarda `acepto_privacidad_at`) + texto en el PDF. Las finalidades secundarias de imagen/voz son consentimiento separado y opcional; no condicionan el registro.

## 8. Respaldos (SEG-08, RNF-28)

- Comando `php artisan db:backup-predeploy`: dump MariaDB con `mysqldump` hacia `~/backups`, usado por `deploy/deploy.sh` y programado diario por scheduler. Retención por `DB_BACKUP_RETENTION` (14 por defecto).
- Copia externa: descarga semanal manual o subida a almacenamiento externo (Google Drive/S3) si se configura.
- Hostinger genera respaldos propios del plan — verificar frecuencia y no depender solo de ellos.
- Prueba de restauración documentada antes del arranque del ciclo.

## 9. Checklist de endurecimiento en despliegue

1. HTTPS activo y forzado; certificado SSL del subdominio emitido.
2. `APP_ENV=production`, `APP_DEBUG=false`.
3. Document root apuntando a `public/` (código y `.env` fuera del webroot).
4. Permisos: `storage/` y `bootstrap/cache` escribibles solo por el usuario del hosting.
5. Rate limiting verificado; headers de seguridad (X-Frame-Options, nosniff, referrer-policy).
6. Usuario de BD con privilegios solo sobre `u132762550_COBAEM`.
7. Llaves SSH de despliegue con passphrase; sin contraseñas SSH.

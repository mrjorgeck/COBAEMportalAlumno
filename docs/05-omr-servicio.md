# 05 — Servicio OMR (microservicio externo)

Decisión de producto: el OMR se implementa como **servicio externo separado**, consumido por el portal vía HTTPS con token. El portal funciona completo sin él (fallback: importación CSV de respuestas / captura manual).

---

## 1. Stack y despliegue del servicio

| Aspecto | Elección |
|---|---|
| Lenguaje | Python 3.12 |
| Framework | FastAPI + Uvicorn |
| Visión | OpenCV (detección de bordes, homografía, umbralizado, conteo de píxeles por burbuja) |
| Despliegue | VPS pequeño (1 vCPU / 1-2 GB) o PC del plantel con túnel/IP fija; Docker recomendado |
| Auth | Header `X-Api-Key` compartida (rotable), HTTPS obligatorio |
| Estado | Sin base de datos propia: procesa y responde; el estado vive en el portal |

## 2. Contrato REST

### POST `/v1/procesar`
Portal → OMR. Multipart: `imagen` (jpg/png, ≤ 8 MB), `plantilla_id`, `hoja_ref` (id de `hojas_respuesta`).

Respuesta síncrona (lote pequeño) o `202 Accepted` + callback (lote grande):

```json
{
  "hoja_ref": 1234,
  "estado": "procesada",            // procesada | requiere_revision | error
  "confianza_global": 92.5,
  "folio_detectado": "000345",       // null si no legible
  "imagen_procesada_b64": "...",     // opcional, con zonas marcadas para revisión
  "respuestas": [
    {"pregunta": 1, "opcion": "B", "confianza": 98.2, "flags": []},
    {"pregunta": 2, "opcion": null, "confianza": 99.0, "flags": ["sin_marca"]},
    {"pregunta": 3, "opcion": "X",  "confianza": 40.1, "flags": ["doble_marca"]}
  ],
  "errores": []
}
```

### POST `{portal}/api/omr/callback` (modo asíncrono)
OMR → Portal, mismo payload + `X-Api-Key`. El portal actualiza `hojas_respuesta` y `respuestas`.

### GET `/v1/salud`
Healthcheck (el panel admin muestra estado del servicio).

### PUT `/v1/plantillas/{id}`
Portal sube definición JSON de plantilla (zonas de respuesta, nº preguntas, opciones por pregunta, zona de folio, marcas de registro/fiduciales).

## 3. Pipeline de procesamiento (dentro del servicio)

1. Carga y normalización de imagen (EXIF, escala).
2. Detección de la hoja: marcas fiduciales de esquina (la plantilla impresa debe incluirlas) o contorno mayor.
3. Corrección de perspectiva (homografía) e inclinación.
4. Mapeo de zonas según plantilla JSON.
5. Por burbuja: umbral adaptativo + porcentaje de relleno → marcada/no marcada.
6. Reglas: 0 marcas → `sin_marca`; ≥2 → `doble_marca`; relleno ambiguo → baja confianza.
7. Lectura de folio: zona de burbujas numéricas (recomendado en el diseño de la hoja) o null → captura manual en el portal.
8. Confianza global = f(nitidez, % preguntas confiables); < umbral (config, default 85%) → `requiere_revision`.

## 4. Reglas del lado del portal

- `EnviarHojaAOmr` (job): toma hojas `pendiente`, llama al servicio, guarda resultado. Reintentos: 3 con backoff; fallo definitivo → estado `error` + aviso en panel.
- Toda hoja `requiere_revision` (o con flags) entra a la cola de revisión manual (pantalla imagen vs respuestas).
- Validación humana obligatoria antes de usar respuestas en cálculo (OMR-09): estado `validada`.
- Vinculación: `folio_detectado` (o capturado) + ciclo → `procesos_ingreso`; sin coincidencia → alerta.
- Exportación (OMR-10/12, RF-18): CSV enriquecido por examen con folio, CURP, nombre, respuestas 1..N; reporte de hojas por estado.
- Si el servicio OMR no está disponible: el flujo continúa por importación CSV (`tipo_importacion: respuestas_examen`) o captura manual por hoja.

## 5. Requisitos de la hoja de respuestas (recomendación al plantel)

Para precisión alta con fotos de celular: 4 marcas fiduciales en esquinas, burbujas ≥ 4 mm, zona de folio en burbujas numéricas, papel sin doblar, foto cenital con buena luz. Documentar guía de fotografiado de 1 página para el personal.

## 6. Criterios de aceptación del piloto OMR

1. ≥ 95% de precisión en hojas escaneadas; ≥ 90% en fotos razonables.
2. 100% de dobles marcas y sin-marca detectadas o enviadas a revisión.
3. Procesar 300 hojas en < 30 min incluyendo revisión manual de dudosas.
4. Ninguna respuesta entra a resultados sin validación humana.

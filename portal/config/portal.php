<?php

/*
|--------------------------------------------------------------------------
| Reglas de negocio configurables del Portal de Nuevo Ingreso
|--------------------------------------------------------------------------
| Todo lo que pueda cambiar entre ciclos sin tocar código vive aquí
| (o en catálogos administrables). Ver CLAUDE.md.
*/

return [

    // Folio interno (RF-37). Placeholders: año, plantel, consecutivo.
    'folio' => [
        'formato' => 'NI-%d-%s-%04d',
    ],

    // Rangos de nivel de riesgo por defecto (§13.5). Los definitivos viven
    // en el catálogo 'nivel_riesgo' (metadata) y son administrables.
    'riesgo' => [
        'bajo' => ['min' => 80, 'max' => 100],
        'medio' => ['min' => 60, 'max' => 79.99],
        'alto' => ['min' => 40, 'max' => 59.99],
        'critico' => ['min' => 0, 'max' => 39.99],
    ],

    // Sesión del alumno (minutos de inactividad).
    'sesion_alumno_minutos' => 30,

    // El plantel fija la fecha oficial en PORTAL_AVISO_PRIVACIDAD_FECHA_PUBLICACION.
    'aviso_privacidad_fecha_publicacion' => env('PORTAL_AVISO_PRIVACIDAD_FECHA_PUBLICACION', '[colocar fecha de publicacion]'),

    // Versión de plantilla PDF que se asigna a procesos nuevos (ADR-07).
    'pdf_plantilla_actual' => 'v2026',

    // Columnas del export "archivo enriquecido" para la plataforma federal
    // (RF-18). PENDIENTE: confirmar estructura oficial con el plantel
    // (docs/09 §6.4). Se define aquí para no hardcodear en el exporter.
    'export_federal' => [
        'columnas' => [
            'folio_examen',
            'curp',
            'nombre_completo',
            // + respuestas 1..N se agregan dinámicamente
        ],
    ],

    // Servicio OMR externo (Fase 4).
    'omr' => [
        'url' => env('OMR_SERVICE_URL'),
        'key' => env('OMR_SERVICE_KEY'),
        'timeout' => 30,
        'umbral_confianza' => 85, // % mínimo para no requerir revisión
    ],
];

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
        h1 { font-size: 18px; margin: 0; }
        h2 { font-size: 13px; margin: 14px 0 6px; border-bottom: 1px solid #374151; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        td { border: 1px solid #d1d5db; padding: 5px; vertical-align: top; }
        .label { font-weight: bold; background: #f3f4f6; width: 28%; }
        .muted { color: #6b7280; }
        .firma { height: 70px; text-align: center; vertical-align: bottom; }
    </style>
</head>
<body>
    <h1>COBAEM Plantel Ario de Rosales</h1>
    <p class="muted">Formato de inscripcion v2026. Encabezado institucional generico pendiente de ajustar al formato oficial del plantel.</p>

    <h2>Encabezado</h2>
    <table>
        <tr><td class="label">Periodo escolar</td><td>{{ $proceso->ciclo->periodo_escolar }}</td><td class="label">Ciclo</td><td>{{ $proceso->ciclo->anio }}</td></tr>
        <tr><td class="label">Plantel</td><td>{{ $proceso->plantel->nombre }}</td><td class="label">Clave</td><td>{{ $proceso->plantel->clave }}</td></tr>
        <tr><td class="label">Folio examen</td><td>{{ $proceso->folio_examen }}</td><td class="label">Folio portal</td><td>{{ $proceso->folio_registro }}</td></tr>
    </table>

    <h2>Datos del estudiante</h2>
    <table>
        <tr><td class="label">Nombre</td><td>{{ $proceso->alumno->nombre_completo }}</td><td class="label">CURP</td><td>{{ $proceso->alumno->curp }}</td></tr>
        <tr><td class="label">Fecha nacimiento</td><td>{{ $proceso->alumno->fecha_nacimiento?->format('d/m/Y') }}</td><td class="label">Semestre</td><td>{{ $proceso->semestre_solicitado }}</td></tr>
    </table>

    <h2>Direccion particular actual</h2>
    <table>
        <tr><td class="label">Domicilio</td><td>{{ $proceso->contacto?->domicilio }}</td><td class="label">Colonia</td><td>{{ $proceso->contacto?->colonia }}</td></tr>
        <tr><td class="label">Codigo postal</td><td>{{ $proceso->contacto?->codigo_postal }}</td><td class="label">Celular</td><td>{{ $proceso->contacto?->celular }}</td></tr>
    </table>

    <h2>Datos de contacto actual</h2>
    <table>
        <tr><td class="label">Telefono</td><td>{{ $proceso->contacto?->telefono }}</td><td class="label">Correo</td><td>{{ $proceso->contacto?->correo }}</td></tr>
    </table>

    <h2>Escuela de procedencia</h2>
    <table>
        <tr><td class="label">Promedio</td><td>{{ $proceso->promedio_secundaria }}</td><td class="label">Folio examen</td><td>{{ $proceso->folio_examen }}</td></tr>
    </table>

    <h2>Datos de tutor</h2>
    <table>
        <tr><td class="label">Nombre</td><td>{{ $proceso->tutor?->nombre_completo }}</td><td class="label">Celular</td><td>{{ $proceso->tutor?->celular }}</td></tr>
    </table>

    <h2>Datos de madre</h2>
    <table>
        <tr><td class="label">Nombre</td><td>{{ $proceso->madre?->nombre_completo }}</td><td class="label">Celular</td><td>{{ $proceso->madre?->celular }}</td></tr>
    </table>

    <h2>Otros datos</h2>
    <table>
        <tr><td class="label">Seguro medico</td><td>{{ $proceso->otrosDatos?->no_seguro_medico }}</td><td class="label">Peso/estatura</td><td>{{ $proceso->otrosDatos?->peso }} / {{ $proceso->otrosDatos?->estatura }}</td></tr>
    </table>

    <h2>Aviso de privacidad</h2>
    <p>El alumno y su tutor manifiestan haber leido y aceptado el aviso de privacidad publicado por el plantel. Texto legal definitivo pendiente de validacion institucional.</p>

    <table>
        <tr><td class="firma">Nombre completo y firma del alumno<br>{{ $proceso->alumno->nombre_completo }}</td><td class="firma">Firma de tutor</td></tr>
    </table>
</body>
</html>

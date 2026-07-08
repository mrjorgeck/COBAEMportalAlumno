<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 7mm 8mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 8.5px; color: #1f1f1f; line-height: 1.1; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1.7px 2px; vertical-align: bottom; }
        .header td { vertical-align: top; }
        .logo { width: 74px; max-height: 74px; }
        .logo-box { width: 70px; height: 54px; border: 1px solid #808080; text-align: center; padding-top: 22px; color: #666; }
        .title { font-size: 13px; font-weight: bold; text-align: center; }
        .subtitle { font-size: 10px; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .periodo-label { font-weight: bold; margin-top: 3px; }
        .periodo { font-size: 23px; line-height: 1; }
        .ficha-label { font-size: 12px; font-weight: bold; margin-top: 4px; }
        .section { background: #c7d9a3; color: #252525; font-size: 13px; text-align: center; text-transform: uppercase; padding: 2px; letter-spacing: .2px; }
        .row { margin-top: 2px; }
        .label { white-space: nowrap; font-weight: bold; }
        .line { border-bottom: 1px solid #222; min-height: 10px; white-space: nowrap; overflow: hidden; }
        .line-center { text-align: center; }
        .checks { width: 175px; font-size: 8px; line-height: .95; }
        .check-box { display: inline-block; width: 18px; text-align: center; border-bottom: 1px solid #111; font-weight: bold; }
        .privacy { margin-top: 11px; font-size: 9.5px; font-weight: bold; line-height: 1.55; }
        .signature { margin-top: 31px; text-align: center; }
        .signature-line { border-bottom: 1px solid #222; width: 100%; height: 12px; }
        .signature-name { font-size: 8.5px; }
        .trace { margin-top: 6px; font-size: 6.5px; color: #666; text-align: right; }
    </style>
</head>
<body>
@php
    $generadoEn = $generadoEn ?? now();
    $logoPath = resource_path('images/pdf/logo-cobaem.png');
    $logoDataUri = is_file($logoPath) ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath)) : null;
    $value = fn ($text) => trim((string) ($text ?? ''));
    $line = fn ($text = null) => $value($text) !== '' ? e($value($text)) : '&nbsp;';
    $catalogo = fn ($catalogo = null) => $catalogo?->nombre;
    $tipoEstudianteClave = $proceso->tipoEstudiante?->clave;
    $marcado = fn (string $clave) => $tipoEstudianteClave === $clave ? 'X' : '&nbsp;';
    $fechaNacimiento = $proceso->alumno->fecha_nacimiento?->format('d/m/Y');
    $edad = $proceso->alumno->fecha_nacimiento?->diffInYears($generadoEn);
    $semestre = ((int) $proceso->semestre_solicitado).'&deg;';
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril', 5 => 'mayo', 6 => 'junio',
        7 => 'julio', 8 => 'agosto', 9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];
    $lugarExpedicion = config('portal.pdf.lugar_expedicion');
    $clavePlantel = $proceso->plantel->clave_oficial ?: $proceso->plantel->clave;
@endphp

<table class="header">
    <tr>
        <td style="width: 22%;">
            @if ($logoDataUri)
                <img class="logo" src="{{ $logoDataUri }}" alt="COBAEM">
            @else
                <div class="logo-box">LOGO</div>
            @endif
            <div class="periodo-label">Periodo escolar</div>
            <div class="periodo">{{ $line($proceso->ciclo->periodo_escolar) }}</div>
        </td>
        <td style="width: 56%;" class="center">
            <div class="title">SOLICITUD DE INSCRIPCI&Oacute;N</div>
            <div class="subtitle">Colegio de Bachilleres del Estado de Michoac&aacute;n</div>
            <div>PLANTEL: {{ mb_strtoupper($proceso->plantel->nombre) }}</div>
            <div>{{ $line($clavePlantel) }}</div>
        </td>
        <td style="width: 22%;" class="center">
            <div class="ficha-label">No. De Ficha</div>
            <table><tr><td class="line line-center">{!! $line($proceso->folio_examen) !!}</td></tr></table>
        </td>
    </tr>
</table>

<table class="row">
    <tr>
        <td class="label" style="width: 245px;">Solicito Inscripci&oacute;n a {!! $semestre !!} Semestre como estudiante:</td>
        <td class="checks">
            Regular <span class="check-box">{!! $marcado('REG') !!}</span><br>
            Repetidor <span class="check-box">{!! $marcado('REP') !!}</span><br>
            Condicionado <span class="check-box">{!! $marcado('CON') !!}</span><br>
            Debe Materias Sec. <span class="check-box">{!! $marcado('DMS') !!}</span>
        </td>
        <td class="label right" style="width: 72px;">Paraescolar:</td>
        <td class="line">{!! $line($catalogo($proceso->paraescolar)) !!}</td>
    </tr>
</table>

<table class="row"><tr><td class="section">DATOS DEL ESTUDIANTE</td></tr></table>
<table>
    <tr>
        <td class="label">Nombre(s)</td><td class="line" style="width: 27%;">{!! $line($proceso->alumno->nombres) !!}</td>
        <td class="label">Primer Ap.</td><td class="line" style="width: 27%;">{!! $line($proceso->alumno->primer_apellido) !!}</td>
        <td class="label">Segundo Ap.</td><td class="line" style="width: 24%;">{!! $line($proceso->alumno->segundo_apellido) !!}</td>
    </tr>
    <tr>
        <td class="label">Estado Civil</td><td class="line">{!! $line($catalogo($proceso->alumno->estadoCivil)) !!}</td>
        <td class="label" colspan="2">Fecha de Nacimiento (D&iacute;a - Mes - A&ntilde;o)</td><td class="line" colspan="2">{!! $line($fechaNacimiento) !!}</td>
    </tr>
    <tr>
        <td class="label">Edad</td><td class="line">{!! $line($edad) !!}</td>
        <td class="label">Sexo</td><td class="line">{!! $line($catalogo($proceso->alumno->sexo)) !!}</td>
        <td class="label">Nacionalidad</td><td class="line">{!! $line($catalogo($proceso->alumno->nacionalidad)) !!}</td>
    </tr>
    <tr>
        <td class="label">Entidad de Nacimiento</td><td class="line" colspan="2">{!! $line($catalogo($proceso->alumno->entidadNacimiento)) !!}</td>
        <td class="label">Municipio de Nacimiento</td><td class="line" colspan="2">{!! $line($catalogo($proceso->alumno->municipioNacimiento)) !!}</td>
    </tr>
    <tr><td class="label">CURP</td><td class="line" colspan="5">{!! $line($proceso->alumno->curp) !!}</td></tr>
</table>

<table class="row"><tr><td class="section">DIRECCI&Oacute;N PARTICULAR ACTUAL</td></tr></table>
<table>
    <tr>
        <td class="label">Municipio</td><td class="line" style="width: 33%;">{!! $line($catalogo($proceso->contacto?->municipio)) !!}</td>
        <td class="label">Localidad</td><td class="line" style="width: 33%;">{!! $line($catalogo($proceso->contacto?->localidad)) !!}</td>
        <td class="label">C.P.</td><td class="line" style="width: 15%;">{!! $line($proceso->contacto?->codigo_postal) !!}</td>
    </tr>
    <tr>
        <td class="label">Domicilio (Calle y no.)</td><td class="line" colspan="3">{!! $line($proceso->contacto?->domicilio) !!}</td>
        <td class="label">Colonia</td><td class="line">{!! $line($proceso->contacto?->colonia) !!}</td>
    </tr>
</table>

<table class="row"><tr><td class="section">DATOS DE CONTACTO ACTUAL</td></tr></table>
<table>
    <tr>
        <td class="label">Tel&eacute;fono</td><td class="line" style="width: 18%;">{!! $line($proceso->contacto?->telefono) !!}</td>
        <td class="label">Celular</td><td class="line" style="width: 25%;">{!! $line($proceso->contacto?->celular) !!}</td>
        <td class="label">correo electr&oacute;nico</td><td class="line">{!! $line($proceso->contacto?->correo) !!}</td>
    </tr>
</table>

<table class="row"><tr><td class="section">ESCUELA DE PROCEDENCIA</td></tr></table>
<table>
    <tr>
        <td class="label">Entidad</td><td class="line">{!! $line($catalogo($proceso->entidadSecundaria)) !!}</td>
        <td class="label">Municipio</td><td class="line">{!! $line($catalogo($proceso->municipioSecundaria)) !!}</td>
    </tr>
    <tr>
        <td class="label">Nombre de la Escuela</td><td class="line" colspan="2">{!! $line($catalogo($proceso->secundariaProcedencia)) !!}</td>
        <td class="label">Promedio</td><td class="line">{!! $line($proceso->promedio_secundaria) !!}</td>
    </tr>
</table>

<table class="row"><tr><td class="section">DATOS DE TUTOR</td></tr></table>
<table>
    <tr>
        <td class="label">Nombre(s)</td><td class="line">{!! $line($proceso->tutor?->nombres) !!}</td>
        <td class="label">Primer Ap.</td><td class="line">{!! $line($proceso->tutor?->primer_apellido) !!}</td>
        <td class="label">Segundo Ap.</td><td class="line">{!! $line($proceso->tutor?->segundo_apellido) !!}</td>
    </tr>
    <tr>
        <td class="label">Tel&eacute;fono</td><td class="line">{!! $line($proceso->tutor?->telefono) !!}</td>
        <td class="label">Celular</td><td class="line">{!! $line($proceso->tutor?->celular) !!}</td>
        <td class="label">Ocupaci&oacute;n</td><td class="line">{!! $line($catalogo($proceso->tutor?->ocupacion)) !!}</td>
        <td class="label">Estudios</td><td class="line">{!! $line($catalogo($proceso->tutor?->estudios)) !!}</td>
    </tr>
</table>

<table class="row"><tr><td class="section">DATOS DE MADRE</td></tr></table>
<table>
    <tr>
        <td class="label">Nombre(s)</td><td class="line">{!! $line($proceso->madre?->nombres) !!}</td>
        <td class="label">Primer Ap.</td><td class="line">{!! $line($proceso->madre?->primer_apellido) !!}</td>
        <td class="label">Segundo Ap.</td><td class="line">{!! $line($proceso->madre?->segundo_apellido) !!}</td>
    </tr>
    <tr>
        <td class="label">Tel&eacute;fono</td><td class="line">{!! $line($proceso->madre?->telefono) !!}</td>
        <td class="label">Celular</td><td class="line">{!! $line($proceso->madre?->celular) !!}</td>
        <td class="label">Ocupaci&oacute;n</td><td class="line" colspan="3">{!! $line($catalogo($proceso->madre?->ocupacion)) !!}</td>
    </tr>
    <tr><td class="label">M&aacute;ximo Grado de Estudios</td><td class="line" colspan="7">{!! $line($catalogo($proceso->madre?->estudios)) !!}</td></tr>
</table>

<table class="row"><tr><td class="section">OTROS DATOS</td></tr></table>
<table>
    <tr>
        <td class="label">No. Seguro M&eacute;dico</td><td class="line">{!! $line($proceso->otrosDatos?->no_seguro_medico) !!}</td>
        <td class="label">Becas</td><td class="line" colspan="2">{!! $line($catalogo($proceso->otrosDatos?->beca)) !!}</td>
    </tr>
    <tr>
        <td class="label">Estatura</td><td class="line">{!! $line($proceso->otrosDatos?->estatura) !!}</td>
        <td class="label">Peso</td><td class="line">{!! $line($proceso->otrosDatos?->peso) !!}</td>
        <td class="label">Tipo Sangre RH:</td><td class="line">{!! $line($catalogo($proceso->otrosDatos?->tipoSangre)) !!}</td>
    </tr>
</table>

<table class="row">
    <tr>
        <td class="label">Lugar y Fecha Actual:</td>
        <td>
            {{ $lugarExpedicion }} a {{ $generadoEn->day }} de {{ $meses[(int) $generadoEn->month] }} de {{ $generadoEn->year }}.
        </td>
    </tr>
</table>

<p class="privacy">He le&iacute;do y acepto las pol&iacute;ticas de protecci&oacute;n de datos personales del aviso de privacidad del Colegio de Bachilleres del Estado de Michoac&aacute;n</p>

<div class="signature">
    <div class="signature-line">{{ $proceso->alumno->nombre_completo }}</div>
    <div class="signature-name">Nombre completo</div>
</div>

<div class="trace">
    Folio portal: {{ $proceso->folio_registro }} | Plantilla: {{ $proceso->plantilla_pdf_version }} | Generado: {{ $generadoEn->format('d/m/Y H:i') }}
</div>
</body>
</html>

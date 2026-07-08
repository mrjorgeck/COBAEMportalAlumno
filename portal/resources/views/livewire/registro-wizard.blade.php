@php
    $input = 'mt-1 min-h-11 w-full rounded border-gray-300 text-sm';
    $select = $input;
    $telefonoPattern = '[0-9+()\\-\\s]{7,20}';
    $isRequired = fn (string $field) => in_array($field, $requiredFields, true);
    $id = fn (string $field) => 'form_'.$field;
    $helpFields = ['curp', 'folio_examen'];
    $counterFields = ['curp'];
    $describedBy = fn (string $field) => collect([
        in_array($field, $helpFields, true) ? $id($field).'_ayuda' : null,
        in_array($field, $counterFields, true) ? $id($field).'_contador' : null,
        $errors->has('form.'.$field) ? $id($field).'_error' : null,
    ])
        ->filter()
        ->implode(' ');
@endphp

<form wire:submit="submit" class="mt-4 space-y-5">
    <div class="rounded bg-white p-3 text-sm shadow-sm">
        <div class="flex items-center justify-between">
            <span>Paso {{ $step }} de 6</span>
            <span class="font-semibold text-cobaem-900">
                {{ ['CURP y folio', 'Datos personales', 'Domicilio y contacto', 'Escuela', 'Familia', 'Privacidad'][$step - 1] }}
            </span>
        </div>
        <div class="mt-3 h-2 rounded bg-gray-100">
            <div class="h-2 rounded bg-cobaem-900" style="width: {{ (int) (($step / 6) * 100) }}%"></div>
        </div>
    </div>

    <x-leyenda-obligatorios />

    <div class="rounded border border-cobaem-100 bg-cobaem-100 p-4 text-sm text-cobaem-900">
        Tu avance se guarda en cada paso; puedes volver despues con tu CURP.
    </div>

    @if ($step === 1)
        <section wire:key="registro-step-1" class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo
                for="{{ $id('curp') }}"
                label="CURP"
                :required="$isRequired('curp')"
                help="Escribe los 18 caracteres de tu CURP en mayusculas."
                help-href="https://www.gob.mx/curp/"
                help-link="No conoces tu CURP? Consultala en gob.mx">
                <div x-data="{ curpLength: 0 }" x-init="curpLength = $refs.curp.value.length">
                    <input id="{{ $id('curp') }}" x-ref="curp" x-on:input="curpLength = $event.target.value.length" wire:key="registro-curp" name="curp" wire:model="form.curp" maxlength="18" autocomplete="section-curp one-time-code" autocapitalize="characters" autocorrect="off" aria-required="{{ $isRequired('curp') ? 'true' : 'false' }}" @if($describedBy('curp')) aria-describedby="{{ $describedBy('curp') }}" @endif required class="{{ $input }} uppercase">
                    <p id="{{ $id('curp') }}_contador" class="mt-1 text-xs text-gray-600">
                        <span x-text="curpLength"></span>/18 caracteres
                    </p>
                </div>
            </x-campo>
            <x-campo for="{{ $id('folio_examen') }}" label="Folio de examen (opcional)" :required="$isRequired('folio_examen')" help="Si ya lo tienes, escríbelo como aparece en la hoja de respuestas o comprobante. Si no, puedes continuar sin capturarlo.">
                <input id="{{ $id('folio_examen') }}" wire:key="registro-folio-examen" name="folio_examen" wire:model="form.folio_examen" autocomplete="section-folio one-time-code" aria-required="{{ $isRequired('folio_examen') ? 'true' : 'false' }}" @if($describedBy('folio_examen')) aria-describedby="{{ $describedBy('folio_examen') }}" @endif class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('folio_examen_confirmacion') }}" label="Confirmar folio de examen (solo si lo capturaste)" :required="$isRequired('folio_examen_confirmacion')">
                <input id="{{ $id('folio_examen_confirmacion') }}" wire:key="registro-folio-examen-confirmacion" name="folio_examen_confirmacion" wire:model="form.folio_examen_confirmacion" autocomplete="section-folio-confirmacion one-time-code" aria-required="{{ $isRequired('folio_examen_confirmacion') ? 'true' : 'false' }}" @if($describedBy('folio_examen_confirmacion')) aria-describedby="{{ $describedBy('folio_examen_confirmacion') }}" @endif class="{{ $input }}">
            </x-campo>
            <input type="hidden" name="semestre_solicitado" wire:model="form.semestre_solicitado">
        </section>
    @elseif ($step === 2)
        <section wire:key="registro-step-2" class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('nombres') }}" label="Nombre(s)" :required="$isRequired('nombres')">
                <input id="{{ $id('nombres') }}" wire:key="registro-nombres" name="nombres" autocomplete="section-alumno given-name" wire:model="form.nombres" aria-required="{{ $isRequired('nombres') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('primer_apellido') }}" label="Primer apellido" :required="$isRequired('primer_apellido')">
                <input id="{{ $id('primer_apellido') }}" wire:key="registro-primer-apellido" name="primer_apellido" autocomplete="section-alumno family-name" wire:model="form.primer_apellido" aria-required="{{ $isRequired('primer_apellido') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('segundo_apellido') }}" label="Segundo apellido" :required="$isRequired('segundo_apellido')">
                <input id="{{ $id('segundo_apellido') }}" wire:key="registro-segundo-apellido" name="segundo_apellido" autocomplete="section-alumno additional-name" wire:model="form.segundo_apellido" aria-required="{{ $isRequired('segundo_apellido') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <fieldset class="grid gap-2 text-sm sm:grid-cols-3">
                <legend class="sm:col-span-3 font-medium text-gray-800">
                    Fecha de nacimiento <x-obligatorio :required="$isRequired('fecha_nacimiento')" />
                </legend>
                <select wire:key="registro-fecha-nacimiento-dia" name="fecha_nacimiento_dia" autocomplete="section-alumno bday-day" wire:model="form.fecha_nacimiento_dia" required aria-required="{{ $isRequired('fecha_nacimiento') ? 'true' : 'false' }}" class="{{ $select }}">
                    <option value="">Dia</option>
                    @foreach ($diasNacimiento as $dia)<option value="{{ $dia }}">{{ str_pad((string) $dia, 2, '0', STR_PAD_LEFT) }}</option>@endforeach
                </select>
                <select wire:key="registro-fecha-nacimiento-mes" name="fecha_nacimiento_mes" autocomplete="section-alumno bday-month" wire:model="form.fecha_nacimiento_mes" required aria-required="{{ $isRequired('fecha_nacimiento') ? 'true' : 'false' }}" class="{{ $select }}">
                    <option value="">Mes</option>
                    @foreach ($mesesNacimiento as $mes)<option value="{{ $mes }}">{{ str_pad((string) $mes, 2, '0', STR_PAD_LEFT) }}</option>@endforeach
                </select>
                <select wire:key="registro-fecha-nacimiento-anio" name="fecha_nacimiento_anio" autocomplete="section-alumno bday-year" wire:model="form.fecha_nacimiento_anio" required aria-required="{{ $isRequired('fecha_nacimiento') ? 'true' : 'false' }}" class="{{ $select }}">
                    <option value="">Anio</option>
                    @foreach ($aniosNacimiento as $anio)<option value="{{ $anio }}">{{ $anio }}</option>@endforeach
                </select>
                <input type="hidden" name="fecha_nacimiento" wire:model="form.fecha_nacimiento">
                @error('form.fecha_nacimiento') <p class="sm:col-span-3 text-sm text-red-700">{{ $message }}</p> @enderror
            </fieldset>
            @foreach (['sexo' => 'Sexo', 'nacionalidad' => 'Nacionalidad', 'estado_civil' => 'Estado civil', 'entidad' => 'Entidad de nacimiento', 'municipio' => 'Municipio de nacimiento', 'tipo_estudiante' => 'Tipo de estudiante'] as $tipo => $label)
                @php $field = $tipo === 'entidad' ? 'entidad_nacimiento_id' : ($tipo === 'municipio' ? 'municipio_nacimiento_id' : $tipo.'_id'); @endphp
                <x-campo for="{{ $id($field) }}" :label="$label" :required="$isRequired($field)">
                    <select id="{{ $id($field) }}" wire:key="registro-{{ $field }}" name="{{ $field }}" wire:model="form.{{ $field }}" aria-required="{{ $isRequired($field) ? 'true' : 'false' }}" required class="{{ $select }}">
                        <option value="">Selecciona</option>
                        @foreach ($catalogos[$tipo] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach
                    </select>
                </x-campo>
            @endforeach
            <x-campo for="{{ $id('paraescolar_id') }}" label="Paraescolar" :required="$isRequired('paraescolar_id')">
                <select id="{{ $id('paraescolar_id') }}" wire:key="registro-paraescolar" name="paraescolar_id" wire:model="form.paraescolar_id" aria-required="{{ $isRequired('paraescolar_id') ? 'true' : 'false' }}" required class="{{ $select }}">
                    <option value="">Selecciona</option>
                    @foreach ($catalogos['paraescolar']->groupBy(fn ($item) => $item->metadata['categoria'] ?? 'Opciones') as $categoria => $opciones)
                        <optgroup label="{{ $categoria }}">
                            @foreach ($opciones as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach
                        </optgroup>
                    @endforeach
                </select>
            </x-campo>
        </section>
    @elseif ($step === 3)
        <section wire:key="registro-step-3" class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('municipio_id') }}" label="Municipio de domicilio" :required="$isRequired('municipio_id')">
                <x-select-buscable label="Filtrar municipios" placeholder="Escribe para filtrar municipios">
                    <select id="{{ $id('municipio_id') }}" wire:key="registro-municipio" name="municipio_id" wire:model="form.municipio_id" aria-required="{{ $isRequired('municipio_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['municipio'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
                </x-select-buscable>
            </x-campo>
            <x-campo for="{{ $id('localidad_id') }}" label="Localidad de domicilio" :required="$isRequired('localidad_id')">
                <x-select-buscable label="Filtrar localidades" placeholder="Escribe para filtrar localidades">
                    <select id="{{ $id('localidad_id') }}" wire:key="registro-localidad" name="localidad_id" wire:model="form.localidad_id" aria-required="{{ $isRequired('localidad_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['localidad'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
                </x-select-buscable>
            </x-campo>
            <x-campo for="{{ $id('domicilio') }}" label="Domicilio, calle y numero" :required="$isRequired('domicilio')">
                <input id="{{ $id('domicilio') }}" wire:key="registro-domicilio" name="domicilio" autocomplete="section-alumno street-address" wire:model="form.domicilio" aria-required="{{ $isRequired('domicilio') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('colonia') }}" label="Colonia" :required="$isRequired('colonia')">
                <input id="{{ $id('colonia') }}" wire:key="registro-colonia" name="colonia" wire:model="form.colonia" aria-required="{{ $isRequired('colonia') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('codigo_postal') }}" label="Codigo postal" :required="$isRequired('codigo_postal')">
                <input id="{{ $id('codigo_postal') }}" wire:key="registro-codigo-postal" name="codigo_postal" autocomplete="section-alumno postal-code" wire:model="form.codigo_postal" aria-required="{{ $isRequired('codigo_postal') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('telefono') }}" label="Telefono" :required="$isRequired('telefono')">
                <input id="{{ $id('telefono') }}" wire:key="registro-telefono" name="telefono" type="tel" inputmode="tel" pattern="{{ $telefonoPattern }}" autocomplete="section-alumno tel" wire:model="form.telefono" aria-required="{{ $isRequired('telefono') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('celular') }}" label="Celular" :required="$isRequired('celular')">
                <input id="{{ $id('celular') }}" wire:key="registro-celular" name="celular" type="tel" inputmode="tel" pattern="{{ $telefonoPattern }}" autocomplete="section-alumno tel" wire:model="form.celular" aria-required="{{ $isRequired('celular') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('correo') }}" label="Correo" :required="$isRequired('correo')">
                <input id="{{ $id('correo') }}" wire:key="registro-correo" type="email" name="correo" autocomplete="section-alumno email" wire:model="form.correo" aria-required="{{ $isRequired('correo') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
        </section>
    @elseif ($step === 4)
        <section wire:key="registro-step-4" class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('entidad_secundaria_id') }}" label="Entidad" :required="$isRequired('entidad_secundaria_id')">
                <x-select-buscable label="Buscar entidad de secundaria" placeholder="Buscar entidad">
                    <select id="{{ $id('entidad_secundaria_id') }}" wire:key="registro-entidad-secundaria" name="entidad_secundaria_id" wire:model="form.entidad_secundaria_id" aria-required="{{ $isRequired('entidad_secundaria_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['entidad'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
                </x-select-buscable>
            </x-campo>
            <x-campo for="{{ $id('municipio_secundaria_id') }}" label="Municipio" :required="$isRequired('municipio_secundaria_id')">
                <x-select-buscable label="Buscar municipio de secundaria" placeholder="Buscar municipio">
                    <select id="{{ $id('municipio_secundaria_id') }}" wire:key="registro-municipio-secundaria" name="municipio_secundaria_id" wire:model="form.municipio_secundaria_id" aria-required="{{ $isRequired('municipio_secundaria_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['municipio'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
                </x-select-buscable>
            </x-campo>
            <x-campo for="{{ $id('secundaria_nombre') }}" label="Nombre de la escuela" :required="$isRequired('secundaria_nombre')">
                <input id="{{ $id('secundaria_nombre') }}" wire:key="registro-secundaria-nombre" name="secundaria_nombre" wire:model="form.secundaria_nombre" aria-required="{{ $isRequired('secundaria_nombre') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('tipo_secundaria_id') }}" label="Tipo de secundaria" :required="$isRequired('tipo_secundaria_id')">
                <select id="{{ $id('tipo_secundaria_id') }}" wire:key="registro-tipo-secundaria" name="tipo_secundaria_id" wire:model="form.tipo_secundaria_id" aria-required="{{ $isRequired('tipo_secundaria_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['tipo_secundaria'] as $item)<option value="{{ $item->id }}">Secundaria {{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('turno_secundaria_id') }}" label="Turno" :required="$isRequired('turno_secundaria_id')">
                <select id="{{ $id('turno_secundaria_id') }}" wire:key="registro-turno-secundaria" name="turno_secundaria_id" wire:model="form.turno_secundaria_id" aria-required="{{ $isRequired('turno_secundaria_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['turno'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('promedio_secundaria') }}" label="Promedio" :required="$isRequired('promedio_secundaria')">
                <input id="{{ $id('promedio_secundaria') }}" wire:key="registro-promedio-secundaria" type="number" name="promedio_secundaria" step="0.01" min="0" max="10" inputmode="decimal" wire:model="form.promedio_secundaria" aria-required="{{ $isRequired('promedio_secundaria') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
        </section>
    @elseif ($step === 5)
        <section wire:key="registro-step-5" class="space-y-4 rounded bg-white p-4 shadow-sm">
            @foreach ([
                'tutor_nombres' => ['Nombre(s) tutor', 'section-tutor given-name', null],
                'tutor_primer_apellido' => ['Primer apellido tutor', 'section-tutor family-name', null],
                'tutor_segundo_apellido' => ['Segundo apellido tutor', 'section-tutor additional-name', null],
                'tutor_telefono' => ['Telefono tutor', 'section-tutor tel', 'tel'],
                'tutor_celular' => ['Celular tutor', 'section-tutor tel', 'tel'],
                'madre_nombres' => ['Nombre(s) madre', 'section-madre given-name', null],
                'madre_primer_apellido' => ['Primer apellido madre', 'section-madre family-name', null],
                'madre_segundo_apellido' => ['Segundo apellido madre', 'section-madre additional-name', null],
                'madre_telefono' => ['Telefono madre', 'section-madre tel', 'tel'],
                'madre_celular' => ['Celular madre', 'section-madre tel', 'tel'],
            ] as $field => [$label, $autocomplete, $type])
                <x-campo for="{{ $id($field) }}" :label="$label" :required="$isRequired($field)">
                    <input id="{{ $id($field) }}" wire:key="registro-{{ $field }}" name="{{ $field }}" autocomplete="{{ $autocomplete }}" @if($type === 'tel') type="tel" inputmode="tel" pattern="{{ $telefonoPattern }}" @endif wire:model="form.{{ $field }}" aria-required="{{ $isRequired($field) ? 'true' : 'false' }}" @if($isRequired($field)) required @endif class="{{ $input }}">
                </x-campo>
            @endforeach
            @foreach ([
                'tutor_ocupacion_id' => ['Ocupacion tutor', 'ocupacion'],
                'tutor_estudios_id' => ['Estudios tutor', 'nivel_estudios'],
                'madre_ocupacion_id' => ['Ocupacion madre', 'ocupacion'],
                'madre_estudios_id' => ['Estudios madre', 'nivel_estudios'],
            ] as $field => [$label, $tipo])
                <x-campo for="{{ $id($field) }}" :label="$label" :required="$isRequired($field)">
                    <select id="{{ $id($field) }}" wire:key="registro-{{ $field }}" name="{{ $field }}" wire:model="form.{{ $field }}" aria-required="{{ $isRequired($field) ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos[$tipo] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
                </x-campo>
            @endforeach
        </section>
    @else
        <section wire:key="registro-step-6" class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('no_seguro_medico') }}" label="No. de seguro medico" :required="$isRequired('no_seguro_medico')">
                <input id="{{ $id('no_seguro_medico') }}" wire:key="registro-no-seguro-medico" name="no_seguro_medico" wire:model="form.no_seguro_medico" aria-required="{{ $isRequired('no_seguro_medico') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('beca_id') }}" label="Beca" :required="$isRequired('beca_id')">
                <select id="{{ $id('beca_id') }}" wire:key="registro-beca" name="beca_id" wire:model="form.beca_id" aria-required="{{ $isRequired('beca_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['beca'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('estatura') }}" label="Estatura" :required="$isRequired('estatura')">
                <input id="{{ $id('estatura') }}" wire:key="registro-estatura" type="number" name="estatura" step="0.01" inputmode="decimal" wire:model="form.estatura" aria-required="{{ $isRequired('estatura') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('peso') }}" label="Peso" :required="$isRequired('peso')">
                <input id="{{ $id('peso') }}" wire:key="registro-peso" type="number" name="peso" step="0.01" inputmode="decimal" wire:model="form.peso" aria-required="{{ $isRequired('peso') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('tipo_sangre_id') }}" label="Tipo de sangre" :required="$isRequired('tipo_sangre_id')">
                <select id="{{ $id('tipo_sangre_id') }}" wire:key="registro-tipo-sangre" name="tipo_sangre_id" wire:model="form.tipo_sangre_id" aria-required="{{ $isRequired('tipo_sangre_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['tipo_sangre'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <label class="mt-4 flex min-h-11 items-start gap-2 text-sm">
                <input id="{{ $id('acepto_privacidad') }}" wire:key="registro-acepto-privacidad" type="checkbox" name="acepto_privacidad" wire:model="form.acepto_privacidad" value="1" required aria-required="true" class="mt-1">
                <span>
                    Acepto el aviso de privacidad <x-obligatorio :required="$isRequired('acepto_privacidad')" />
                    <span class="mt-2 block text-gray-700">
                        El Colegio de Bachilleres del Estado de Michoacan, con la participacion operativa del Plantel Ario de Rosales, utilizara los datos personales proporcionados en el Portal Academico de Nuevo Ingreso para registro digital de aspirantes, generacion del formato de inscripcion en PDF, seguimiento de documentacion, consulta de resultados de evaluacion diagnostica, curso propedeutico, publicacion de grupo, matricula, horario, avisos institucionales, soporte, seguridad informatica, reportes internos y cumplimiento de obligaciones legales.
                    </span>
                    <span class="mt-2 block text-gray-700">
                        Antes de continuar, consulte el
                        <a href="{{ route('alumno.privacidad') }}" target="_blank" rel="noopener" class="font-semibold text-cobaem-900 underline">Aviso de Privacidad Integral</a>.
                    </span>
                </span>
            </label>
        </section>
    @endif

    @if ($errors->any())
        <div
            id="resumen-errores"
            tabindex="-1"
            x-data
            x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); $el.focus({ preventScroll: true }); })"
            class="rounded border border-red-200 bg-red-50 p-4 text-sm text-red-800 outline-none focus:ring-2 focus:ring-red-300"
        >
            <p class="font-semibold">Revisa estos campos para continuar:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->getMessages() as $field => $messages)
                    @php $target = str_replace('.', '_', $field); @endphp
                    <li>
                        <a href="#{{ $target }}" class="underline">{{ $messages[0] }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="flex gap-3">
        @if ($step > 1)
            <button type="button" wire:click="previous" wire:loading.attr="disabled" class="min-h-11 w-full rounded bg-gray-200 px-4 py-3 font-semibold disabled:cursor-not-allowed disabled:opacity-70">Anterior</button>
        @endif
        @if ($step < 6)
            <button type="button" wire:click="next" wire:loading.attr="disabled" wire:target="next" class="min-h-11 w-full rounded bg-cobaem-900 px-4 py-3 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-70">
                <span wire:loading.remove wire:target="next">Siguiente</span>
                <span wire:loading wire:target="next" class="inline-flex items-center justify-center gap-2">
                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    Revisando...
                </span>
            </button>
        @else
            <button wire:loading.attr="disabled" wire:target="submit" class="min-h-11 w-full rounded bg-cobaem-900 px-4 py-3 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-70">
                <span wire:loading.remove wire:target="submit">Finalizar registro</span>
                <span wire:loading wire:target="submit" class="inline-flex items-center justify-center gap-2">
                    <span class="h-4 w-4 animate-spin rounded-full border-2 border-white border-t-transparent"></span>
                    Guardando...
                </span>
            </button>
        @endif
    </div>
</form>

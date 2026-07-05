@php
    $input = 'mt-1 min-h-11 w-full rounded border-gray-300 text-sm';
    $select = $input;
    $isRequired = fn (string $field) => in_array($field, $requiredFields, true);
    $id = fn (string $field) => 'form_'.$field;
    $helpFields = ['curp', 'folio_examen'];
    $describedBy = fn (string $field) => collect([
        in_array($field, $helpFields, true) ? $id($field).'_ayuda' : null,
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
                {{ ['CURP y folio', 'Datos personales', 'Contacto', 'Escuela', 'Familia', 'Privacidad'][$step - 1] }}
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
        <section class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo
                for="{{ $id('curp') }}"
                label="CURP"
                :required="$isRequired('curp')"
                help-html='Escribe los 18 caracteres de tu CURP en mayusculas. <a href="https://www.gob.mx/curp/" target="_blank" rel="noopener noreferrer" class="font-semibold text-cobaem-900 underline">¿No conoces tu CURP? Consultala en gob.mx</a>.'>
                <input id="{{ $id('curp') }}" name="curp" wire:model="form.curp" maxlength="18" autocomplete="section-curp one-time-code" autocapitalize="characters" autocorrect="off" aria-required="{{ $isRequired('curp') ? 'true' : 'false' }}" @if($describedBy('curp')) aria-describedby="{{ $describedBy('curp') }}" @endif required class="{{ $input }} uppercase">
            </x-campo>
            <x-campo for="{{ $id('folio_examen') }}" label="Folio de examen" :required="$isRequired('folio_examen')" help="Lo encuentras en la hoja de respuestas o comprobante entregado al terminar tu examen.">
                <input id="{{ $id('folio_examen') }}" name="folio_examen" wire:model="form.folio_examen" autocomplete="section-folio one-time-code" aria-required="{{ $isRequired('folio_examen') ? 'true' : 'false' }}" @if($describedBy('folio_examen')) aria-describedby="{{ $describedBy('folio_examen') }}" @endif required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('folio_examen_confirmacion') }}" label="Confirmar folio de examen" :required="$isRequired('folio_examen_confirmacion')">
                <input id="{{ $id('folio_examen_confirmacion') }}" name="folio_examen_confirmacion" wire:model="form.folio_examen_confirmacion" autocomplete="section-folio-confirmacion one-time-code" aria-required="{{ $isRequired('folio_examen_confirmacion') ? 'true' : 'false' }}" @if($describedBy('folio_examen_confirmacion')) aria-describedby="{{ $describedBy('folio_examen_confirmacion') }}" @endif required class="{{ $input }}">
            </x-campo>
            <input type="hidden" name="semestre_solicitado" wire:model="form.semestre_solicitado">
        </section>
    @elseif ($step === 2)
        <section class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('nombres') }}" label="Nombre(s)" :required="$isRequired('nombres')">
                <input id="{{ $id('nombres') }}" name="nombres" autocomplete="section-alumno given-name" wire:model="form.nombres" aria-required="{{ $isRequired('nombres') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('primer_apellido') }}" label="Primer apellido" :required="$isRequired('primer_apellido')">
                <input id="{{ $id('primer_apellido') }}" name="primer_apellido" autocomplete="section-alumno family-name" wire:model="form.primer_apellido" aria-required="{{ $isRequired('primer_apellido') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('segundo_apellido') }}" label="Segundo apellido" :required="$isRequired('segundo_apellido')">
                <input id="{{ $id('segundo_apellido') }}" name="segundo_apellido" autocomplete="section-alumno additional-name" wire:model="form.segundo_apellido" aria-required="{{ $isRequired('segundo_apellido') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('fecha_nacimiento') }}" label="Fecha de nacimiento" :required="$isRequired('fecha_nacimiento')">
                <input id="{{ $id('fecha_nacimiento') }}" type="date" name="fecha_nacimiento" autocomplete="section-alumno bday" wire:model="form.fecha_nacimiento" aria-required="{{ $isRequired('fecha_nacimiento') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            @foreach (['sexo' => 'Sexo', 'nacionalidad' => 'Nacionalidad', 'estado_civil' => 'Estado civil', 'entidad' => 'Entidad de nacimiento', 'municipio' => 'Municipio de nacimiento', 'tipo_estudiante' => 'Tipo de estudiante'] as $tipo => $label)
                @php $field = $tipo === 'entidad' ? 'entidad_nacimiento_id' : ($tipo === 'municipio' ? 'municipio_nacimiento_id' : $tipo.'_id'); @endphp
                <x-campo for="{{ $id($field) }}" :label="$label" :required="$isRequired($field)">
                    <select id="{{ $id($field) }}" name="{{ $field }}" wire:model="form.{{ $field }}" aria-required="{{ $isRequired($field) ? 'true' : 'false' }}" required class="{{ $select }}">
                        <option value="">Selecciona</option>
                        @foreach ($catalogos[$tipo] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach
                    </select>
                </x-campo>
            @endforeach
            <x-campo for="{{ $id('paraescolar_id') }}" label="Paraescolar" :required="$isRequired('paraescolar_id')">
                <select id="{{ $id('paraescolar_id') }}" name="paraescolar_id" wire:model="form.paraescolar_id" aria-required="{{ $isRequired('paraescolar_id') ? 'true' : 'false' }}" class="{{ $select }}">
                    <option value="">Sin seleccionar</option>
                    @foreach ($catalogos['paraescolar'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach
                </select>
            </x-campo>
        </section>
    @elseif ($step === 3)
        <section class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('municipio_id') }}" label="Municipio" :required="$isRequired('municipio_id')">
                <select id="{{ $id('municipio_id') }}" name="municipio_id" wire:model="form.municipio_id" aria-required="{{ $isRequired('municipio_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['municipio'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('localidad_id') }}" label="Localidad" :required="$isRequired('localidad_id')">
                <select id="{{ $id('localidad_id') }}" name="localidad_id" wire:model="form.localidad_id" aria-required="{{ $isRequired('localidad_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['localidad'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('domicilio') }}" label="Domicilio" :required="$isRequired('domicilio')">
                <input id="{{ $id('domicilio') }}" name="domicilio" autocomplete="section-alumno street-address" wire:model="form.domicilio" aria-required="{{ $isRequired('domicilio') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('colonia') }}" label="Colonia" :required="$isRequired('colonia')">
                <input id="{{ $id('colonia') }}" name="colonia" wire:model="form.colonia" aria-required="{{ $isRequired('colonia') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('codigo_postal') }}" label="Codigo postal" :required="$isRequired('codigo_postal')">
                <input id="{{ $id('codigo_postal') }}" name="codigo_postal" autocomplete="section-alumno postal-code" wire:model="form.codigo_postal" aria-required="{{ $isRequired('codigo_postal') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('telefono') }}" label="Telefono" :required="$isRequired('telefono')">
                <input id="{{ $id('telefono') }}" name="telefono" type="tel" inputmode="numeric" autocomplete="section-alumno tel" wire:model="form.telefono" aria-required="{{ $isRequired('telefono') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('celular') }}" label="Celular" :required="$isRequired('celular')">
                <input id="{{ $id('celular') }}" name="celular" type="tel" inputmode="numeric" autocomplete="section-alumno tel" wire:model="form.celular" aria-required="{{ $isRequired('celular') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('correo') }}" label="Correo" :required="$isRequired('correo')">
                <input id="{{ $id('correo') }}" type="email" name="correo" autocomplete="section-alumno email" wire:model="form.correo" aria-required="{{ $isRequired('correo') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
        </section>
    @elseif ($step === 4)
        <section class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('entidad_secundaria_id') }}" label="Entidad" :required="$isRequired('entidad_secundaria_id')">
                <select id="{{ $id('entidad_secundaria_id') }}" name="entidad_secundaria_id" wire:model="form.entidad_secundaria_id" aria-required="{{ $isRequired('entidad_secundaria_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['entidad'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('municipio_secundaria_id') }}" label="Municipio" :required="$isRequired('municipio_secundaria_id')">
                <select id="{{ $id('municipio_secundaria_id') }}" name="municipio_secundaria_id" wire:model="form.municipio_secundaria_id" aria-required="{{ $isRequired('municipio_secundaria_id') ? 'true' : 'false' }}" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['municipio'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('secundaria_nombre') }}" label="Nombre de la escuela" :required="$isRequired('secundaria_nombre')">
                <input id="{{ $id('secundaria_nombre') }}" name="secundaria_nombre" wire:model="form.secundaria_nombre" aria-required="{{ $isRequired('secundaria_nombre') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('tipo_secundaria_id') }}" label="Tipo de secundaria" :required="$isRequired('tipo_secundaria_id')">
                <select id="{{ $id('tipo_secundaria_id') }}" name="tipo_secundaria_id" wire:model="form.tipo_secundaria_id" aria-required="{{ $isRequired('tipo_secundaria_id') ? 'true' : 'false' }}" class="{{ $select }}"><option value="">Sin seleccionar</option>@foreach ($catalogos['tipo_secundaria'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('turno_secundaria_id') }}" label="Turno" :required="$isRequired('turno_secundaria_id')">
                <select id="{{ $id('turno_secundaria_id') }}" name="turno_secundaria_id" wire:model="form.turno_secundaria_id" aria-required="{{ $isRequired('turno_secundaria_id') ? 'true' : 'false' }}" class="{{ $select }}"><option value="">Sin seleccionar</option>@foreach ($catalogos['turno'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <x-campo for="{{ $id('promedio_secundaria') }}" label="Promedio" :required="$isRequired('promedio_secundaria')">
                <input id="{{ $id('promedio_secundaria') }}" type="number" name="promedio_secundaria" step="0.01" min="0" max="10" inputmode="decimal" wire:model="form.promedio_secundaria" aria-required="{{ $isRequired('promedio_secundaria') ? 'true' : 'false' }}" required class="{{ $input }}">
            </x-campo>
        </section>
    @elseif ($step === 5)
        <section class="space-y-4 rounded bg-white p-4 shadow-sm">
            @foreach ([
                'tutor_nombres' => 'Nombre(s) tutor',
                'tutor_primer_apellido' => 'Primer apellido tutor',
                'tutor_segundo_apellido' => 'Segundo apellido tutor',
                'tutor_celular' => 'Celular tutor',
                'tutor_telefono' => 'Telefono tutor',
                'madre_nombres' => 'Nombre(s) madre',
                'madre_primer_apellido' => 'Primer apellido madre',
            ] as $field => $label)
                <x-campo for="{{ $id($field) }}" :label="$label" :required="$isRequired($field)">
                    <input id="{{ $id($field) }}" name="{{ $field }}" @if(str_contains($field, 'telefono') || str_contains($field, 'celular')) type="tel" inputmode="numeric" autocomplete="tel" @endif wire:model="form.{{ $field }}" aria-required="{{ $isRequired($field) ? 'true' : 'false' }}" @if($isRequired($field)) required @endif class="{{ $input }}">
                </x-campo>
            @endforeach
        </section>
    @else
        <section class="space-y-4 rounded bg-white p-4 shadow-sm">
            <x-campo for="{{ $id('no_seguro_medico') }}" label="No. de seguro medico" :required="$isRequired('no_seguro_medico')">
                <input id="{{ $id('no_seguro_medico') }}" name="no_seguro_medico" wire:model="form.no_seguro_medico" aria-required="{{ $isRequired('no_seguro_medico') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('estatura') }}" label="Estatura" :required="$isRequired('estatura')">
                <input id="{{ $id('estatura') }}" type="number" name="estatura" step="0.01" inputmode="decimal" wire:model="form.estatura" aria-required="{{ $isRequired('estatura') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('peso') }}" label="Peso" :required="$isRequired('peso')">
                <input id="{{ $id('peso') }}" type="number" name="peso" step="0.01" inputmode="decimal" wire:model="form.peso" aria-required="{{ $isRequired('peso') ? 'true' : 'false' }}" class="{{ $input }}">
            </x-campo>
            <x-campo for="{{ $id('tipo_sangre_id') }}" label="Tipo de sangre" :required="$isRequired('tipo_sangre_id')">
                <select id="{{ $id('tipo_sangre_id') }}" name="tipo_sangre_id" wire:model="form.tipo_sangre_id" aria-required="{{ $isRequired('tipo_sangre_id') ? 'true' : 'false' }}" class="{{ $select }}"><option value="">Sin seleccionar</option>@foreach ($catalogos['tipo_sangre'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select>
            </x-campo>
            <label class="mt-4 flex min-h-11 items-start gap-2 text-sm">
                <input id="{{ $id('acepto_privacidad') }}" type="checkbox" name="acepto_privacidad" wire:model="form.acepto_privacidad" value="1" required aria-required="true" class="mt-1">
                <span>Acepto el aviso de privacidad <x-obligatorio :required="$isRequired('acepto_privacidad')" /></span>
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

@php
    $input = 'mt-1 w-full rounded border-gray-300 text-sm';
    $select = $input;
@endphp

<form wire:submit="submit" class="mt-4 space-y-5">
    <div class="flex items-center justify-between rounded bg-white p-3 text-sm shadow-sm">
        <span>Paso {{ $step }} de 6</span>
        <span class="font-semibold text-cobaem-900">
            {{ ['CURP y folio', 'Datos personales', 'Contacto', 'Escuela', 'Familia', 'Privacidad'][$step - 1] }}
        </span>
    </div>

    @if ($step === 1)
        <section class="rounded bg-white p-4 shadow-sm">
            <label class="block text-sm">CURP<input wire:model="form.curp" maxlength="18" required class="{{ $input }} uppercase"></label>
            <label class="mt-3 block text-sm">Folio de examen<input wire:model="form.folio_examen" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Confirmar folio de examen<input wire:model="form.folio_examen_confirmacion" required class="{{ $input }}"></label>
            <input type="hidden" wire:model="form.semestre_solicitado">
        </section>
    @elseif ($step === 2)
        <section class="rounded bg-white p-4 shadow-sm">
            <label class="block text-sm">Nombre(s)<input wire:model="form.nombres" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Primer apellido<input wire:model="form.primer_apellido" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Segundo apellido<input wire:model="form.segundo_apellido" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Fecha de nacimiento<input type="date" wire:model="form.fecha_nacimiento" required class="{{ $input }}"></label>
            @foreach (['sexo' => 'Sexo', 'nacionalidad' => 'Nacionalidad', 'estado_civil' => 'Estado civil', 'entidad' => 'Entidad de nacimiento', 'municipio' => 'Municipio de nacimiento', 'tipo_estudiante' => 'Tipo de estudiante'] as $tipo => $label)
                <label class="mt-3 block text-sm">{{ $label }}
                    <select wire:model="form.{{ $tipo === 'entidad' ? 'entidad_nacimiento_id' : ($tipo === 'municipio' ? 'municipio_nacimiento_id' : $tipo.'_id') }}" required class="{{ $select }}">
                        <option value="">Selecciona</option>
                        @foreach ($catalogos[$tipo] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach
                    </select>
                </label>
            @endforeach
            <label class="mt-3 block text-sm">Paraescolar<select wire:model="form.paraescolar_id" class="{{ $select }}"><option value="">Sin seleccionar</option>@foreach ($catalogos['paraescolar'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
        </section>
    @elseif ($step === 3)
        <section class="rounded bg-white p-4 shadow-sm">
            <label class="block text-sm">Municipio<select wire:model="form.municipio_id" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['municipio'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
            <label class="mt-3 block text-sm">Localidad<select wire:model="form.localidad_id" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['localidad'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
            <label class="mt-3 block text-sm">Domicilio<input wire:model="form.domicilio" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Colonia<input wire:model="form.colonia" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Codigo postal<input wire:model="form.codigo_postal" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Telefono<input wire:model="form.telefono" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Celular<input wire:model="form.celular" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Correo<input type="email" wire:model="form.correo" class="{{ $input }}"></label>
        </section>
    @elseif ($step === 4)
        <section class="rounded bg-white p-4 shadow-sm">
            <label class="block text-sm">Entidad<select wire:model="form.entidad_secundaria_id" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['entidad'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
            <label class="mt-3 block text-sm">Municipio<select wire:model="form.municipio_secundaria_id" required class="{{ $select }}"><option value="">Selecciona</option>@foreach ($catalogos['municipio'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
            <label class="mt-3 block text-sm">Nombre de la escuela<input wire:model="form.secundaria_nombre" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Tipo de secundaria<select wire:model="form.tipo_secundaria_id" class="{{ $select }}"><option value="">Sin seleccionar</option>@foreach ($catalogos['tipo_secundaria'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
            <label class="mt-3 block text-sm">Turno<select wire:model="form.turno_secundaria_id" class="{{ $select }}"><option value="">Sin seleccionar</option>@foreach ($catalogos['turno'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
            <label class="mt-3 block text-sm">Promedio<input type="number" step="0.01" min="0" max="10" wire:model="form.promedio_secundaria" required class="{{ $input }}"></label>
        </section>
    @elseif ($step === 5)
        <section class="rounded bg-white p-4 shadow-sm">
            <label class="block text-sm">Nombre(s) tutor<input wire:model="form.tutor_nombres" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Primer apellido tutor<input wire:model="form.tutor_primer_apellido" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Segundo apellido tutor<input wire:model="form.tutor_segundo_apellido" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Celular tutor<input wire:model="form.tutor_celular" required class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Telefono tutor<input wire:model="form.tutor_telefono" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Nombre(s) madre<input wire:model="form.madre_nombres" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Primer apellido madre<input wire:model="form.madre_primer_apellido" class="{{ $input }}"></label>
        </section>
    @else
        <section class="rounded bg-white p-4 shadow-sm">
            <label class="block text-sm">No. de seguro medico<input wire:model="form.no_seguro_medico" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Estatura<input type="number" step="0.01" wire:model="form.estatura" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Peso<input type="number" step="0.01" wire:model="form.peso" class="{{ $input }}"></label>
            <label class="mt-3 block text-sm">Tipo de sangre<select wire:model="form.tipo_sangre_id" class="{{ $select }}"><option value="">Sin seleccionar</option>@foreach ($catalogos['tipo_sangre'] as $item)<option value="{{ $item->id }}">{{ $item->nombre }}</option>@endforeach</select></label>
            <label class="mt-4 flex items-start gap-2 text-sm"><input type="checkbox" wire:model="form.acepto_privacidad" value="1" required class="mt-1"> Acepto el aviso de privacidad.</label>
        </section>
    @endif

    @if ($errors->any())
        <div class="rounded bg-red-50 p-4 text-sm text-red-800">{{ $errors->first() }}</div>
    @endif

    <div class="flex gap-3">
        @if ($step > 1)
            <button type="button" wire:click="previous" class="w-full rounded bg-gray-200 px-4 py-3 font-semibold">Anterior</button>
        @endif
        @if ($step < 6)
            <button type="button" wire:click="next" class="w-full rounded bg-cobaem-900 px-4 py-3 font-semibold text-white">Siguiente</button>
        @else
            <button class="w-full rounded bg-cobaem-900 px-4 py-3 font-semibold text-white">Finalizar registro</button>
        @endif
    </div>
</form>

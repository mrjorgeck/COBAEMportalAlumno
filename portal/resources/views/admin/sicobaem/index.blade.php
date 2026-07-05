@extends('layouts.admin')

@section('titulo', 'SICOBaEM')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">SICOBaEM por ciclo</h1>

    <div class="mt-4 space-y-4">
        @foreach ($ciclos as $ciclo)
            @php $config = $configs->get($ciclo->id); @endphp
            <form method="POST" action="{{ route('admin.sicobaem.store') }}" class="rounded bg-white p-4 shadow-sm">
                @csrf
                <input type="hidden" name="ciclo_ingreso_id" value="{{ $ciclo->id }}">
                <h2 class="font-semibold">{{ $ciclo->generacion }}</h2>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <input name="url" value="{{ old('url', $config?->url) }}" placeholder="https://sicobaem..." class="rounded border-gray-300">
                    <input name="fecha_disponibilidad" inputmode="numeric" pattern="\d{2}/\d{2}/\d{4}" placeholder="dd/mm/aaaa" value="{{ old('fecha_disponibilidad', \App\Support\FechaInput::toDisplay($config?->fecha_disponibilidad)) }}" class="rounded border-gray-300">
                    <input name="contacto_soporte" value="{{ old('contacto_soporte', $config?->contacto_soporte) }}" placeholder="Contacto de soporte" class="rounded border-gray-300 md:col-span-2">
                    <textarea name="mensaje" placeholder="Mensaje para alumnos" class="rounded border-gray-300 md:col-span-2">{{ old('mensaje', $config?->mensaje ?? 'Tu acceso a SICOBaEM estara disponible cuando control escolar publique tu matricula.') }}</textarea>
                    <textarea name="pasos_activacion" placeholder="Pasos de activacion" class="rounded border-gray-300 md:col-span-2">{{ old('pasos_activacion', $config?->pasos_activacion ?? "1. Ingresa al portal SICOBaEM.\n2. Usa tu matricula como usuario, si asi se indica.\n3. Cambia tu contrasena inicial y conserva tus datos de acceso.") }}</textarea>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="activo" value="1" @checked($config?->activo ?? true)> Activo</label>
                </div>
                <button class="mt-3 rounded bg-cobaem-900 px-4 py-2 text-white">Guardar</button>
            </form>
        @endforeach
    </div>
@endsection

@extends('layouts.admin')

@section('titulo', 'Alumnos')

@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Alumnos</h1>
    <form method="GET" class="mt-4 flex gap-2">
        <input name="buscar" value="{{ $buscar }}" placeholder="CURP, nombre o folio" class="w-full rounded border-gray-300">
        <button class="rounded bg-cobaem-900 px-4 py-2 text-white">Buscar</button>
    </form>
    <div class="mt-4 overflow-x-auto rounded bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-100 text-left"><th class="p-2">Alumno</th><th>CURP</th><th>Folio</th><th>Estatus</th><th></th></tr></thead>
            <tbody>
            @foreach ($procesos as $proceso)
                <tr class="border-t">
                    <td class="p-2">{{ $proceso->alumno->nombre_completo }}</td>
                    <td>{{ $proceso->alumno->curp }}</td>
                    <td>{{ $proceso->folio_registro }}<br>{{ $proceso->folio_examen }}</td>
                    <td>{{ $proceso->estatus_proceso }}</td>
                    <td><a class="font-semibold text-cobaem-900" href="{{ route('admin.alumnos.show', $proceso) }}">Ver</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $procesos->links() }}</div>
@endsection

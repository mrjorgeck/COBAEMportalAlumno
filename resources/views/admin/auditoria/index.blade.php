@extends('layouts.admin')
@section('titulo', 'Auditoria')
@section('contenido')
    <h1 class="text-xl font-bold text-cobaem-900">Auditoria</h1>
    <div class="mt-4 overflow-x-auto rounded bg-white shadow-sm">
        <table class="w-full text-sm">
            <thead><tr class="bg-gray-100 text-left"><th class="p-2">Fecha</th><th>Log</th><th>Descripcion</th><th>Modelo</th></tr></thead>
            <tbody>
            @foreach ($eventos as $evento)
                <tr class="border-t"><td class="p-2">{{ $evento->created_at }}</td><td>{{ $evento->log_name }}</td><td>{{ $evento->description }}</td><td>{{ $evento->subject_type }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

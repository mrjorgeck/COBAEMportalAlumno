@extends('layouts.admin')
@section('titulo', 'Avisos')
@section('contenido')
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-cobaem-900">Avisos</h1>
        <a class="rounded bg-cobaem-900 px-4 py-2 text-white" href="{{ route('admin.avisos.create') }}">Nuevo aviso</a>
    </div>
    <div class="mt-4 space-y-3">
        @foreach ($avisos as $aviso)
            <article class="rounded bg-white p-4 shadow-sm">
                <p class="font-semibold">{{ $aviso->titulo }}</p>
                <p class="text-sm text-gray-600">{{ $aviso->prioridad }} - {{ $aviso->dirigido_a }}</p>
                <div class="mt-3 flex gap-3 text-sm">
                    <a class="font-semibold text-cobaem-900" href="{{ route('admin.avisos.edit', $aviso) }}">Editar</a>
                    <form method="POST" action="{{ route('admin.avisos.destroy', $aviso) }}">@csrf @method('DELETE')<button>Eliminar</button></form>
                </div>
            </article>
        @endforeach
    </div>
@endsection

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('titulo', 'Panel') - Portal Nuevo Ingreso COBAEM</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-100 text-gray-900 antialiased">
<div class="flex min-h-screen">
    <aside class="hidden w-60 shrink-0 bg-cobaem-900 text-white md:block">
        <div class="border-b border-cobaem-700 px-4 py-5">
            <p class="font-bold">Portal Nuevo Ingreso</p>
            <p class="text-xs text-cobaem-100">Panel administrativo</p>
        </div>
        <nav class="space-y-1 p-3 text-sm">
            @can('dashboard.registros')
                <a href="{{ route('admin.dashboard') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Dashboard</a>
            @endcan
            @can('dashboard.academico')
                <a href="{{ route('admin.dashboard-academico') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Dashboard academico</a>
            @endcan
            @can('alumnos.ver')
                <a href="{{ route('admin.alumnos.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Alumnos</a>
            @endcan
            @can('csv.exportar')
                <a href="{{ route('admin.exportaciones') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Exportaciones</a>
            @endcan
            @can('csv.importar')
                <a href="{{ route('admin.importaciones') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Importaciones</a>
            @endcan
            @can('resultados.cargar')
                <a href="{{ route('admin.examenes.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Examenes</a>
                <a href="{{ route('admin.materiales.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Materiales</a>
            @endcan
            @can('grupos.asignar')
                <a href="{{ route('admin.grupos-propedeuticos.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Propedeutico</a>
                <a href="{{ route('admin.grupos-escolares.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Grupos escolares</a>
            @endcan
            @can('avisos.publicar')
                <a href="{{ route('admin.avisos.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Avisos</a>
            @endcan
            @can('modulos.publicar')
                <a href="{{ route('admin.modulos.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Modulos</a>
                <a href="{{ route('admin.sicobaem.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">SICOBaEM</a>
            @endcan
            @can('catalogos.administrar')
                <a href="{{ route('admin.catalogos.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Catalogos</a>
            @endcan
            @can('usuarios.administrar')
                <a href="{{ route('admin.auditoria.index') }}" class="block rounded px-3 py-2 hover:bg-cobaem-700">Auditoria</a>
            @endcan
        </nav>
    </aside>

    <div class="flex flex-1 flex-col">
        <header class="flex items-center justify-between bg-white px-4 py-3 shadow-sm">
            <span class="text-sm text-gray-600">
                Ciclo: <strong>{{ \App\Models\CicloIngreso::vigente()?->generacion ?? 'sin ciclo activo' }}</strong>
            </span>
            <div class="flex items-center gap-3 text-sm">
                <span>{{ auth()->user()?->name }}</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="rounded bg-gray-200 px-3 py-1 hover:bg-gray-300">Salir</button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 md:p-6">
            <x-flash :message="session('mensaje')" />
            @yield('contenido')
        </main>
    </div>
</div>
@livewireScripts
</body>
</html>

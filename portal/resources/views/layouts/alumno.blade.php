<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('titulo', 'Portal de Nuevo Ingreso') · COBAEM Ario de Rosales</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 antialiased">

    <header class="bg-cobaem-900 text-white">
        <div class="mx-auto max-w-lg px-4 py-3 flex items-center gap-3">
            {{-- Logo institucional: colocar en public/img/logo-cobaem.png --}}
            <a href="{{ route('alumno.landing') }}" class="flex items-center gap-3">
                <span class="text-lg font-bold leading-tight">
                    Portal de Nuevo Ingreso
                    <span class="block text-xs font-normal text-cobaem-100">
                        COBAEM · Plantel Ario de Rosales
                    </span>
                </span>
            </a>
        </div>
    </header>

    <main class="mx-auto max-w-lg px-4 py-6">
        <x-flash :message="session('mensaje')" />

        @yield('contenido')
    </main>

    <footer class="mx-auto max-w-lg px-4 py-6 text-center text-xs text-gray-500">
        <p>Colegio de Bachilleres del Estado de Michoacán · Plantel Ario de Rosales</p>
        <p class="mt-1">
            <a href="{{ route('alumno.privacidad') }}" class="underline">Aviso de privacidad</a>
        </p>
    </footer>

    @livewireScripts
</body>
</html>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Iniciar sesión · Portal Nuevo Ingreso COBAEM</title>
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center bg-cobaem-900 px-4">
    <div class="w-full max-w-sm rounded-xl bg-white p-6 shadow-lg">
        <h1 class="text-center text-lg font-bold text-cobaem-900">
            Panel administrativo
            <span class="block text-xs font-normal text-gray-500">
                Portal de Nuevo Ingreso · COBAEM Ario
            </span>
        </h1>

        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium">Correo electrónico</label>
                <input id="email" name="email" type="email" required autofocus
                       value="{{ old('email') }}"
                       class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-cobaem-500 focus:ring-cobaem-500">
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium">Contraseña</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 w-full rounded-lg border-gray-300 text-sm focus:border-cobaem-500 focus:ring-cobaem-500">
            </div>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" class="rounded border-gray-300">
                Mantener sesión iniciada
            </label>

            <button class="w-full rounded-lg bg-cobaem-700 py-2 text-sm font-medium text-white hover:bg-cobaem-500">
                Entrar
            </button>
        </form>
    </div>
</body>
</html>

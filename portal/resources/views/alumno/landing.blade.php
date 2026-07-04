@extends('layouts.alumno')

@section('titulo', 'Inicio')

@section('contenido')
    <div class="rounded-xl bg-white p-6 shadow">
        <h1 class="text-xl font-bold text-cobaem-900">Bienvenido(a)</h1>
        <p class="mt-2 text-sm text-gray-600">
            Registra tus datos, descarga tu formato de inscripcion y consulta el avance de tu proceso.
        </p>

        <form method="POST" action="{{ route('alumno.acceso') }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700" for="curp">CURP</label>
                <input id="curp" name="curp" maxlength="18" required value="{{ old('curp') }}"
                       class="mt-1 w-full rounded border-gray-300 uppercase shadow-sm focus:border-cobaem-700 focus:ring-cobaem-700">
                @error('curp') <p class="mt-1 text-sm text-red-700">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-start gap-2 text-sm text-gray-600">
                <input type="checkbox" id="recordar_curp" class="mt-1 rounded border-gray-300">
                Recordar mi CURP en este navegador con mi consentimiento.
            </label>

            <button class="w-full rounded bg-cobaem-900 px-4 py-2 font-semibold text-white hover:bg-cobaem-800">
                Continuar
            </button>
        </form>
    </div>

    <script>
        window.curpStorage = {
            key: 'portal_cobaem_curp',
            get() { return localStorage.getItem(this.key) || ''; },
            set(value) { localStorage.setItem(this.key, value); },
            forget() { localStorage.removeItem(this.key); },
        };
        const input = document.getElementById('curp');
        const checkbox = document.getElementById('recordar_curp');
        input.value = input.value || window.curpStorage.get();
        input.form.addEventListener('submit', () => {
            if (checkbox.checked) window.curpStorage.set(input.value.toUpperCase());
        });
    </script>
@endsection

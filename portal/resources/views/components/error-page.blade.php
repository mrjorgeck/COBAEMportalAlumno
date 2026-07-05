@props([
    'code',
    'title',
    'message',
    'action' => 'Volver al inicio',
    'href' => null,
])

<section class="rounded bg-white p-6 text-center shadow-sm">
    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-cobaem-100 text-xl font-bold text-cobaem-900">
        {{ $code }}
    </div>
    <h1 class="mt-4 text-2xl font-bold text-cobaem-900">{{ $title }}</h1>
    <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-gray-700">{{ $message }}</p>
    <a href="{{ $href ?? route('alumno.landing') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded bg-cobaem-900 px-4 py-2 text-sm font-semibold text-white">
        {{ $action }}
    </a>
</section>

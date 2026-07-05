@props([
    'message' => null,
])

@if ($message)
    <div
        x-data="{ show: true }"
        x-init="setTimeout(() => show = false, 6000)"
        x-show="show"
        x-transition.opacity
        role="status"
        class="mb-4 flex items-start justify-between gap-3 rounded bg-cobaem-100 px-4 py-3 text-sm text-cobaem-900"
    >
        <span>{{ $message }}</span>
        <button type="button" x-on:click="show = false" class="font-semibold underline">Cerrar</button>
    </div>
@endif

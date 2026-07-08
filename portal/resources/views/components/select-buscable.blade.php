@props([
    'label' => 'Filtrar opciones',
    'placeholder' => 'Filtrar opciones',
])

<div
    x-data="{
        query: '',
        normalize(value) {
            return value.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
        },
        filter() {
            const needle = this.normalize(this.query);
            const select = this.$refs.selectWrap.querySelector('select');

            if (! select) {
                return;
            }

            Array.from(select.options).forEach((option, index) => {
                option.hidden = index > 0 && ! this.normalize(option.textContent).includes(needle);
            });
        },
    }"
    class="space-y-2"
>
    <span class="block text-xs font-medium text-gray-600">{{ $label }}</span>
    <input
        type="search"
        x-model="query"
        x-on:input.debounce.150ms="filter()"
        x-on:search="filter()"
        autocomplete="off"
        aria-label="{{ $label }}"
        placeholder="{{ $placeholder }}"
        class="min-h-10 w-full rounded border-gray-300 bg-gray-50 text-sm"
    >
    <div x-ref="selectWrap">
        {{ $slot }}
    </div>
</div>

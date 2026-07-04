@props(['required' => false])

@if ($required)
    <span aria-hidden="true" class="text-red-600">*</span>
@else
    <span class="font-normal text-gray-500">(opcional)</span>
@endif

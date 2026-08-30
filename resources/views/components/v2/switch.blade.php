@php
    $id = $id ?? '';
    $checked = $checked ?? false;
    $label = $label ?? '';
    $labelId = $labelId ?? ($id . '_status');
    $disabled = $disabled ?? false;
@endphp

<label class="flex cursor-pointer items-center gap-3" for="{{ $id }}">
    <span class="relative inline-flex h-5 w-9 shrink-0">
        <input type="checkbox"
               id="{{ $id }}"
               class="peer sr-only"
               @if($checked) checked @endif
               @if($disabled) disabled @endif>
        <span class="absolute inset-0 rounded-full bg-gray-300 transition peer-checked:bg-brand peer-disabled:opacity-50"></span>
        <span class="absolute left-0.5 top-0.5 h-4 w-4 rounded-full bg-white shadow transition peer-checked:translate-x-4"></span>
    </span>
    @if($label)
        <span id="{{ $labelId }}" class="text-sm text-gray-700">{{ $label }}</span>
    @endif
</label>

@php
    $href = $href ?? '#';
    $label = $label ?? '';
    $description = $description ?? null;
    $target = $target ?? null;
    $disabled = $disabled ?? false;
    $variant = $variant ?? 'default';

    $variants = [
        'default' => 'border-gray-200 bg-white text-gray-800 hover:border-brand/40 hover:bg-brand-surface',
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900 hover:border-emerald-300',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900 hover:border-amber-300',
        'primary' => 'border-brand/30 bg-brand-surface text-brand-dark hover:border-brand/50',
    ];
    $className = 'flex flex-col items-center justify-center rounded-xl border p-4 text-center text-sm font-medium transition ' . ($variants[$variant] ?? $variants['default']);
    if ($disabled) {
        $className .= ' pointer-events-none opacity-50';
    }
@endphp

@if($disabled)
    <div class="{{ $className }}">
        @if(!empty($icon))
            <span class="mb-2 inline-flex h-8 w-8 items-center justify-center text-brand">{!! $icon !!}</span>
        @endif
        <span>{{ $label }}</span>
        @if($description)
            <span class="mt-1 text-xs font-normal text-gray-500">{{ $description }}</span>
        @endif
    </div>
@else
    <a href="{{ $href }}"
       class="{{ $className }}"
       @if($target) target="{{ $target }}" rel="noopener noreferrer" @endif>
        @if(!empty($icon))
            <span class="mb-2 inline-flex h-8 w-8 items-center justify-center text-brand">{!! $icon !!}</span>
        @endif
        <span>{{ $label }}</span>
        @if($description)
            <span class="mt-1 text-xs font-normal text-gray-500">{{ $description }}</span>
        @endif
    </a>
@endif

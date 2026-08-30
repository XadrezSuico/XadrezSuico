@php
    $variant = $variant ?? 'default';
    $size = $size ?? 'md';

    $variants = [
        'default' => 'border border-gray-200 bg-white text-gray-700 hover:bg-gray-50',
        'primary' => 'bg-brand text-white hover:bg-brand-dark',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600',
        'secondary' => 'border border-brand/30 bg-white text-brand hover:bg-brand-surface',
    ];

    $sizes = [
        'sm' => 'rounded-md px-2 py-1 text-[0.7rem] font-medium gap-1',
        'md' => 'rounded-lg px-3 py-2 text-sm font-medium gap-1.5',
    ];

    $className = 'group relative inline-flex items-center transition ' . ($sizes[$size] ?? $sizes['md']) . ' ' . ($variants[$variant] ?? $variants['default']);

    if (!empty($block)) {
        $className .= ' w-full justify-center';
    }
@endphp

<a href="{{ $href ?? '#' }}"
   @if(!empty($target)) target="{{ $target }}" @endif
   @if(!empty($target)) rel="noopener noreferrer" @endif
   class="{{ $className }} {{ $class ?? '' }}"
   @if(!empty($tooltip)) title="{{ $tooltip }}" @endif
   role="button">
    @if(!empty($icon))
        <span class="inline-flex shrink-0 {{ $size === 'sm' ? 'h-3.5 w-3.5' : 'h-4 w-4' }}">{!! $icon !!}</span>
    @endif
    @if(!empty($label))
        <span>{{ $label }}</span>
    @endif
    @if(!empty($tooltip))
        <span class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1.5 hidden -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-[10px] font-normal text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100 sm:block">
            {{ $tooltip }}
            <span class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-gray-900"></span>
        </span>
    @endif
</a>

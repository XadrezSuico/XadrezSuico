@php
    $variant = $variant ?? 'default';
    $classes = [
        'default' => 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50',
        'primary' => 'bg-brand text-white hover:bg-brand-dark',
        'success' => 'bg-emerald-600 text-white hover:bg-emerald-700',
        'warning' => 'bg-amber-500 text-white hover:bg-amber-600',
        'secondary' => 'border border-brand bg-white text-brand hover:bg-brand-surface',
    ];
    $className = 'inline-flex items-center rounded-lg px-3 py-2 text-sm font-medium transition ' . ($classes[$variant] ?? $classes['default']);
    if (!empty($block)) {
        $className .= ' w-full justify-center';
    }
@endphp
<a href="{{ $href ?? '#' }}"
   @if(!empty($target)) target="{{ $target }}" @endif
   class="{{ $className }} {{ $class ?? '' }}"
   role="button">
    {{ $label }}
</a>

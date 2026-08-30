@php
    $tones = [
        'brand' => 'bg-brand-surface text-brand-dark ring-brand/25',
        'green' => 'bg-emerald-50 text-emerald-800 ring-emerald-200',
        'amber' => 'bg-amber-50 text-amber-800 ring-amber-200',
        'red' => 'bg-red-50 text-red-800 ring-red-200',
        'blue' => 'bg-sky-50 text-sky-800 ring-sky-200',
        'violet' => 'bg-violet-50 text-violet-800 ring-violet-200',
        'gray' => 'bg-gray-100 text-gray-700 ring-gray-200',
    ];
    $toneClass = $tones[$tone ?? 'gray'] ?? $tones['gray'];
@endphp

<span class="group relative inline-flex cursor-default items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {{ $toneClass }}">
    @if(!empty($icon))
        <span class="inline-flex h-3.5 w-3.5 shrink-0">{!! $icon !!}</span>
    @endif
    <span>{{ $value }}</span>
    @if(!empty($tooltip))
        <span class="pointer-events-none absolute bottom-full left-1/2 z-10 mb-1.5 -translate-x-1/2 whitespace-nowrap rounded-md bg-gray-900 px-2 py-1 text-[10px] font-normal text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100">
            {{ $tooltip }}
            <span class="absolute left-1/2 top-full -translate-x-1/2 border-4 border-transparent border-t-gray-900"></span>
        </span>
    @endif
</span>

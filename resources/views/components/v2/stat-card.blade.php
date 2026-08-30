@php
    $tone = $tone ?? 'brand';
    $tones = [
        'brand' => 'from-brand to-brand-light',
        'green' => 'from-emerald-600 to-emerald-500',
        'aqua' => 'from-cyan-600 to-cyan-500',
    ];
    $gradient = $tones[$tone] ?? $tones['brand'];
@endphp
<div class="overflow-hidden rounded-xl bg-gradient-to-br {{ $gradient }} p-6 text-white shadow-lg">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-4xl font-bold">{{ $value }}</p>
            <p class="mt-2 text-sm text-white/90">{{ $label }}</p>
        </div>
        @if(!empty($icon))
            <div class="text-white/30">
                @include('layouts.v2.partials.icon', ['name' => $icon])
            </div>
        @endif
    </div>
</div>

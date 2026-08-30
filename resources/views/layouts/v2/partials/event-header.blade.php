@php
    $backUrl = null;
    $backLabel = null;

    if ($evento->parent_event) {
        $backUrl = url('/evento/dashboard/' . $evento->parent_event->id . '?tab=evento_filho');
        $backLabel = 'Voltar ao evento pai';
    } else {
        $backUrl = url('/grupoevento/dashboard/' . $evento->grupo_evento->id);
        $backLabel = 'Voltar ao grupo de evento';
    }
@endphp

<div class="mb-4 flex flex-wrap items-start justify-between gap-3">
    <div>
        @if($evento->parent_event)
            <p class="text-sm text-gray-500">Filho de: {{ $evento->parent_event->name }}</p>
        @endif
        <h1 class="text-2xl font-semibold text-brand-dark">{{ $evento->name }}</h1>
        <p class="text-sm text-gray-500">Evento #{{ $evento->id }} · {{ $evento->grupo_evento->name }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @include('components.v2.btn', [
            'href' => $backUrl,
            'label' => $backLabel,
            'variant' => 'secondary',
            'size' => 'sm',
        ])
        @include('components.v2.btn', [
            'href' => url('/evento/dashboard-legacy/' . $evento->id),
            'label' => 'Layout antigo',
            'variant' => 'default',
            'size' => 'sm',
        ])
    </div>
</div>

@php
    use App\Support\EventDashboardTabs;

    $resolvedBackUrl = $backUrl ?? null;
    $resolvedBackLabel = $backLabel ?? null;

    if (!$resolvedBackUrl) {
        if ($evento->parent_event && !empty($isEventDashboard)) {
            $resolvedBackUrl = url('/evento/dashboard/' . $evento->parent_event->id . '?tab=evento_filho');
            $resolvedBackLabel = 'Voltar ao evento pai';
        } elseif (!empty($isEventDashboard)) {
            $resolvedBackUrl = url('/grupoevento/dashboard/' . $evento->grupo_evento->id);
            $resolvedBackLabel = 'Voltar ao grupo de evento';
        } elseif ($fromTab = request('from_tab')) {
            $resolvedBackUrl = url('/evento/dashboard/' . $evento->id . '?tab=' . urlencode($fromTab));
            $resolvedBackLabel = 'Voltar à aba ' . EventDashboardTabs::labelFor($fromTab);
        } else {
            $resolvedBackUrl = url('/evento/dashboard/' . $evento->id);
            $resolvedBackLabel = 'Voltar ao evento';
        }
    }

    if (!$resolvedBackLabel) {
        $resolvedBackLabel = 'Voltar';
    }
@endphp

<div class="v2-event-shell mb-4 flex flex-wrap items-start justify-between gap-3">
    <div>
        @if($evento->parent_event)
            <p class="text-sm text-gray-500">Filho de: {{ $evento->parent_event->name }}</p>
        @endif
        <h1 class="text-2xl font-semibold text-brand-dark">{{ $evento->name }}</h1>
        <p class="text-sm text-gray-500">Evento #{{ $evento->id }} · {{ $evento->grupo_evento->name }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @include('components.v2.btn', [
            'href' => $resolvedBackUrl,
            'label' => $resolvedBackLabel,
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

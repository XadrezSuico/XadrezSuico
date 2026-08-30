@php
    $alerts = $dashboard_alerts ?? [];
    $compact = $compact ?? false;
    $visible_limit = $compact ? 2 : count($alerts);
    $visible_alerts = array_slice($alerts, 0, $visible_limit);
    $hidden_alerts = array_slice($alerts, $visible_limit);

    $levelStyles = [
        'success' => 'border-emerald-200 bg-emerald-50 text-emerald-900',
        'info' => 'border-sky-200 bg-sky-50 text-sky-900',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-900',
        'danger' => 'border-red-200 bg-red-50 text-red-900',
    ];
@endphp

@if(count($alerts) > 0)
    <div class="{{ $compact ? 'mt-4' : 'mb-6' }} space-y-2">
        @foreach($visible_alerts as $alert)
            <div class="rounded-lg border px-3 py-2 text-sm {{ $levelStyles[$alert['level']] ?? $levelStyles['info'] }}">
                {{ $alert['message'] }}
                @if(!empty($alert['action_url']))
                    <a href="{{ $alert['action_url'] }}" class="ml-1 font-medium underline">{{ $alert['action_label'] ?? 'Ver' }} →</a>
                @endif
            </div>
        @endforeach

        @if($compact && count($hidden_alerts) > 0)
            <details class="text-sm">
                <summary class="cursor-pointer text-brand hover:text-brand-dark">
                    Ver todos os alertas ({{ count($alerts) }})
                </summary>
                <div class="mt-2 space-y-2">
                    @foreach($hidden_alerts as $alert)
                        <div class="rounded-lg border px-3 py-2 text-sm {{ $levelStyles[$alert['level']] ?? $levelStyles['info'] }}">
                            {{ $alert['message'] }}
                            @if(!empty($alert['action_url']))
                                <a href="{{ $alert['action_url'] }}" class="ml-1 font-medium underline">{{ $alert['action_label'] ?? 'Ver' }} →</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </details>
        @endif
    </div>
@endif

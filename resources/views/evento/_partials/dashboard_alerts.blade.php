@php
    $alerts = $dashboard_alerts ?? [];
    $compact = $compact ?? false;
    $visible_limit = $compact ? 2 : count($alerts);
    $visible_alerts = array_slice($alerts, 0, $visible_limit);
    $hidden_alerts = array_slice($alerts, $visible_limit);
@endphp

@if(count($alerts) > 0)
    <div class="dashboard-alerts {{ $compact ? 'dashboard-alerts-compact' : '' }}">
        @foreach($visible_alerts as $alert)
            <div class="alert alert-{{ $alert['level'] }} dashboard-alert-item" style="margin-bottom: {{ $compact ? '8px' : '10px' }};">
                <i class="fa fa-exclamation-circle"></i>
                {{ $alert['message'] }}
                @if(!empty($alert['action_url']))
                    <a href="{{ $alert['action_url'] }}" class="alert-link">{{ $alert['action_label'] ?? 'Ver' }} &rarr;</a>
                @endif
            </div>
        @endforeach

        @if($compact && count($hidden_alerts) > 0)
            <div class="dashboard-alerts-more">
                <a class="btn btn-default btn-xs" data-toggle="collapse" href="#dashboard_alerts_all_{{ $alert_collapse_id ?? 'strip' }}" aria-expanded="false">
                    Ver todos os alertas ({{ count($alerts) }})
                </a>
                <div class="collapse" id="dashboard_alerts_all_{{ $alert_collapse_id ?? 'strip' }}">
                    @foreach($hidden_alerts as $alert)
                        <div class="alert alert-{{ $alert['level'] }} dashboard-alert-item" style="margin-top: 8px; margin-bottom: 0;">
                            <i class="fa fa-exclamation-circle"></i>
                            {{ $alert['message'] }}
                            @if(!empty($alert['action_url']))
                                <a href="{{ $alert['action_url'] }}" class="alert-link">{{ $alert['action_label'] ?? 'Ver' }} &rarr;</a>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endif

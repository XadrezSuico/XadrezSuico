@php
    $totals = $dashboard_stats['totals'];
    $registration_status = $evento->getRegistrationStatus();
    $status_label_class = [
        'success' => 'label-success',
        'warning' => 'label-warning',
        'danger' => 'label-danger',
        'default' => 'label-default',
    ][$registration_status['level']] ?? 'label-default';
@endphp

<div class="box box-solid event-overview-strip" style="margin-bottom: 15px;">
    <div class="box-body" style="padding: 12px 15px;">
        <div class="row">
            <div class="col-md-4 col-sm-12 event-overview-meta">
                <p class="event-overview-period" style="margin: 0 0 6px;">
                    <i class="fa fa-calendar text-muted"></i>
                    <strong>Período:</strong>
                    {{ $evento->getDataInicio() ?: '—' }}
                    <i class="fa fa-long-arrow-right text-muted"></i>
                    {{ $evento->getDataFim() ?: '—' }}
                </p>
                <p style="margin: 0;">
                    <i class="fa fa-pencil-square-o text-muted"></i>
                    <strong>Inscrições:</strong>
                    <span class="label {{ $status_label_class }}">{{ $registration_status['label'] }}</span>
                </p>
            </div>

            <div class="col-md-5 col-sm-12 event-overview-kpis">
                <div class="event-overview-kpi-row">
                    <span class="event-overview-kpi">
                        <span class="event-overview-kpi-value">{{ $totals['inscritos'] }}</span>
                        <span class="event-overview-kpi-label">Inscritos</span>
                    </span>
                    <span class="event-overview-kpi-sep">·</span>
                    <span class="event-overview-kpi">
                        <span class="event-overview-kpi-value">{{ $totals['confirmados'] }}</span>
                        <span class="event-overview-kpi-label">Confirmados</span>
                    </span>
                    <span class="event-overview-kpi-sep">·</span>
                    <span class="event-overview-kpi">
                        <span class="event-overview-kpi-value">{{ $totals['presentes'] }}</span>
                        <span class="event-overview-kpi-label">Presentes</span>
                    </span>
                </div>

                @if($dashboard_stats['limits'])
                    @php $limits = $dashboard_stats['limits']; @endphp
                    <div class="event-overview-limit" style="margin-top: 8px;">
                        <span class="text-muted" style="font-size: 12px;">
                            Limite: {{ $limits['total'] }}/{{ $limits['limit'] }} ({{ $limits['pct'] }}%)
                        </span>
                        <div class="progress progress-xs" style="margin: 4px 0 0;">
                            <div class="progress-bar progress-bar-{{ $limits['pct'] >= 100 ? 'red' : ($limits['pct'] >= 85 ? 'yellow' : 'green') }}"
                                 style="width: {{ $limits['pct'] }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-md-3 col-sm-12 text-right event-overview-link">
                <a href="{{ url('/evento/dashboard/' . $evento->id) }}?tab=resume" class="btn btn-default btn-sm">
                    Resumo completo <i class="fa fa-arrow-right"></i>
                </a>
            </div>
        </div>

        @include('evento._partials.dashboard_alerts', [
            'compact' => true,
            'alert_collapse_id' => 'strip',
        ])
    </div>
</div>

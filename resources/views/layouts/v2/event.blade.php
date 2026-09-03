@extends('layouts.v2.app')

@section('title', 'Dashboard de Evento')

@section('content')
    <div id="v2-event-chrome">
        @include('layouts.v2.partials.event-header', [
            'evento' => $evento,
            'isEventDashboard' => true,
        ])

        @if(!empty($show_kpi_strip))
            @include('components.v2.kpi-strip', [
                'evento' => $evento,
                'dashboard_stats' => $dashboard_stats,
                'dashboard_alerts' => $dashboard_alerts,
            ])
        @endif

        @include('layouts.v2.partials.event-tabs', ['event_tabs' => $event_tabs ?? []])
    </div>

    @yield('event-content')
@endsection

@extends('layouts.v2.app')

@section('title', 'Dashboard de Evento')

@section('content')
    @include('layouts.v2.partials.event-header', ['evento' => $evento])

    @if(!empty($show_kpi_strip))
        @include('components.v2.kpi-strip', [
            'evento' => $evento,
            'dashboard_stats' => $dashboard_stats,
            'dashboard_alerts' => $dashboard_alerts,
        ])
    @endif

    @include('layouts.v2.partials.event-tabs', ['event_tabs' => $event_tabs ?? []])

    @yield('event-content')
@endsection

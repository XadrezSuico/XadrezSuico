@extends('layouts.v2.app')

@section('title', 'Dashboard de Evento')

@push('styles')
    @include('layouts.v2.partials.event-legacy-plugins-styles')
    <style>
        .v2-legacy-tab .box { margin-bottom: 1rem; }
        .v2-legacy-tab .btn-app { margin: 0.25rem; }
        .v2-legacy-tab .form-control { max-width: 100%; }
    </style>
@endpush

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

@push('scripts')
    @include('layouts.v2.partials.event-legacy-plugins-scripts')
@endpush

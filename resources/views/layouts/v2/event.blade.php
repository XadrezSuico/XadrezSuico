@extends('layouts.v2.app')

@section('title', 'Dashboard de Evento')

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
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
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/vendor/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/input-mask/jquery.inputmask.bundle.min.js') }}"></script>
@endpush

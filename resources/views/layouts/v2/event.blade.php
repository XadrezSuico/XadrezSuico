@extends('layouts.v2.app')

@section('title', 'Dashboard de Evento')

@php
    use App\Support\EventDashboardTabs;

    $isLegacyTab = in_array($tab ?: 'funcoes', EventDashboardTabs::legacyTabIds(), true);
@endphp

@push('styles-before-v2')
    @if($isLegacyTab)
        @include('layouts.v2.partials.event-legacy-plugins-styles')
        @include('layouts.v2.partials.event-shell-protect-styles')
    @endif
    @include('layouts.v2.partials.event-v2-native-styles')
    @include('layouts.v2.partials.event-legacy-scoped-styles')
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if($isLegacyTab)
        @include('layouts.v2.partials.event-legacy-plugins-scripts')
    @endif
@endpush

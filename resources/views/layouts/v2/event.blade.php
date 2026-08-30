@extends('layouts.v2.app')

@section('title', 'Dashboard de Evento')

@php
    $legacyTabIds = [
        'editar_evento', 'pagina', 'timeline', 'criterio_desempate', 'premiacao_equipe',
        'categoria', 'categorias_relacionadas', 'evento_filho', 'torneio',
        'campo_personalizado', 'email_template', 'classificator',
    ];
    $isLegacyTab = in_array($tab ?: 'funcoes', $legacyTabIds, true);
@endphp

@push('styles')
    @if($isLegacyTab)
        @include('layouts.v2.partials.event-legacy-plugins-styles')
    @endif
    @include('layouts.v2.partials.event-v2-native-styles')
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if($isLegacyTab)
        @include('layouts.v2.partials.event-legacy-plugins-scripts')
    @endif
@endpush

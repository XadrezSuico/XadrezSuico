@extends('layouts.v2.event')

@php
    use App\Support\EventDashboardTabs;

    $activeTab = $tab ?: 'funcoes';
    $isLegacyTab = in_array($activeTab, EventDashboardTabs::legacyTabIds(), true);
    $v2Tabs = [
        'funcoes' => 'evento.v2._tabs.funcoes',
        'resume' => 'evento.v2._tabs.resume',
    ];
@endphp

@push('styles-after-v2')
    <link rel="stylesheet" href="{{ asset('css/v2/registrations.css') }}?v={{ filemtime(public_path('css/v2/registrations.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/v2/dashboard-tables.css') }}?v={{ filemtime(public_path('css/v2/dashboard-tables.css')) }}">
    @if($isLegacyTab)
        @include('layouts.v2.partials.event-legacy-plugins-styles')
        @include('layouts.v2.partials.event-legacy-scoped-styles')
    @else
        @include('layouts.v2.partials.event-v2-native-styles')
    @endif
@endpush

@section('event-content')
    <div class="v2-dashboard-tables v2-legacy-content" data-enhance-tables="true">
    @if($isLegacyTab)
        <div class="v2-legacy-tab">
            <div class="row">
                <div class="col-xs-12">
                    @include('evento._tabs.' . $activeTab)
                </div>
            </div>
        </div>
    @else
        <div class="v2-event-native">
            @include(isset($v2Tabs[$activeTab]) ? $v2Tabs[$activeTab] : 'evento.v2._tabs.funcoes')
        </div>
    @endif
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if($isLegacyTab)
        @include('layouts.v2.partials.event-legacy-plugins-scripts')
    @endif
    @if(!$isLegacyTab)
        <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    @endif
    @include('evento.v2._scripts.dashboard', compact('evento', 'tab', 'categorias', 'criterios_desempate', 'tipos_torneio', 'softwares', 'tipos_rating', 'xadrezsuicopag_controller'))
@endpush

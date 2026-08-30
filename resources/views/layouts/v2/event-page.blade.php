@extends('layouts.v2.app')

@section('title', $pageTitle ?? 'Evento')

@push('styles')
    @include('layouts.v2.partials.event-legacy-plugins-styles')
    @include('layouts.v2.partials.event-v2-native-styles')
    <style>
        .v2-legacy-content .box { margin-bottom: 1rem; }
        .v2-legacy-content .form-control { max-width: 100%; }
    </style>
@endpush

@section('content')
    @if(!empty($evento))
        @include('layouts.v2.partials.event-header', ['evento' => $evento])
    @endif

    @if(!empty($pageTitle))
        <h2 class="mb-4 text-lg font-semibold text-brand-dark">{{ $pageTitle }}</h2>
    @endif

    <div class="v2-legacy-content">
        @yield('event-page-content')
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('layouts.v2.partials.event-legacy-plugins-scripts')
    @stack('event-scripts')
@endpush

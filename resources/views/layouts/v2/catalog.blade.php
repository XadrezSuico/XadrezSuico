@extends('layouts.v2.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/font-awesome/css/font-awesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/v2/registrations.css') }}?v={{ filemtime(public_path('css/v2/registrations.css')) }}">
<link rel="stylesheet" href="{{ asset('css/v2/dashboard-tables.css') }}?v={{ filemtime(public_path('css/v2/dashboard-tables.css')) }}">
<link rel="stylesheet" href="{{ asset('css/v2/catalog.css') }}?v={{ filemtime(public_path('css/v2/catalog.css')) }}">
@endpush

@section('content')
<div class="v2-catalog v2-dashboard-tables" data-enhance-tables="true">
    @if(session('status'))<div class="v2-catalog-notice" role="status">{{ session('status') }}</div>@endif
    @yield('catalog-content')
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('layouts.v2.partials.dashboard-table-scripts')
<script src="{{ asset('js/v2/catalog.js') }}?v={{ filemtime(public_path('js/v2/catalog.js')) }}"></script>
@endpush

@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.inscricao.index')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/v2/registrations.css') }}?v={{ filemtime(public_path('css/v2/registrations.css')) }}">
@endpush

@push('event-scripts')
@include('evento.torneio.inscricao.delete-paid-script')
<script>
    jQuery(function () { window.initV2RegistrationsTable('#tabela', {}); });
</script>
@endpush

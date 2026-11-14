@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.inscricao.index')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/v2/registrations.css') }}?v={{ filemtime(public_path('css/v2/registrations.css')) }}">
@endpush

@push('event-scripts')
@include('evento.torneio.inscricao.delete-paid-script')
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script src="{{ asset('js/v2/registrations.js') }}?v={{ filemtime(public_path('js/v2/registrations.js')) }}"></script>
<script>
    jQuery(function () { window.initV2RegistrationsTable('#tabela', {}); });
</script>
@endpush

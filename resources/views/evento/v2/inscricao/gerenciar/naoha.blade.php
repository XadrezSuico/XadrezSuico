@extends('layouts.v2.guest')

@section('content')
    <div class="v2-legacy-content w-full max-w-lg">
        @include('evento.v2._content.inscricao.gerenciar.naoha')
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/vendor/bootstrap/dist/css/bootstrap.min.css') }}">
    <style>
        .display-none, .displayNone { display: none; }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('vendor/adminlte/vendor/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ url('/js/jquery.mask.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#alerts').modal();
        });
    </script>
@endpush

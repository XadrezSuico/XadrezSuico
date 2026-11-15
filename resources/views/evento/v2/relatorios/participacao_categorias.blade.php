@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.relatorios.participacao_categorias')
@endsection

@push('styles')
    <style>
        .display-none, .displayNone {
            display: none;
        }

        .box-title.evento {
            font-size: 2.5rem;
            font-weight: bold;
        }

        #tabela tfoot td {
            font-weight: bold;
        }
    </style>
@endpush

@push('event-scripts')
<script type="text/javascript">
    $(document).ready(function(){
        if ($("#tabela").length) {
            window.initV2DashboardManagedTable("#tabela", {
                order: [[0, 'asc']],
                paging: false
            });
        }
    });
</script>
@endpush

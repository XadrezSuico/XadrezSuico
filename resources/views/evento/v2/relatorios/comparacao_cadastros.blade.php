@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.relatorios.comparacao_cadastros')
@endsection

@push('styles')
    <style>
.display-none, .displayNone{
			display: none;
		}

		.box-title.evento{
			font-size: 2.5rem;
			font-weight: bold;
		}

		.parecer-badge {
			display: inline-block;
			padding: 4px 8px;
			border-radius: 3px;
			font-size: 12px;
			font-weight: bold;
			white-space: nowrap;
		}

		.parecer-confere {
			background-color: #00a65a;
			color: #fff;
		}

		.parecer-verificar {
			background-color: #f39c12;
			color: #fff;
		}

		.parecer-nao-confere,
		.parecer-nao-integrado {
			background-color: #dd4b39;
			color: #fff;
		}

		.legenda-parecer {
			margin-bottom: 15px;
		}

		.legenda-parecer .parecer-badge {
			margin-right: 8px;
		}
    </style>
@endpush

@push('event-scripts')
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
<script type="text/javascript">
    $(document).ready(function(){
        if ($("#tabela").length) {
            $("#tabela").DataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'asc']],
                columnDefs: [
                    { targets: 0, visible: false }
                ],
                paging: false
            });
        }
    });
</script>
@endpush

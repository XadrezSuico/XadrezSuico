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
<script type="text/javascript">
    $(document).ready(function(){
        if ($("#tabela").length) {
            window.initV2DashboardManagedTable("#tabela", {
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

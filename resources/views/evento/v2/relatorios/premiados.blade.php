@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.relatorios.premiados')
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
    </style>
@endpush

@push('event-scripts')
<script type="text/javascript">
    $(document).ready(function(){
        window.initV2DashboardManagedTable("#tabela", {
            ordering: false,
            paging: false
        });
    });
</script>
@endpush

@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.edit')
@endsection

@push('styles')
    <style>
.display-none, .displayNone{
			display: none;
		}
    </style>
@endpush

@push('event-scripts')
<script type="text/javascript">
  $(document).ready(function(){
		$("#categoria_id").select2();
		$("#tipo_torneio_id").select2().val({{$torneio->tipo_torneio_id}}).change();
		$("#softwares_id").select2().val({{$torneio->softwares_id}}).change();
		$("#tabela").DataTable({
				responsive: true,
		});
  });
</script>
@endpush

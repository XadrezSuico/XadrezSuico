@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.new')
@endsection

@push('styles')
    <style>
.display-none, .displayNone{
			display: none;
		}
    </style>
@endpush

@push('event-scripts')
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
	$("#tipo_torneio_id").select2();
	$("#softwares_id").select2();
  });
</script>
@endpush

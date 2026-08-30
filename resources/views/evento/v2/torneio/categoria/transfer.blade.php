@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.categoria.transfer')
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
		$("#tournament_id").select2();
    });
</script>
@endpush

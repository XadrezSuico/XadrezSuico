@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.union')
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
		$("#torneio_a_ser_unido").select2();
    });
</script>
@endpush

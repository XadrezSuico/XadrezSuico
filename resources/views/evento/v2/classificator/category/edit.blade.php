@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.classificator.category.edit')
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
        $("#category_id").select2();
        $("#category_classificator_id").select2();


        $("#category_id").val([{{$event_classificate_category->category_id}}]).change();
        $("#category_classificator_id").val([{{$event_classificate_category->category_classificator_id}}]).change();
  });
</script>
@endpush

@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.classificator.category.new')
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
        $("#category_id").select2();
        $("#category_classificator_id").select2();
  });
</script>
@endpush

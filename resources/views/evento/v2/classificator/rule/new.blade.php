@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.classificator.rule.new')
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
        $("#type").select2();
        $("#event_id").select2();

        $("#type").on("select2:select",function(){
            checkTypeSelected();
        });

        checkTypeSelected();
  });

  function checkTypeSelected(){
    switch($("#type").val()){
        case "position":
        case "position-absolute":
        case "place-by-quantity":
        case "classificate-by-start-position":
            $("#value_block").show("fast");
            $("#event_block").hide("fast");

            $("#event_block select").val("").change();
        break;
        case "pre-classificate":
            $("#value_block").hide("fast");
            $("#event_block").show("fast");

            $("#value_block input").val("");
            break;
        default:
            $("#value_block").hide("fast");
            $("#event_block").hide("fast");

            $("#value_block input").val("");
            $("#event_block select").val("").change();
    }
  }
</script>
@endpush

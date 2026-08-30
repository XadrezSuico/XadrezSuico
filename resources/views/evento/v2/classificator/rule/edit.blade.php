@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.classificator.rule.edit')
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
        $("#type").select2();
        $("#event_id").select2();


        $("#type").val(["{{$event_classificate_rule->type}}"]).change();
        $("#event_id").val([{{$event_classificate_rule->event_id}}]).change();


        $("#type").on("select2:select",function(){
            checkTypeSelected(true);
        });

        checkTypeSelected(false);
  });


  function checkTypeSelected(clear){
    switch($("#type").val()){
        case "position":
        case "position-absolute":
        case "place-by-quantity":
        case "classificate-by-start-position":
            $("#value_block").show("fast");
            $("#event_block").hide("fast");

            if(clear) $("#event_block select").val("").change();
        break;
        case "pre-classificate":
            $("#value_block").hide("fast");
            $("#event_block").show("fast");

            if(clear) $("#value_block input").val("");
            break;
        default:
            $("#value_block").hide("fast");
            $("#event_block").hide("fast");

            if(clear) {
                $("#value_block input").val("");
                $("#event_block select").val("").change();
            }
    }
  }
</script>
@endpush

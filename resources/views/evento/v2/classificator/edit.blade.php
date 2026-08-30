@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.classificator.edit')
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
        $("#event_classificator_id").select2();
        $("#event_group_classificator_id").select2();
        $("#event_or_event_group").select2();

        $("#event_or_event_group").on("select2:select",()=>{
            checkEventOrEventGroup();
        });

        @if($event_classificate->event_classificator_id)
            $("#event_classificator_id").val([{{$event_classificate->event_classificator_id}}]).change();
            $("#event_or_event_group").val('event').change();
        @endif
        @if($event_classificate->event_group_classificator_id)
            $("#event_group_classificator_id").val([{{$event_classificate->event_group_classificator_id}}]).change();
            $("#event_or_event_group").val('event_group').change();
        @endif

        checkEventOrEventGroup();

    });

    async function checkEventOrEventGroup() {
        return new Promise((resolve, reject)=>{

            const eventClassificator = $("#event_classificator_id_group");
            const eventGroupClassificator = $("#event_group_classificator_id_group");

            eventClassificator.hide(100);
            eventGroupClassificator.hide(100);

            setTimeout(() => {
                if ($("#event_or_event_group").val() === "event") {
                    eventClassificator.show(100);
                } else {
                    eventGroupClassificator.show(100);
                }

                resolve();
            }, 200);
        });
    }
</script>
@endpush

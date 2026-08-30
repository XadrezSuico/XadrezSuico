@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.classificator.new')
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
        $("#event_classificator_id").select2();
        $("#event_group_classificator_id").select2();
        $("#event_or_event_group").select2();

        $("#event_or_event_group").on("select2:select",()=>{
            checkEventOrEventGroup();
        });

        checkEventOrEventGroup();
    });

    function checkEventOrEventGroup() {
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
        }, 200);
    }

</script>
@endpush

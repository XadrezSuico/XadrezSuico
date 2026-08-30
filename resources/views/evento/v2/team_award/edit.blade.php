@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.team_award.edit')
@endsection

@push('styles')
    <style>
.form-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 20px;
        flex-shrink: 0;
    }
    .form-switch input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    .form-switch .form-check-input {
        position: absolute;
        pointer-events: none;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 20px;
    }
    .form-switch .form-check-input:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    .form-switch input:checked + .form-check-input {
        background-color: #206bc4;
    }
    .form-switch input:checked + .form-check-input:before {
        transform: translateX(20px);
    }
    .switch-row {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        cursor: pointer;
        font-weight: normal;
    }
    .switch-label {
        margin-left: 10px;
        margin-bottom: 0;
        user-select: none;
    }
    .team-award-checklist li {
        margin-bottom: 6px;
    }
    .team-award-checklist .fa-check { color: #00a65a; }
    .team-award-checklist .fa-times { color: #dd4b39; }
    .team-award-checklist .fa-minus { color: #999; }
    .scoring-panel-disabled {
        opacity: 0.55;
        pointer-events: none;
    }
    .scoring-mode-hint {
        border-left: 3px solid #3c8dbc;
        padding-left: 12px;
        margin-bottom: 15px;
    }
    </style>
@endpush

@push('event-scripts')
<script>
    function syncScoringPanels() {
        var individual = $("#is_points").is(":checked");
        if (individual) {
            $("#box_pontos_categoria").slideDown(150);
            $("#pontos_posicao_inner").addClass("scoring-panel-disabled");
        } else {
            $("#box_pontos_categoria").slideUp(150);
            $("#pontos_posicao_inner").removeClass("scoring-panel-disabled");
        }
    }

    $("#is_points").on("change", syncScoringPanels);
    syncScoringPanels();

    $(".btn-preset").on("click", function () {
        $("#place").val($(this).data("place"));
        $("#score").val($(this).data("score"));
    });

    @if($active_tab && $active_tab !== 'geral')
        $("#team_award_tabs a[href='#ta_{{ $active_tab }}']").tab("show");
    @endif
</script>
@endpush

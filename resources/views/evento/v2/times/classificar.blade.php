@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.times.classificar')
@endsection

@push('styles')
    <style>
.fa-spinner{
        color: orange;
    }
    .fa-times{
        color: red;
    }
    .alert-danger .fa-times{
        color: white;
    }
    .fa-check{
        color: green;
    }
    </style>
@endpush

@push('event-scripts')
<script type="text/javascript">
    premiacoes_time = [];
    erro = false;
    @php($j = 0)
    @foreach($evento->event_team_awards()->where([["is_can_calculate","=",true]])->get() as $premiacao_time)
        @if(!$premiacao_time->hasConfig("no_classificate"))
            premiacoes_time[{{$j++}}] = {{$premiacao_time->id}};
        @endif
    @endforeach

    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
        });
        setTimeout(function(){
            start();
        },1000);
    });

    function start(){
        proxima_premiacao(0);
    }

    function proxima_premiacao(i){
        $("#time_award_".concat(premiacoes_time[i]).concat("_icon")).show(200);
        execute(i,1);
    }

    function execute(i,action){
        switch(action){
            case 1:
            case 2:
                $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(action).concat("_icon")).show(200);
                $.getJSON('{{url("/evento/premiacao_time/classificar/".$evento->id."/call")}}'.concat('/').concat(premiacoes_time[i]).concat('/').concat(action),function(data){
                    if(data.ok == 1){
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(action).concat("_icon")).removeClass('fa-spinner');
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(action).concat("_icon")).addClass('fa-check');
                    }else{
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(action).concat("_icon")).removeClass('fa-spinner');
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(action).concat("_icon")).addClass('fa-times');
                    }
                    execute(i,action + 1);
                });
                break;
            case 3:
                $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(3).concat("_icon")).show(200);
                $.getJSON('{{url("/evento/premiacao_time/classificar/".$evento->id."/call")}}'.concat('/').concat(premiacoes_time[i]).concat('/').concat(action),function(data){
                    if(data.ok == 1){
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(3).concat("_icon")).removeClass('fa-spinner');
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(3).concat("_icon")).addClass('fa-check');
                    }else{
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(3).concat("_icon")).removeClass('fa-spinner');
                        $("#time_award_".concat(premiacoes_time[i]).concat("_").concat(3).concat("_icon")).addClass('fa-times');
                    }

                    setTimeout(function(){
                        if(
                            $("#time_award_".concat(premiacoes_time[i]).concat("_1_icon")).hasClass('fa-check') &&
                            $("#time_award_".concat(premiacoes_time[i]).concat("_2_icon")).hasClass('fa-check') &&
                            $("#time_award_".concat(premiacoes_time[i]).concat("_3_icon")).hasClass('fa-check')
                        ){
                            $("#time_award_".concat(premiacoes_time[i]).concat("_icon")).removeClass('fa-spinner');
                            $("#time_award_".concat(premiacoes_time[i]).concat("_icon")).addClass('fa-check');
                        }else{
                            $("#time_award_".concat(premiacoes_time[i]).concat("_icon")).removeClass('fa-spinner');
                            $("#time_award_".concat(premiacoes_time[i]).concat("_icon")).addClass('fa-times');
                            erro = true;
                        }


                        if((i+1) < premiacoes_time.length){
                            proxima_premiacao(i+1);
                        }else{
                            muda_alerta();
                        }
                    },700);
                });
        }
    }

    function muda_alerta(){
        $("#processo_aguarde").hide(400);
        if(!erro){
            $("#processo_sucesso").show(400);
        }else{
            $("#processo_erro").show(400);
        }
    }
</script>
@endpush

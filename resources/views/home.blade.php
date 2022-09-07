@extends('adminlte::page')

@section('title', 'AdminLTE')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Você está logado!</p>
    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal())
        <div class="row">
            <div class="col-md-4">
                <!-- small box -->
                <div class="small-box bg-aqua">
                    <div class="inner">
                    <h3>{{\App\Email::where([["is_sent","=",0]])->count()}}</h3>

                    <p>E-mails pendentes</p>
                    </div>
                    <div class="icon">
                    <i class="fa fa-envelope"></i>
                    </div>
                    <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                </div>
            </div>
            <div class="col-md-4">
                <!-- small box -->
                <div class="small-box bg-green">
                    <div class="inner">
                    <h3>{{\App\Evento::countAllReceivingRegister()}}</h3>

                    <p>Eventos Recebendo Inscrições</p>
                    </div>
                    <div class="icon">
                    <i class="fa fa-ticket"></i>
                    </div>
                    <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                </div>
            </div>
        </div>
    @endif
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Próximos Eventos</h3>
        </div>
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome do Evento</th>
                        <th>Cidade</th>
                        <th>Local do Evento</th>
                        <th>Datas</th>
                        <th>Inscrições</th>
                        <th>Recebendo Inscrições?</th>
                        <th>Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(\App\Evento::where([["data_fim",">=", date("Y-m-d",time() - (60*60*24))]])->get() as $evento)
                        @if(
                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4]) ||
                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6,7])
                        )
                            <tr>
                                <td>{{$evento->id}}</td>
                                <td>{{$evento->name}}<br/>({{$evento->grupo_evento->name}})</td>
                                <td>{{$evento->cidade->name}}/{{trim($evento->cidade->estado->abbr)}} - {{$evento->cidade->estado->pais->codigo_iso}}</td>
                                <td>{{$evento->local}}</td>
                                <td data-order="{{$evento->data_inicio}}">
                                    @if($evento->getDataInicio() == $evento->getDataFim())
                                        {{$evento->getDataInicio()}}
                                    @else
                                        {{$evento->getDataInicio()}}<br/>{{$evento->getDataFim()}}
                                    @endif
                                </td>
                                <td data-order="{{$evento->quantosInscritos()}}">
                                    Total de Inscritos: {{$evento->quantosInscritos()}}@if($evento->maximo_inscricoes_evento)/{{$evento->maximo_inscricoes_evento}}@endif<br/>
                                    @if(
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                    )
                                        Confirmados: {{$evento->quantosInscritosConfirmados()}}<br/>
                                        Presentes: {{$evento->quantosInscritosPresentes()}}
                                        <hr/>
                                        @if($evento->is_lichess_integration)
                                            <strong>Torneio Lichess.org</strong><br/>
                                            Inscritos: <strong>{{$evento->quantosInscritosConfirmadosLichess()}}</strong><br/>
                                            Não Inscritos: <strong>{{$evento->quantosInscritosFaltamLichess()}}</strong>
                                        @endif
                                    @endif
                                </td>
                                <td>@if(!$evento->inscricoes_encerradas()) Sim @else <strong>Não</strong> @endif</td>
                                <td>
                                    <a class="btn btn-default" href="{{url("/evento/dashboard/".$evento->id)}}" role="button">Dashboard</a>
                                    @if(
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                    )
                                        <a class="btn btn-success" href="{{url("/inscricao/".$evento->id)}}" target="_blank" role="button">Nova Inscrição</a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop


@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
        });
    });
</script>
@endsection

@extends('adminlte::page')

@section('title', 'Eventos')

@section('content_header')
    <h1>Eventos</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Período</th>
                        <th>Local</th>
                        <th>Grupo de Evento</th>
                        <th>Inscritos</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventos as $evento)
                        @if(
                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4,5]) ||
                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
                        )
                            <tr>
                                <td>{{$evento->id}}</td>
                                <td>{{$evento->name}}</td>
                                <td>{{$evento->getDataInicio()}}<br/>{{$evento->getDataFim()}}</td>
                                <td>{{$evento->cidade->name}} - {{$evento->local}}</td>
                                <td>{{$evento->grupo_evento->name}}</td>
                                <td>
                                    Total: {{$evento->quantosInscritos()}}<br/>
                                    Confirmados: {{$evento->quantosInscritosConfirmados()}}
                                </td>
                                <td>
                                    @if(
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4]) ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
                                    )
                                        <a class="btn btn-default" href="{{url("/evento/dashboard/".$evento->id)}}" role="button">Dashboard</a>
                                    @endif
                                    @if(
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5])
                                    )
                                        <a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id)}}" role="button">Nova Inscrição</a>
                                        <a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id."/confirmacao")}}" role="button">Confirmar Inscrição</a>
                                    @endif
                                    @if($evento->isDeletavel() && \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) <a class="btn btn-danger" href="{{url("/evento/delete/".$evento->id)}}" role="button">Apagar</a> @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
        });
    });
</script>
@endsection

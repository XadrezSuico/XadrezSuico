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
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/evento/new")}}">Novo Evento</a></li>
    </ul>

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
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($eventos as $evento)
                        <tr>
                            <td>{{$evento->id}}</td>
                            <td>{{$evento->name}}</td>
                            <td>{{$evento->getDataInicio()}}<br/>{{$evento->getDataFim()}}</td>
                            <td>{{$evento->cidade->name}} - {{$evento->local}}</td>
                            <td>{{$evento->grupo_evento->name}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/evento/edit/".$evento->id)}}" role="button">Editar</a>
                                <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios")}}" role="button">Torneios</a>
                                <a class="btn btn-default" href="{{url("/evento/".$evento->id."/toggleresultados")}}" role="button">@if($evento->mostrar_resultados) Restringir @else Liberar @endif Classificação Pública</a>
                                <a class="btn btn-default" href="{{url("/evento/classificar/".$evento->id)}}" role="button">Classificar Evento</a>
                                <a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id)}}" role="button">Nova Inscrição</a>
                                <a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id."/confirmacao")}}" role="button">Confirmar Inscrição</a>
                                @if($evento->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/delete/".$evento->id)}}" role="button">Apagar</a> @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable();
    });
</script>
@endsection

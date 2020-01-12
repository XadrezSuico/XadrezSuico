@extends('adminlte::page')

@section('title', 'Grupos de Evento')

@section('content_header')
    <h1>Grupos de Evento</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal())
            <li role="presentation"><a href="{{url("/grupoevento/new")}}">Novo Grupo de Evento</a></li>
        @endif
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupos_evento as $grupo_evento)
                        @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($grupo_evento->id,[6,7]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfilByGroupEvent($grupo_evento->id,[3,4,5]))
                            <tr>
                                <td>{{$grupo_evento->id}}</td>
                                <td>{{$grupo_evento->name}}</td>
                                <td>
                                    <a class="btn btn-default" href="{{url("/grupoevento/dashboard/".$grupo_evento->id)}}" role="button">Dashboard</a>
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal())
                                        @if($grupo_evento->isDeletavel())
                                            <a class="btn btn-danger" href="{{url("/grupoevento/delete/".$grupo_evento->id)}}" role="button">Apagar</a>
                                        @endif
                                    @endif
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

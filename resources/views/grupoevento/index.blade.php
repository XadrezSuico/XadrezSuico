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
        <li role="presentation"><a href="{{url("/grupoevento/new")}}">Novo Grupo de Evento</a></li>
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
                        <tr>
                            <td>{{$grupo_evento->id}}</td>
                            <td>{{$grupo_evento->name}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/grupoevento/dashboard/".$grupo_evento->id)}}" role="button">Dashboard</a>
                                @if($grupo_evento->isDeletavel()) <a class="btn btn-danger" href="{{url("/grupoevento/delete/".$grupo_evento->id)}}" role="button">Apagar</a> @endif
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
        $("#tabela").DataTable({
            responsive: true,
        });
    });
</script>
@endsection

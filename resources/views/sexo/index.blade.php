@extends('adminlte::page')

@section('title', 'Sexos')

@section('content_header')
    <h1>Sexos</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/sexo/new")}}">Novo Sexo</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Abreviação</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sexos as $sexo)
                        <tr>
                            <td>{{$sexo->id}}</td>
                            <td>{{$sexo->name}}</td>
                            <td>{{$sexo->abbr}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/sexo/edit/".$sexo->id)}}" role="button">Editar</a>
                                @if($sexo->isDeletavel()) <a class="btn btn-danger" href="{{url("/sexo/delete/".$sexo->id)}}" role="button">Apagar</a> @endif
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

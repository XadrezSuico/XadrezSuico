@extends('adminlte::page')

@section('title', 'Cidades')

@section('content_header')
    <h1>Cidades</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/cidade/new")}}">Nova Cidade</a></li>
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
                    @foreach($cidades as $cidade)
                        <tr>
                            <td>{{$cidade->id}}</td>
                            <td>{{$cidade->name}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/cidade/edit/".$cidade->id)}}" role="button">Editar</a>
                                @if($cidade->isDeletavel()) <a class="btn btn-danger" href="{{url("/cidade/delete/".$cidade->id)}}" role="button">Apagar</a> @endif
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

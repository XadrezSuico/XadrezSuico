@extends('adminlte::page')

@section('title', 'Clubes')

@section('content_header')
    <h1>Clubes</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/clube/new")}}">Novo Clube</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Cidade</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clubes as $clube)
                        <tr>
                            <td>{{$clube->id}}</td>
                            <td>{{$clube->name}}</td>
                            <td>{{$clube->cidade->name}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/clube/edit/".$clube->id)}}" role="button">Editar</a>
                                @if($clube->isDeletavel()) <a class="btn btn-danger" href="{{url("/clube/delete/".$clube->id)}}" role="button">Apagar</a> @endif
                                <hr/>
                                <a class="btn btn-warning" href="{{url("/clube/union/".$clube->id)}}" role="button">Unir para Este Clube</a>
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

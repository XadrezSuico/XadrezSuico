@extends('adminlte::page')

@section('title', 'Categorias')

@section('content_header')
    <h1>Categorias</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/categoria/new")}}">Nova Categoria</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Faixa de Idade</th>
                        <th>Código Categoria</th>
                        <th>Código Grupo</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categorias as $categoria)
                        <tr>
                            <td>{{$categoria->id}}</td>
                            <td>{{$categoria->name}}</td>
                            <td>
                                @if(!$categoria->idade_minima && !$categoria->idade_maxima)
                                    Sem limitação de idade
                                @else
                                    @if($categoria->idade_minima && !$categoria->idade_maxima)
                                        Idade Mínima: {{$categoria->idade_minima}}
                                    @else
                                        @if(!$categoria->idade_minima && $categoria->idade_maxima)
                                            Idade Máxima: {{$categoria->idade_maxima}}
                                        @else
                                            Entre {{$categoria->idade_minima}} e {{$categoria->idade_maxima}}
                                        @endif
                                    @endif
                                @endif
                            </td>
                            <td>{{$categoria->cat_code}}</td>
                            <td>{{$categoria->code}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/categoria/dashboard/".$categoria->id)}}" role="button">Dashboard</a>
                                @if($categoria->isDeletavel()) <a class="btn btn-danger" href="{{url("/categoria/delete/".$categoria->id)}}" role="button">Apagar</a> @endif
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

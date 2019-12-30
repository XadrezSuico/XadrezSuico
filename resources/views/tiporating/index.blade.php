@extends('adminlte::page')

@section('title', 'Tipos de Rating')

@section('content_header')
    <h1>Tipos de Rating</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/tiporating/new")}}">Novo Tipo de Rating</a></li>
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
                    @foreach($tipos_rating as $tipo_rating)
                        <tr>
                            <td>{{$tipo_rating->id}}</td>
                            <td>{{$tipo_rating->name}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/tiporating/dashboard/".$tipo_rating->id)}}" role="button">Dashboard</a>
                                @if($tipo_rating->isDeletavel()) <a class="btn btn-danger" href="{{url("/tiporating/delete/".$tipo_rating->id)}}" role="button">Apagar</a> @endif
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

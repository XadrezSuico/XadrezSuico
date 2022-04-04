@extends('adminlte::page')

@section('title', 'Tipo de Rating #'.$tipo_rating->id." - Ratings")

@section('content_header')
    <h1>Tipo de Rating #{{$tipo_rating->id}} ({{$tipo_rating->name}}) - Ratings</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/rating")}}">Voltar à Seleção do Tipo de Rating</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Rating</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tipo_rating->ratings()->orderBy("enxadrista_id","ASC")->get() as $rating)
                        <tr>
                            <td>{{$rating->id}}</td>
                            <td>#{{$rating->enxadrista->id}} - {{$rating->enxadrista->getNomePublico()}}</td>
                            <td>{{$rating->enxadrista->getNascimentoPublico()}}</td>
                            <td>{{$rating->valor}}</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/rating/".$tipo_rating->id."/view/".$rating->id)}}" role="button">Visualizar</a>
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

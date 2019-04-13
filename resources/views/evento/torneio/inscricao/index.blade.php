@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id." - Torneio #".$torneio->id." - Inscrições")

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Torneio #{{$torneio->id}} - Inscrições</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/evento/".$evento->id."/torneios")}}">Voltar à Lista de Torneios</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Rating</th>
                        <th>Categoria</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>Confirmado?</th>
                        <th width="20%">Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr>
                            <td>{{$inscricao->id}}</td>
                            <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}}</td>
                            <td>@if($inscricao->enxadrista->ratings()->where([["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id]])->count() > 0) {{$inscricao->enxadrista->ratings()->where([["tipo_ratings_id","=",$evento->tipo_rating->tipo_ratings_id]])->first()->valor}} @else Não Há @endif</td>
                            <td>{{$inscricao->categoria->name}}</td>
                            <td>{{$inscricao->cidade->name}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else Sem Clube @endif</td>
                            <td>@if($inscricao->confirmado) Sim @else Não @endif</td>
                            <td>
                                <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$inscricao->id)}}" role="button">Editar</a>
                                @if($inscricao->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/delete/".$inscricao->id)}}" role="button">Apagar</a> @endif
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

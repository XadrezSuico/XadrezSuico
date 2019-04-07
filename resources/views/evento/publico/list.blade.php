@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' - Resultados da Categoria #'.$categoria->id)

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Resultados da Categoria #{{$categoria->id}} ({{$categoria->name}})</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/evento/classificacao/".$evento->id)}}">Voltar à Seleção da Categoria</a></li>
    </ul>

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        <th>Pontuação</th>
                        @foreach($criterios as $criterio)
                            <th>{{$criterio->criterio->code}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr>
                            <td>{{$inscricao->posicao}}</td>
                            <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}}</td>
                            <td>{{$inscricao->enxadrista->getBorn()}}</td>
                            <td>{{$inscricao->cidade->name}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->name}} @else Sem Clube @endif</td>
                            <td>{{$inscricao->pontos}}</td>
                            @foreach($criterios as $criterio)
                                <th>@if($criterio->criterio->valor_criterio($inscricao->id)) {{$criterio->criterio->valor_criterio($inscricao->id)->valor}} @else - @endif</th>
                            @endforeach
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

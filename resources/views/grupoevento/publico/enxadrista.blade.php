@extends('adminlte::page')

@section('title', 'Grupo de Evento #'.$grupo_evento->id." - Pontuação de ".$enxadrista->name)

@section('content_header')
    <h1>Grupo de Evento #{{$grupo_evento->id}} ({{$grupo_evento->name}}) - Pontuação de {{$enxadrista->name}}</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/grupoevento/classificacao/".$grupo_evento->id)}}">Voltar à Lista de Pontuação do Grupo de Evento</a></li>
    </ul>

    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Cadastro</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
        <div class="box-body">
                <h3 style="margin: 0; padding: 0;"><strong>Nome:</strong> {{$enxadrista->name}}<br/></h3>
                <strong>Data de Nascimento:</strong> {{$enxadrista->getBorn()}}<br/>
                <strong>Cidade:</strong> {{$enxadrista->cidade->name}}<br/>
                <strong>FIDE:</strong> {{$enxadrista->fide_id}}<br/>
                <strong>CBX:</strong> {{$enxadrista->cbx_id}}<br/>
                @if($enxadrista->clube) <strong>Clube:</strong> {{$enxadrista->clube->name}}<br/> @endif
        </div>
    </div>
    @foreach($enxadrista->getCategoriasParticipantesbyGrupoEvento($grupo_evento->id) as $categoria)
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Categoria: {{$categoria->name}}</h3>
                <div class="pull-right box-tools">
                </div>
            </div>
            @php($pontuacao_enxadrista = $enxadrista->getPontuacaoGeral($grupo_evento->id,$categoria->id))
            <div class="box-body">
                <strong>Pontuação Atual:</strong> @if($pontuacao_enxadrista) {{$pontuacao_enxadrista->pontos}} @else - @endif<br/>
                <strong>Quantidade de Etapas Consideradas para a Pontuação:</strong> @if($pontuacao_enxadrista) {{$pontuacao_enxadrista->inscricoes_calculadas}} @else - @endif<br/>
                @if($pontuacao_enxadrista->grupo_evento->limite_calculo_geral) <strong>Limite de Etapas Consideradas para a Pontuação neste Grupo de Evento:</strong> {{$pontuacao_enxadrista->grupo_evento->limite_calculo_geral}} @endif
        
                <table class="table-responsive table-condensed table-striped tabela" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Evento</th>
                            <th>Torneio</th>
                            <th>Categoria</th>
                            <th>Posição</th>
                            <th>Pontuação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enxadrista->getInscricoesByGrupoEventoECategoria($grupo_evento->id,$categoria->id) as $inscricao)
                            <tr>
                                <td>{{$inscricao->id}}</td>
                                <td>{{$inscricao->torneio->evento->name}}</td>
                                <td>{{$inscricao->torneio->name}}</td>
                                <td>{{$inscricao->categoria->name}}</td>
                                <td>{{$inscricao->posicao}}</td>
                                <td>{{$inscricao->pontos_geral}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $(".tabela").DataTable({
            responsive: true,
        });
    });
</script>
@endsection

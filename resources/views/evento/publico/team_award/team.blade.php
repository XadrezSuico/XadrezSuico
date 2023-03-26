@extends('adminlte::page')

@section('title', 'Evento #'.$event->id." Pontuação de Times '".$team_award->name."' - Pontuação do Time '".$team->name."'")

@section('content_header')
    <h1>Evento #{{$event->id}} ({{$event->name}}) - Pontuação de Times #{{$team_award->id}} ({{$team_award->name}})</h1>
    <h2>Pontuação do Time #{{$team->id}} ({{$team->name}})</h2>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    <ul class="nav nav-pills">
        <li role="presentation"><a href="{{url("/evento/".$event->id."/team_awards/".$team_award->id."/results")}}">Voltar à Lista de Pontuação de Times</a></li>
    </ul>

    <div class="box">
		<div class="box-header">
			<h3 class="box-title">Cadastro</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
        <div class="box-body">
                <h3 style="margin: 0; padding: 0;"><strong>Nome:</strong> {{$team->name}}<br/></h3>
                <strong>ID de Cadastro:</strong> {{$team->id}}<br/>
                <strong>Cidade:</strong> {{$team->cidade->name}}<br/>
        </div>
    </div>
    @php($resume = array())
    @foreach($team_award->categories->all() as $team_award_category)
        <div class="box">
            <div class="box-header">
                <h3 class="box-title">Categoria: {{$team_award_category->category->name}}</h3>
                <div class="pull-right box-tools">
                </div>
            </div>
            <div class="box-body">
                <table class="table-responsive table-condensed table-striped tabela" style="width: 100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Enxadrista</th>
                            <th>Torneio</th>
                            <th>Categoria</th>
                            <th>Posição</th>
                            <th>Pontuação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($j = 0)
                        @php($total = 0)
                        @foreach($team->inscricoes()->where([
                                ["categoria_id","=",$team_award_category->category->id],
                                ["confirmado","=",true],
                                ["desconsiderar_pontuacao_geral","=",false],
                            ])
                            ->whereHas("torneio",function($q1) use ($team_score) {
                                $q1->where([["evento_id","=",$team_score->event_team_award->events_id]]);
                            })
                            ->orderBy("posicao","ASC")->get() as $inscricao)
                            <tr>
                                <td>{{$inscricao->id}}</td>
                                <td>{{$inscricao->torneio->evento->name}}</td>
                                <td>{{$inscricao->torneio->name}}</td>
                                <td>{{$inscricao->categoria->name}}</td>
                                <td>{{$inscricao->posicao}}</td>
                                <td>
                                    @if($inscricao->confirmado && !$inscricao->is_desclassificado && !$inscricao->desconsiderar_pontuacao_geral)
                                        @if($team_award->hasConfig("limit_places"))
                                            @if($team_award->getConfig("limit_places",true) > $j)
                                                @if($team_award->hasPlace($inscricao->posicao))
                                                    {{$team_award->getPlace($inscricao->posicao,true)}}
                                                    @php($j++)
                                                    @php($total += $team_award->getPlace($inscricao->posicao,true))
                                                @else
                                                    0
                                                @endif
                                            @else
                                                -
                                            @endif
                                        @else
                                            @if($team_award->hasPlace($inscricao->posicao))
                                                {{$team_award->getPlace($inscricao->posicao,true)}}
                                                @php($total += $team_award->getPlace($inscricao->posicao,true))
                                            @else
                                                0
                                            @endif
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <h3>Resumo da Categoria:</h3>
                <p><strong>Total de Lugares considerados:</strong> {{$j}}</p>
                <p><strong>Total de Pontuação:</strong> {{$total}}</p>
                @php($resume[] = ["total"=>$total,"places"=>$j])
            </div>
        </div>
    @endforeach
    @php($total_general = 0)
    @php($places_general = 0)
    @foreach($resume as $item)
        @php($total_general += $item["total"])
        @php($places_general += $item["places"])
    @endforeach
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Resumo Geral</h3>
            <div class="pull-right box-tools">
            </div>
        </div>
        <div class="box-body">
            <p><strong>Total de Posições Consideradas: </strong>{{$places_general}}</p>
            <p><strong>Total de Pontuação: </strong>{{$total_general}}</p>
        </div>
    </div>
@endsection

@section("js")
<script type="text/javascript">
    $(document).ready(function(){
        $(".tabela").DataTable({
            responsive: true,
            "ordering": false,
        });
    });
</script>
@endsection

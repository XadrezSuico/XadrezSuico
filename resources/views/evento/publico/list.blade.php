@extends('adminlte::page')

@section('title', 'Evento #'.$evento->id.' ('.$evento->name.') - Resultados da Categoria "'.$categoria->name.'"')

@section('content_header')
    <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Resultados da Categoria #{{$categoria->id}} ({{$categoria->name}})</h1>
@stop

@section('content')
    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if(\Illuminate\Support\Facades\Auth::check())
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/classificacao/".$evento->id."/interno")}}">Voltar à Seleção da Categoria</a></li>
        </ul>
    @else
        <ul class="nav nav-pills">
            <li role="presentation"><a href="{{url("/evento/classificacao/".$evento->id)}}">Voltar à Seleção da Categoria</a></li>
        </ul>
	@endif

    @if($evento->classifica)
        <div class="alert alert-success" role="alert">
            <h3 style="margin-top: 0;">Importante!</h3>
            <p>Os nomes em <strong><span style="color: green"><u>verde e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->classifica->name}}</strong>.</p>
            <p>Já os nomes em <strong><span style="color: orange"><u>laranja e sublinhados</u></span></strong> estão classificados para uma etapa do <strong>{{$evento->classifica->grupo_evento->name}}</strong> do mesmo dia por <strong><u>outro evento classificatório</u></strong>.</p>
            @if($evento->classifica->grupo_evento->evento_classifica) <p>E os nomes em <strong><span style="color: red"><u>vermelho e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->classifica->grupo_evento->evento_classifica->name}}</strong> e por isso <strong><u>não podem mais se classificar</u></strong> para algum evento do <strong>{{$evento->classifica->grupo_evento->name}}</strong>.</p> @endif
            <p>A <strong>lista de classificados pode sofrer alterações</strong> devido caso ocorra declínio por parte de algum(a) enxadrista, caso permitido assim pela organização ou pelo regulamento.</p>
        </div>
    @endif
    @if($evento->grupo_evento->evento_classifica)
        <div class="alert alert-success" role="alert">
            <h3 style="margin-top: 0;">Importante!</h3>
            <p>Os nomes em <strong><span style="color: green"><u>verde e sublinhados</u></span></strong> estão classificados para o <strong>{{$evento->grupo_evento->evento_classifica->name}}</strong>.</p>
            <p>A <strong>lista de classificados pode sofrer alterações</strong> devido caso ocorra declínio por parte de algum(a) enxadrista, caso permitido assim pela organização ou pelo regulamento.</p>
        </div>
    @endif

    <div class="box">
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Inscrição</th>
                        <th>Nome</th>
                        <th>Data de Nascimento</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        @if($torneio->tipo_torneio->usaPontuacao()) <th>Pontuação</th> @endif
                        @foreach($criterios as $criterio)
                            <th>D-{{$criterio->prioridade}}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($inscricoes as $inscricao)
                        <tr>

                            <td data-sort='{{($inscricao->posicao) ? $inscricao->posicao : 999999999}}'>@if($inscricao->posicao) {{$inscricao->posicao}} @else - @endif</td>
                            <td data-sort='{{($inscricao->id)}}'>{{$inscricao->id}}</td>
                            @if($evento->classifica)
                                @if($evento->classifica->enxadristaInscrito($inscricao->enxadrista->id))
                                    <!-- E -->
                                    <td style="font-weight: bold; color: green; text-decoration: underline" >
                                        #{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <u><i>(WO)</i></u> @endif
                                    </td>
                                @else
                                    @if($evento->classifica->grupo_evento->enxadristaJaInscritoEmOutroEvento($evento->classifica->id,$inscricao->enxadrista->id))
                                        <!-- JE -->
                                        <td style="font-weight: bold; color: orange; text-decoration: underline" >
                                            #{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <u><i>(WO)</i></u> @endif
                                        </td>
                                    @else
                                        @if($evento->classifica->grupo_evento->evento_classifica)
                                            @if($evento->classifica->grupo_evento->evento_classifica->enxadristaInscrito($inscricao->enxadrista->id))
                                                <!-- GE -->
                                                <td style="font-weight: bold; color: red; text-decoration: underline">
                                                    #{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <u><i>(WO)</i></u> @endif
                                                </td>
                                            @else
                                                <!-- SGE -->
                                                <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <strong><u><i>(WO)</i></u></strong> @endif</td>
                                            @endif
                                        @else
                                            <!-- S -->
                                            <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <strong><u><i>(WO)</i></u></strong> @endif</td>
                                        @endif
                                    @endif
                                @endif
                            @else
                                @if($evento->grupo_evento->evento_classifica)
                                    @php
                                        /*if($evento->grupo_evento->evento_classifica->enxadristaInscrito($inscricao->enxadrista->id))*/
                                    @endphp
                                    @if($inscricao->hasInscricoesFromEstaParaEvento($evento->grupo_evento->evento_classifica->id))
                                        <!-- 2GE -->
                                        <td style="font-weight: bold; color: green; text-decoration: underline">
                                            #{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <u><i>(WO)</i></u> @endif
                                        </td>
                                    @else
                                        <!-- 2NGE -->
                                        <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <strong><u><i>(WO)</i></u></strong> @endif</td>
                                    @endif
                                @else
                                    <!-- 2N -->
                                    <td>#{{$inscricao->enxadrista->id}} - {{$inscricao->enxadrista->name}} @if($inscricao->desconsiderar_pontuacao_geral) <strong><u><i>(WO)</i></u></strong> @endif</td>
                                @endif
                            @endif

                            <td>{{$inscricao->enxadrista->getNascimentoPublico()}}</td>
                            <td>{{$inscricao->getCidade()}}</td>
                            <td>@if($inscricao->clube) {{$inscricao->clube->getName()}} @else Sem Clube @endif</td>
                            @if($torneio->tipo_torneio->usaPontuacao()) <td>@if($inscricao->posicao) {{$inscricao->pontos}} @else - @endif</td> @endif
                            @foreach($criterios as $criterio)
                                <th>@if($criterio->criterio->valor_criterio_visualizacao($inscricao->id)) {{$criterio->criterio->valor_criterio_visualizacao($inscricao->id)}} @else - @endif</th>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <hr/>
            <h4>Legenda dos Critérios de Desempate:</h4><br/>
            @foreach($criterios as $criterio)
                <strong>D-{{$criterio->prioridade}} - {{$criterio->criterio->code}}:</strong> {{$criterio->criterio->name}}<br/>
            @endforeach
        </div>
    </div>
@endsection

@section("js")
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela").DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>
@endsection

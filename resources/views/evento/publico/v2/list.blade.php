<p>Cache em: <strong>{{date("d/m/Y H:i:s")}}</strong> - Categoria: <strong>{{$categoria->name}}</strong></p>
@if($is_internal) <p><strong>Resultado Interno</strong> - Está acessível ao público? {!! ($evento->mostrar_resultados) ? "<strong>Sim</strong>" : "Não" !!} </p> @endif
<hr/>
<table id="tabela_categoria_{{$categoria->id}}" class="table-responsive table-condensed table-striped" style="width: 100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Inscrição</th>
            <th>Nome</th>
            <th>Data de Nascimento</th>
            <th>Cidade</th>
            <th>Clube</th>
            @foreach($evento->event_classificates->all() as $event_classificates)
                <th>
                    Classificado?
                    {{$event_classificates->event->name}}
                </th>
            @endforeach
            @if($torneio->tipo_torneio->usaPontuacao()) <th>Pontuação</th> @endif
            @foreach($criterios as $criterio)
                <th>D-{{$criterio->prioridade}}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($inscricoes as $inscricao)
            <tr @if($is_internal && $inscricao->is_draw) class="is_draw" @endif>

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
                @foreach($evento->event_classificates->all() as $event_classificates)
                    <td>
                        @if($inscricao->enxadrista->estaInscrito($event_classificates->event->id))
                            @php($inscricao_classificated = $inscricao->enxadrista->getInscricao($event_classificates->event->id))

                            @if($inscricao_classificated->hasConfig("event_classificator_id"))
                                @if($inscricao_classificated->getConfig("event_classificator_id",true) == $evento->id)
                                    @if($inscricao_classificated->hasConfig("event_classificator_rule_id"))
                                        <strong>Inscrito</strong><br>
                                        @php($rule_that_classificated = \App\Classification\EventClassificateRule::where([["id", "=", $inscricao_classificated->getConfig("event_classificator_rule_id",true)]])->first())

                                        @php($classified_event_rule = \App\Enum\ClassificationTypeRule::get($rule_that_classificated->type)["name"])

                                        @if ($rule_that_classificated->value)
                                            {{$classified_event_rule}} - Valor: {{$rule_that_classificated->value}}
                                        @endif
                                        @if ($rule_that_classificated->event)
                                            {{$classified_event_rule}} - Evento: {{$rule_that_classificated->event->name}}
                                        @endif
                                    @else
                                        <strong>Inscrito</strong><br>
                                        Classificado por Este Evento
                                    @endif
                                @else
                                    <strong>Inscrito</strong><br>
                                    Classificado por <strong>OUTRO</strong> Evento.<br/>
                                    Evento: {{$evento->where([["id","=",$inscricao_classificated->getConfig("event_classificator_id",true)]])->first()->name}}
                                @endif
                            @else
                                <strong>Inscrito</strong><br>
                                Não Classificado
                            @endif
                        @else
                            Não Inscrito<br>
                            Não Classificado
                        @endif
                    </td>
                @endforeach
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

<script type="text/javascript">
    $(document).ready(function(){
        $("#tabela_categoria_{{$categoria->id}}").DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
    });
</script>

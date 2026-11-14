<!-- Main row -->
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Lista de Inscrições</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			<div class="v2-registration-scroll">
            <table id="tabela" class="table table-striped v2-registration-table" style="width: 100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th class="v2-reg-code">Código Enxadrista</th>
                        <th>Nome do Enxadrista</th>
                        <th>Data de Nascimento</th>
                        <th>Sexo</th>
                        <th>Categoria Inscrição</th>
                        <th>Cidade</th>
                        <th>Clube</th>
                        @if($evento->usa_cbx)
                            <th>ID CBX</th>
                            <th>Rating CBX</th>
                        @endif
                        @if($evento->usa_fide)
                            <th>ID FIDE</th>
                            <th>Rating FIDE</th>
                        @endif
                        @if($evento->tipo_rating)
                            <th>Rating</th>
                            <th>Possui rating?</th>
                        @endif
                        @if($evento->isPaid())
                            <th>Pago?</th>
                        @endif
                        <th>Confirmado?</th>
                        <th>Presente?</th>
                        <th>Inscrição Inicial</th>
                        <th>Posição</th>
                        <th>Dados Inscrição Inicial</th>
                        @if ($evento->event_classificators()->count() > 0)
                            <th>Classificado pelo Evento</th>
                        @endif
				    	@foreach($evento->campos() as $campo)
                            <th>{{$campo->name}}</th>
                        @endforeach
                        @if($evento->classificador)
                            @foreach($evento->classificador->campos() as $campo)
                                <th>{{$campo->name}}</th>
                            @endforeach
                        @endif
                        @if($evento->is_lichess_integration)
                            <th>Link de Acesso</th>
                        @endif
                        <th>Opções</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evento->getInscricoes() as $inscricao_ordered)
                        @php($inscricao = $evento->enxadristaInscrito($inscricao_ordered->enxadrista->id))
                        <tr>
                            <td class="v2-reg-number">{{$inscricao->id}}</td>
                            <td>{{$inscricao->enxadrista->id}}</td>
                            <td class="v2-reg-name">
                                {{$inscricao->enxadrista->getNomePrivado()}}
                            </td>
                            <td>{{$inscricao->enxadrista->getNascimentoPrivado()}}</td>
                            <td>@if($inscricao->enxadrista->sexo) {{$inscricao->enxadrista->sexo->id}} - {{$inscricao->enxadrista->sexo->name}} @else - @endif</td>
                            <td class="v2-reg-text">{{$inscricao->categoria->id}} - {{$inscricao->categoria->name}}</td>
                            <td class="v2-reg-text">{{$inscricao->getCidade()}}</td>
                            <td class="v2-reg-text">@if($inscricao->clube) {{$inscricao->clube->getName()}} @else - @endif</td>
                            @if($evento->usa_cbx)
                                <td class="v2-reg-number">{{$inscricao->enxadrista->cbx_id}}</td>
                                <td class="v2-reg-number">{{$inscricao->enxadrista->showRating(1, $evento->tipo_modalidade)}}</td>
                            @endif
                            @if($evento->usa_fide)
                                <td class="v2-reg-number">{{$inscricao->enxadrista->fide_id}}</td>
                                <td class="v2-reg-number">{{$inscricao->enxadrista->showRating(0, $evento->tipo_modalidade, $evento->getConfig("fide_sequence"))}}</td>
                            @endif
                            @if($evento->tipo_rating)
                                <td>{{$inscricao->enxadrista->ratingParaEvento($evento->id,true)}}</td>
                                <td>@if($inscricao->enxadrista->hasRatingParaEvento($evento->id)) Sim @endif</td>
                            @endif
                            @if($evento->isPaid())
                                <td>
                                    @if($inscricao->categoria->isPaid($evento->id))
                                        @if($inscricao->paid)
                                            <span class="v2-reg-badge v2-reg-badge--success">Pago</span>
                                        @else
                                            <span class="v2-reg-badge v2-reg-badge--warning">Pendente</span>
                                        @endif
                                    @else
                                        <span class="v2-reg-badge">Gratuita</span>
                                    @endif
                                </td>
                            @endif
                            <td>
                                @if($inscricao->confirmado) <span class="v2-reg-badge v2-reg-badge--success">Sim</span> @else <span class="v2-reg-badge">Não</span> @endif
                            </td>
                            <td>
                                @if($inscricao->isPresent()) <span class="v2-reg-badge v2-reg-badge--success">Sim</span> @else <span class="v2-reg-badge">Não</span> @endif
                            </td>
                            <td>@if($inscricao->from) {{$inscricao->from->id}} @else - @endif</td>
                            <td>@if($inscricao->from) @if($inscricao->from->posicao) {{$inscricao->from->posicao}} @else - @endif @else - @endif</td>
                            <td class="v2-reg-text">
                                @if($inscricao->from)
                                    Evento: {{$inscricao->from->torneio->evento->id}} - {{$inscricao->from->torneio->evento->name}}<br/>
                                    Torneio: {{$inscricao->from->torneio->id}} - {{$inscricao->from->torneio->name}}<br/>
                                    Categoria: {{$inscricao->from->categoria->id}} - {{$inscricao->from->categoria->name}}<br/>
                                @endif
                            </td>
                            @if ($evento->event_classificators()->count() > 0)
                                <td>
                                    @if($inscricao->hasConfig("event_classificator_id"))
                                        @php($event_that_classificated = \App\Evento::where([["id","=",$inscricao->getConfig("event_classificator_id",true)]])->first())
                                        @if($inscricao->hasConfig("event_classificator_rule_id"))
                                            @php($rule_that_classificated = \App\Classification\EventClassificateRule::where([["id", "=", $inscricao->getConfig("event_classificator_rule_id",true)]])->first())

                                            @php($classified_event_rule = $event_that_classificated->name. " - ". \App\Enum\ClassificationTypeRule::get($rule_that_classificated->type)["name"])

                                            @if ($rule_that_classificated->value)
                                                {{$classified_event_rule}} - Valor: {{$rule_that_classificated->value}}
                                            @endif
                                            @if ($rule_that_classificated->event)
                                                {{$classified_event_rule}} - Evento: {{$rule_that_classificated->event->name}}
                                            @endif
                                        @else
                                            {{$event_that_classificated->name}}
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            @foreach($evento->campos() as $campo)
                                @if($inscricao->hasOpcao($campo->id))
                                    <td>{{$inscricao->getOpcao($campo->id)->opcao->name}}</td>
                                @else
                                    <td>-</td>
                                @endif
                            @endforeach
                            @if($evento->classificador)
                                @foreach($evento->classificador->campos() as $campo)
                                    @if($inscricao->from && $inscricao->from->getOpcao($campo->id))
                                        <td>{{$inscricao->from->getOpcao($campo->id)->opcao->name}}</td>
                                    @else
                                        <td>-</td>
                                    @endif
                                @endforeach
                            @endif
                            @if($evento->is_lichess_integration)
                                <td>
                                    @if(!$inscricao->is_lichess_found)
<a href="{{$inscricao->getLichessProcessLink()}}" class="v2-icon-action v2-icon-action--success" title="Abrir inscrição no torneio Lichess" aria-label="Abrir inscrição no torneio Lichess" data-toggle="tooltip" data-placement="top"><i class="fa fa-external-link" aria-hidden="true"></i></a>
                                    @else
<a href="{{$evento->getLichessTournamentLink()}}" class="v2-icon-action v2-icon-action--warning" title="Já inscrito: abrir torneio Lichess" aria-label="Já inscrito: abrir torneio Lichess" data-toggle="tooltip" data-placement="top"><i class="fa fa-check-circle" aria-hidden="true"></i></a>
                                    @endif
                                </td>
                            @endif
                            <td class="v2-reg-options"><div class="input-group v2-registration-actions" role="group" aria-label="Ações da inscrição">@if(
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4,5]) ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6,7])
                                )
                                    <a title="Editar" aria-label="Editar" data-toggle="tooltip" data-placement="top" class="v2-icon-action v2-icon-action--default" href="{{url("/evento/".$evento->id."/torneios/".$inscricao->torneio->id."/inscricoes/edit/".$inscricao->id)}}" role="button" target="_blank"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                    @include('evento.torneio.inscricao.delete-paid-button', ['registrationList' => true, 'compact' => true])
                                    @if($inscricao->isDeletavel())
                                        <a title="Apagar" aria-label="Apagar" data-toggle="tooltip" data-placement="top" class="v2-icon-action v2-icon-action--danger" href="{{url("/evento/".$evento->id."/torneios/".$inscricao->torneio->id."/inscricoes/delete/".$inscricao->id)}}?route=event_registration_list" role="button"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                    @else
                                        @if(
                                            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal())
                                            @if($inscricao->isDeletavel(true))
                                                <a title="Apagar (Admin)" aria-label="Apagar (Admin)" data-toggle="tooltip" data-placement="top" class="v2-icon-action v2-icon-action--danger" href="{{url("/evento/".$evento->id."/torneios/".$inscricao->torneio->id."/inscricoes/delete_admin/".$inscricao->id)}}?route=event_registration_list" role="button"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            @endif
                                        @endif
                                    @endif
                                @endif


                                @if(
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([9])

                                )

                                    <a title="Editar Enxadrista" aria-label="Editar Enxadrista" data-toggle="tooltip" data-placement="top" class="v2-icon-action v2-icon-action--warning" href="{{url("/enxadrista/edit/".$inscricao->enxadrista->id)}}" role="button" target="_blank"><i class="fa fa-user" aria-hidden="true"></i></a>
                                @endif
                            </div></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
		</div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

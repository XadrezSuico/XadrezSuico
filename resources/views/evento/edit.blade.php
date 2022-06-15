@extends('adminlte::page')

@section("title", "Dashboard de Evento")

@section('content_header')
  <h1>Dashboard de Evento: {{$evento->name}}</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
		.width-100{
			width: 100% !important;
		}
	</style>
@endsection

@section("content")
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/grupoevento/dashboard/{{$evento->grupo_evento->id}}">Voltar a Dashboard de Grupo de Evento</a></li>
</ul>
<div class="row">
  <!-- Left col -->
	<div>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a id="tab_funcoes" href="#funcoes" aria-controls="funcoes" role="tab" data-toggle="tab">Funções</a></li>
			<li role="presentation"><a id="tab_editar_evento" href="#editar_evento" aria-controls="editar_evento" role="tab" data-toggle="tab">Editar Evento</a></li>
			<li role="presentation"><a id="tab_pagina" href="#pagina" aria-controls="pagina" role="tab" data-toggle="tab">Página</a></li>
			<li role="presentation"><a id="tab_criterio_desempate" href="#criterio_desempate" aria-controls="criterio_desempate" role="tab" data-toggle="tab">Critério de Desempate</a></li>
			<li role="presentation"><a id="tab_categoria" href="#categoria" aria-controls="categoria" role="tab" data-toggle="tab">Categoria: Cadastro</a></li>
			<li role="presentation"><a id="tab_categorias_relacionadas" href="#categorias_relacionadas" aria-controls="categorias_relacionadas" role="tab" data-toggle="tab">Categorias Relacionadas</a></li>
			<li role="presentation"><a id="tab_torneio" href="#torneio" aria-controls="torneio" role="tab" data-toggle="tab">Torneios</a></li>
			<li role="presentation"><a id="tab_campo_personalizado" href="#campo_personalizado" aria-controls="campo_personalizado" role="tab" data-toggle="tab">Campos Personalizados Adicionais</a></li>
            <li role="presentation"><a id="tab_email_template" href="#email_template" aria-controls="email_template" role="tab" data-toggle="tab">Templates de E-mail</a></li>
        </ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="funcoes">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary" id="inscricao">
						<div class="box-header">
							<h3 class="box-title">Funções</h3>
						</div>

						<div class="box-body">
							<h4>Inscrições:</h4>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)

								@if($evento->e_inscricao_apenas_com_link)
									<div class="alert alert-warning alert-dismissible" role="alert">
										<strong>Aviso!</strong><br/>
										A inscrição para este evento será efetuada apenas pelo link compartilhado (que é possível acessar logo abaixo).
									</div>
									<a href="{{url("/inscricao/".$evento->id."?token=".$evento->token)}}" class="btn btn-bg-green btn-app">
										<i class="fa fa-plus"></i>
										Nova ou Confirmar Inscrição
									</a>
								@else
									<a href="{{url("/inscricao/".$evento->id)}}" class="btn btn-bg-green btn-app">
										<i class="fa fa-plus"></i>
										Nova ou Confirmar Inscrição
									</a>
								@endif
							@endif
							@if($evento->e_inscricao_apenas_com_link)
								<a href="{{url("/inscricao/".$evento->id."?token=".$evento->token)}}" class="btn btn-success btn-app">
									<i class="fa fa-link"></i>
									Link para Divulgação
								</a>
							@else
								<a href="{{url("/inscricao/".$evento->id)}}" class="btn btn-success btn-app">
									<i class="fa fa-link"></i>
									Link para Divulgação
								</a>
							@endif
                            <br/>
                            @if($evento->e_permite_confirmacao_publica)
                                @if($evento->e_inscricao_apenas_com_link)
                                    <a href="{{url("/inscricao/".$evento->id."/confirmacao?token=".$evento->token)}}" class="btn btn-success btn-app">
                                        <i class="fa fa-check"></i>
                                        Link para Confirmação Pública
                                    </a>
                                @else
                                    <a href="{{url("/inscricao/".$evento->id)}}/confirmacao" class="btn btn-success btn-app">
                                        <i class="fa fa-check"></i>
                                        Link para Confirmação Pública
                                    </a>
                                @endif
                            @endif
                            <br/>

                            <a href="{{url("/evento/".$evento->id."/toggleinscricoes")}}" class="btn btn-warning btn-app">
                                @if(!$evento->is_inscricoes_bloqueadas)
                                    <i class="fa fa-lock"></i>
                                    Bloquear (Status Atual: Liberado)
                                @else
                                    <i class="fa fa-unlock"></i>
                                    Liberar  (Status Atual: Bloqueado)
                                @endif
                                Inscricoes
                            </a>

                            <a href="{{url("/evento/".$evento->id."/toggleedicaoinscricao")}}" class="btn btn-warning btn-app">
                                @if($evento->permite_edicao_inscricao)
                                    <i class="fa fa-lock"></i>
                                    Restringir (Status Atual: Permitido)
                                @else
                                    <i class="fa fa-unlock"></i>
                                    Permitir (Status Atual: Restringido)
                                @endif
                                Edição de Inscrição
                            </a>
							<hr/>
                            <h4>Divulgação de Emparceiramentos:</h4>
							<a href="{{url("/evento/acompanhar/".$evento->id)}}" class="btn btn-app">
								<i class="fa fa-eye"></i>
								Link para Acompanhar os Emparceiramentos
							</a>
							<hr/>
							<h4>Classificação:</h4>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
								<a href="/evento/classificar/{{$evento->id}}" class="btn btn-success btn-app">
									<i class="fa fa-sort"></i>
									Classificar Evento
								</a>
								<a href="{{url("/evento/".$evento->id."/toggleresultados")}}" class="btn btn-warning btn-app">
									@if($evento->mostrar_resultados)
										<i class="fa fa-lock"></i>
										Restringir (Status Atual: Liberado)
									@else
										<i class="fa fa-unlock"></i>
										Liberar (Status Atual: Restringido)
									@endif
									Classificação Pública
								</a>
								@if($evento->mostrar_resultados)
									<a href="{{url("/evento/classificacao/".$evento->id)}}" class="btn btn-app">
										<i class="fa fa-eye"></i>
										Visualizar Classificação (Pública)
									</a>
								@endif
							@endif
							<a href="{{url("/evento/classificacao/".$evento->id)}}/interno" class="btn btn-app">
								<i class="fa fa-eye"></i>
								Visualizar Classificação (Interna)
							</a>
							<hr/>
							<h4>Inscritos:</h4>
							<a href="{{url("/evento/".$evento->id)}}/inscricoes/list" class="btn btn-app">
								<i class="fa fa-list"></i>
								Visualizar Lista de Inscritos (Completa)
							</a>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
								<hr/>
								<h4>Lista de Rating:</h4>
								<a href="{{url("/evento/".$evento->id)}}/enxadristas/sm" class="btn btn-app" target="_blank">
									<i class="fa fa-download"></i>
									Baixar para Uso neste Evento (Swiss-Manager)
								</a>

                                @if($evento->tipo_rating)
                                    <hr/>
                                    <h4>Rating:</h4>
                                    <a href="{{url("/evento/".$evento->id)}}/togglerating" class="btn btn-app">
                                        @if($evento->is_rating_calculate_enabled)
                                            <i class="fa fa-lock"></i>
                                            Não Permitir o Cálculo do Rating Interno (Status Atual: Permitido)
                                        @else
                                            <i class="fa fa-unlock"></i>
                                            Permitir o Cálculo do Rating Interno (Status Atual: Não Permitido)
                                        @endif
                                    </a>
                                    @if($evento->is_rating_calculate_enabled)
                                        @if ($evento->consegueCalcularRating() == 0)
                                            <button type="button" class="btn btn-app disabled" disabled>
                                                <i class="fa fa-calculator"></i>
                                                Calcular Rating (Não foi importado o emparceiramento)
                                            </button>
                                        @else
                                            <a href="{{url("/evento/".$evento->id)}}/rating/calculate" class="btn btn-app">
                                                <i class="fa fa-calculator"></i>
                                                Calcular Rating
                                            </a>
                                        @endif
                                    @endif
                                @endif

							@endif

							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
								<hr/>
								<h4>Demais Funções:</h4>
								<a href="{{url("/evento/".$evento->id."/toggleclassificavel")}}" class="btn btn-app">
									@if($evento->classificavel)
										<i class="fa fa-lock"></i>
										Não Permitir (Status Atual: Permitido)
									@else
										<i class="fa fa-unlock"></i>
										Permitir (Status Atual: Não Permitido)
									@endif
									Classificação Geral deste Evento
								</a>
								<a href="{{url("/evento/".$evento->id."/togglemanual")}}" class="btn btn-app">
									@if($evento->e_resultados_manuais)
										<i class="fa fa-lock"></i>
									@else
										<i class="fa fa-lock"></i>
									@endif
									Resultados @if($evento->e_resultados_manuais) Automáticos  (Status Atual: Manuais) @else Manuais  (Status Atual: Automáticos) @endif
								</a>
							@endif<br/><br/>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
                                @if($evento->evento_classificador_id > 0)
                                    <h4>Funções de Evento que possui Classificador:</h4>
                                    <a href="{{url("/evento/".$evento->id."/gerenciamento/torneio_3/import")}}" class="btn btn-app">
                                        <i class="fa fa-upload"></i>
                                        Importar Inscrições do Evento Classificador
                                    </a>
                                    <a href="{{url("/evento/".$evento->id."/gerenciamento/torneio_3/removeAll")}}" class="btn btn-app">
                                        <i class="fa fa-times"></i>
                                        Remove todas as Inscrições do Evento
                                    </a>
                                @else
                                    @if($evento->grupo_evento_classificador_id > 0)
                                        <h4>Funções de Evento que possui Grupo de Evento Classificador:</h4>
                                        <a href="{{url("/evento/".$evento->id."/gerenciamento/import")}}" class="btn btn-app">
                                            <i class="fa fa-upload"></i>
                                            Importar Inscrições do Grupo de Evento Classificador
                                        </a>
                                        <a href="{{url("/evento/".$evento->id."/gerenciamento/removeAll")}}" class="btn btn-app">
                                            <i class="fa fa-times"></i>
                                            Remove todas as Inscrições do Evento
                                        </a>

                                    @endif
                                @endif
                            @endif
                            <br/><br/>
                            <hr/>
                            <h4>Relatórios:</h4>
                            <a href="{{url("/evento/".$evento->id."/relatorios/premiados")}}" class="btn btn-app">
                                <i class="fa fa-file"></i>
                                Enxadristas Premiados neste Evento
                            </a>
						</div>
						<!-- /.box-body -->
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="editar_evento">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary" id="inscricao">
						<div class="box-header">
							<h3 class="box-title">Editar Evento</h3>
						</div>
						<!-- form start -->

					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<form method="post">
					@endif
							<div class="box-body">
								<div class="form-group">
									<label for="evento_name">Nome *</label>
									<input name="name" id="evento_name" class="form-control" type="text" value="{{$evento->name}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_inicio">Data de Início *</label>
									<input name="data_inicio" id="evento_data_inicio" class="form-control" type="text" value="{{$evento->getDataInicio()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_fim">Data de Fim *</label>
									<input name="data_fim" id="evento_data_fim" class="form-control" type="text" value="{{$evento->getDataFim()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="tipo_modalidade">Tipo de Modalidade *</label>
									<select name="tipo_modalidade" id="tipo_modalidade" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de modalidade ---</option>
										<option value="0">Convencional</option>
										<option value="1">Rápido</option>
										<option value="2">Relâmpago</option>
									</select>
								</div>
								<div class="form-group">
									<label for="exportacao_sm_modelo">Tipo de Exportação - Swiss Manager *</label>
									<select name="exportacao_sm_modelo" id="exportacao_sm_modelo" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="0">Padrão XadrezSuíço</option>
										<option value="1">FIDE</option>
										<option value="2">LBX</option>
										<option value="3">Padrão XadrezSuíço (Nome no Sobrenome, e Sobrenome no Nome)</option>
									</select>
								</div>
								<div class="form-group">
									<label for="cidade_id">Cidade *</label>
									<select name="cidade_id" id="cidade_id" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Selecione ---</option>
										@foreach($cidades as $cidade)
											<option value="{{$cidade->id}}">{{$cidade->id}} - {{$cidade->getName()}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="evento_local">Local *</label>
									<input name="local" id="evento_local" class="form-control" type="text" value="{{$evento->local}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_link">Link</label>
									<input name="link" id="evento_link" class="form-control" type="text" value="{{$evento->link}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_limite_inscricoes_abertas">Data e Hora Limite para Inscrições</label>
									<input name="data_limite_inscricoes_abertas" id="evento_data_limite_inscricoes_abertas" class="form-control" type="text" value="{{$evento->getDataFimInscricoesOnline()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="maximo_inscricoes_evento">Número Máximo de Inscrições Permitidas no Evento</label>
									<input name="maximo_inscricoes_evento" id="maximo_inscricoes_evento" class="form-control" type="text" value="{{$evento->maximo_inscricoes_evento}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="orientacao_pos_inscricao">Orientações Pós-Inscrição</label>
									<textarea class="form-control" id="orientacao_pos_inscricao" name="orientacao_pos_inscricao" placeholder="Texto com as Orientações Pós-Inscrição" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >{{$evento->orientacao_pos_inscricao}}</textarea>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="e_permite_visualizar_lista_inscritos_publica" name="e_permite_visualizar_lista_inscritos_publica" @if($evento->e_permite_visualizar_lista_inscritos_publica) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Permite a visualização da lista de inscrições de forma pública?</label>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="e_inscricao_apenas_com_link" name="e_inscricao_apenas_com_link" @if($evento->e_inscricao_apenas_com_link) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > As inscrições deste evento deverão ser feitas apenas pelo link divulgado (Inscrição Privada)</label>
								</div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="usa_fide" name="usa_fide" @if($evento->usa_fide) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Utiliza Rating FIDE?</label>
                                </div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="calcula_fide" name="calcula_fide" @if($evento->calcula_fide) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Calcula Rating FIDE?</label>
                                </div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="fide_required" name="fide_required" @if($evento->fide_required) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Não Calcula Rating FIDE mas Obriga ID FIDE?</label>
                                </div>
                                @if(!$evento->tipo_rating)
                                    <div class="form-group">
                                        <label><input type="checkbox" id="usa_cbx" name="usa_cbx" @if($evento->usa_cbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Utiliza Rating CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="calcula_cbx" name="calcula_cbx" @if($evento->calcula_cbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Calcula Rating CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="cbx_required" name="cbx_required" @if($evento->cbx_required) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Não Calcula Rating CBX mas Obriga ID CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="usa_lbx" name="usa_lbx" @if($evento->usa_lbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Utiliza Rating LBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess" name="is_lichess" @if($evento->is_lichess) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Lichess.org</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess_integration" name="is_lichess_integration" @if($evento->is_lichess_integration) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Usa Integração com o Lichess.org</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="lichess_team_id">Lichess.org: ID do Time/Equipe</label>
                                        <input name="lichess_team_id" id="lichess_team_id" class="form-control" type="text" value="{{$evento->lichess_team_id}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                        <small><strong>Importante!</strong> Aqui vai o ID do Time no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões. Segue exemplo de onde encontrar o ID do Time: https://lichess.org/team/<strong>circuito-sesc-de-xadrez-2021</strong></small>
                                        @if($evento->lichess_team_id) <br/><a href="https://lichess.org/team/{{$evento->lichess_team_id}}" target="_blank">Link do Time</a> @endif
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
                                        <div class="form-group">
                                            <label for="lichess_team_password">Lichess.org: Senha para a Entrada no Time/Equipe</label>
                                            <input name="lichess_team_password" id="lichess_team_password" class="form-control" type="text" value="{{$evento->lichess_team_password}}" />
                                            <small><strong>Importante!</strong> Aqui vai a Senha para a Entrada no Time no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões.</small>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="lichess_tournament_id">Lichess.org: ID do Torneio</label>
                                        <input name="lichess_tournament_id" id="lichess_tournament_id" class="form-control" type="text" value="{{$evento->lichess_tournament_id}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                        <small><strong>Importante!</strong> Aqui vai o ID do Torneio no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões. Segue exemplo de onde encontrar o ID do Torneio: https://lichess.org/swiss/<strong>ZDig8Z5Y</strong></small>
                                        @if($evento->lichess_tournament_id) <br/><a href="https://lichess.org/swiss/{{$evento->lichess_tournament_id}}" target="_blank">Link do Torneio</a> @endif
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
                                        <div class="form-group">
                                            <label for="lichess_tournament_password">Lichess.org: Senha para a Inscrição no Torneio</label>
                                            <input name="lichess_tournament_password" id="lichess_tournament_password" class="form-control" type="text" value="{{$evento->lichess_tournament_password}}" />
                                            <small><strong>Importante!</strong> Aqui vai a Senha para a Inscrição no Torneio no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões.</small>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_chess_com" name="is_chess_com" @if($evento->is_chess_com) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Chess.com</label>
                                    </div>
                                @endif
								<hr/>
								<div class="form-group">
									<label for="tipo_ratings_id">Tipo de Rating</label>
									<select name="tipo_ratings_id" id="tipo_ratings_id" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de rating ---</option>
										@foreach($tipos_rating as $tipo_rating)
											<option value="{{$tipo_rating->id}}">{{$tipo_rating->id}} - {{$tipo_rating->name}}</option>
										@endforeach
									</select>
								</div>

                                @if($evento->grupo_evento->classificador)
                                    <div class="form-group">
                                        <label for="evento_classificador_id">Evento Classificador ({{$evento->grupo_evento->classificador->name}})</label>
                                        <select name="evento_classificador_id" id="evento_classificador_id" class="form-control width-100">
                                            <option value="">--- Você pode selecionar um evento ---</option>
                                            @foreach($evento->grupo_evento->classificador->eventos->all() as $ec)
                                                <option value="{{$ec->id}}">{{$ec->id}} - {{$ec->name}}</option>
                                            @endforeach
                                        </select>
                                        <small><strong>IMPORTANTE!</strong> Essa configuração serve para caso tenha um grupo de evento que classifica para este grupo de evento. Aqui vai o Evento Classificador que classifica para este Evento.</small>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label for="grupo_evento_classificador_id">Grupo de Evento Classificador</label>
                                        <select name="grupo_evento_classificador_id" id="grupo_evento_classificador_id" class="form-control width-100">
                                            <option value="">--- Você pode selecionar um grupo de evento ---</option>
                                            @foreach($evento->grupo_evento->all() as $gec)
                                                <option value="{{$gec->id}}">{{$gec->id}} - {{$gec->name}}</option>
                                            @endforeach
                                        </select>
                                        <small><strong>IMPORTANTE!</strong> Essa configuração serve para caso tenha um grupo de evento que classifica para este grupo de evento.</small>
                                    </div>
                                @endif
                                <hr/>
                                <div class="form-group">
                                    <label><input type="checkbox" id="e_permite_confirmacao_publica" name="e_permite_confirmacao_publica" @if($evento->e_permite_confirmacao_publica) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Permite Confirmação Pública</label>
                                </div>
								<div class="form-group">
									<label for="confirmacao_publica_inicio">Confirmações: Data e Hora Inicial</label>
									<input name="confirmacao_publica_inicio" id="confirmacao_publica_inicio" class="form-control" type="text" value="{{$evento->getConfirmacoesDataInicial()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                    <small><strong>IMPORTANTE!</strong> Os campos de Data e Hora Inicial e Data e Hora Final devem estar preenchidos para que a confirmação pública fique disponível.</small>
                                </div>
								<div class="form-group">
									<label for="confirmacao_publica_final">Confirmações: Data e Hora Final</label>
									<input name="confirmacao_publica_final" id="confirmacao_publica_final" class="form-control" type="text" value="{{$evento->getConfirmacoesDataFim()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                    <small><strong>IMPORTANTE!</strong> Os campos de Data e Hora Inicial e Data e Hora Final devem estar preenchidos para que a confirmação pública fique disponível.</small>
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						</form>
					@endif
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="pagina">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Aviso!</strong><br/>
						Caso queira personalizar a página de inscrição do evento, será possível adicionar um texto e também uma imagem, não sendo itens obrigatórios.
					</div>
					<div class="box box-primary" id="inscricao">
						<div class="box-header">
							<h3 class="box-title">Página</h3>
						</div>
						<!-- form start -->

					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<form method="post" action="{{url("/evento/".$evento->id."/pagina")}}" enctype="multipart/form-data">
					@endif
							<div class="box-body">
								<div class="form-group">
									<label for="imagem">Imagem</label>
									@if($evento->pagina)
										@if($evento->pagina->imagem)
											<br/><img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 400px"/>
											@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
												<label><input type="checkbox" name="remover_imagem" /> Remover Imagem?</label>
											@endif
										@endif
									@endif
									@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
										<input type="file" id="imagem" name="imagem">
										@if ($errors->has('imagem'))
											<span class="help-block">
												<strong>{{ $errors->first('imagem') }}</strong>
											</span>
										@endif
									@endif
								</div>

								<div class="form-group">
									<label for="texto">Texto</label>
									<textarea class="form-control" id="texto" name="texto" placeholder="Texto sobre o Evento" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >@if($evento->pagina){{$evento->pagina->texto}}@endif</textarea>
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						</form>
					@endif
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="criterio_desempate">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Lembre-se que, o <b>Grupo de Evento</b> poderá possuir critérios de desempate também.<br/>
						Caso você escolha um ou mais critérios nesta tela, os critérios de desempate do Grupo de Evento <strong>serão desconsiderados!</strong>
					</div>
				</section>
				<br/>
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
				)
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Relacionar Critério de Desempate</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/criteriodesempate/add")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="criterio_desempate_id">Critério de Desempate</label>
										<select name="criterio_desempate_id" id="criterio_desempate_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($criterios_desempate as $criterio_desempate)
												<option value="{{$criterio_desempate->id}}">{{$criterio_desempate->id}} - {{$criterio_desempate->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="tipo_torneio_id">Tipo de Torneio</label>
										<select name="tipo_torneio_id" id="tipo_torneio_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($tipos_torneio as $tipo_torneio)
												<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->id}} - {{$tipo_torneio->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="softwares_id">Software</label>
										<select name="softwares_id" id="softwares_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($softwares as $software)
												<option value="{{$software->id}}">{{$software->id}} - {{$software->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="prioridade">Prioridade</label>
										<input name="prioridade" id="prioridade" class="form-control" type="number" />
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					</section>
				@endif
				<section class="col-lg-6 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Critérios de Desempate</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_criterio_desempate" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Tipo de Torneio</th>
											<th>Software</th>
											<th>Prior.</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->criterios()->orderBy("tipo_torneio_id","ASC")->orderBy("softwares_id","ASC")->orderBy("prioridade","ASC")->get() as $criterio_desempate)
											<tr>
												<td>{{$criterio_desempate->criterio->id}}</td>
												<td>{{$criterio_desempate->criterio->name}}</td>
												<td>{{$criterio_desempate->tipo_torneio->name}}</td>
												<td>{{$criterio_desempate->software->name}}</td>
												<td>{{$criterio_desempate->prioridade}}</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/criteriodesempate/remove/".$criterio_desempate->id)}}" role="button"><i class="fa fa-times"></i></a>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>

			<div role="tabpanel" class="tab-pane" id="categoria">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-danger alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Esta aba se destina apenas ao <strong>caso de necessitar de uma categoria específica para este evento</strong>. As categorias aqui cadastradas <strong>não serão replicadas</strong> a qualquer outro Evento ou então Grupo de Evento.<br/>
						Obs: O cadastro da categoria aqui <strong>não retira a necessidade de efetuar a relação</strong> da mesma na aba "Categorias Relacionadas".
					</div>
				</section>
				<br/>
				<section class="col-lg-12 connectedSortable">
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Nova Categoria</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/categorias/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="name">Nome</label>
										<input name="name" id="name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="name">Idade Mínima (Em anos)</label>
										<input name="idade_minima" id="idade_minima" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="name">Idade Máxima (Em anos)</label>
										<input name="idade_maxima" id="idade_maxima" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="name">Código Categoria (Padrão Swiss-Manager)</label>
										<input name="cat_code" id="cat_code" class="form-control" type="text" />
										<small>Exemplo: Para Sub-08, utilizar <strong>U08</strong>.</small>
									</div>
									<div class="form-group">
										<label for="name">Código Grupo (Deve ser único em cada evento, para evitar problemas de processamento do resultado)</label>
										<input name="code" id="code" class="form-control" type="text" />
										<small>Este código pode ser diferente de acordo com a sua forma de controle. Mas vale saber: é esta a informação que será utilizada para identificação da categoria quando ocorrer o processamento do resultado, e por isso é importante que esteja preenchida no Swiss-Manager e também que seja única para cada categoria.</small>
									</div>
									<div class="form-group">
										<label><input type="checkbox" id="nao_classificar" name="nao_classificar"> Não Classificar Categoria</label>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					@endif
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Categorias</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Classificar?</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->categorias_cadastradas->all() as $categoria)
											<tr>
												<td>{{$categoria->id}}</td>
												<td>{{$categoria->name}}</td>
												<td>@if(!$categoria->nao_classificar) Sim @else Não @endif</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/categorias/dashboard/".$categoria->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($categoria->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/categorias/delete/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="categorias_relacionadas">
				<section class="col-lg-12 connectedSortable">
				<br/>
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
				)
					<div class="box box-primary collapsed-box">
						<div class="box-header">
							<h3 class="box-title">Nova Relação de Categoria</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
						</div>
						<!-- form start -->
						<form method="post" action="{{url("/evento/".$evento->id."/categoria/add")}}">
							<div class="box-body">
								<div class="form-group">
									<label for="categoria_id">Categoria</label>
									<select name="categoria_id" id="categoria_id" class="form-control width-100">
										<option value="">--- Selecione ---</option>
										@foreach($categorias as $categoria)
											<option value="{{$categoria->id}}">{{$categoria->id}} - {{$categoria->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
						</form>
					</div>
				@endif
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Categorias Relacionadas</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Vínculo Principal</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->categorias->all() as $categoria)
											<tr>
												<td>{{$categoria->categoria->id}}</td>
												<td>{{$categoria->categoria->name}}</td>
												<td>
													@if($categoria->categoria->grupo_evento_id)
														Grupo de Evento: #{{$categoria->categoria->grupo_evento->id}} - {{$categoria->categoria->grupo_evento->name}}
													@else
														@if($categoria->categoria->evento_id)
															Evento: #{{$categoria->categoria->evento->id}} - {{$categoria->categoria->evento->name}}
														@else
															Estou Confuso. Não há vinculo.
														@endif
													@endif
												</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/categoria/remove/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="torneio">
				<br/>
				@if(
					(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
				)
					<section class="col-lg-12 connectedSortable">

						<!-- Torneio -->
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Novo Torneio</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/torneios/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="name">Nome</label>
										<input name="name" id="name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="tipo_torneio_id">Tipo de Torneio</label>
										<select id="tipo_torneio_id" name="tipo_torneio_id" class="form-control">
											<option value="">-- Selecione --</option>
											@foreach(\App\TipoTorneio::all() as $tipo_torneio)
												<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="softwares_id">Software</label>
										<select id="torneio_softwares_id" name="softwares_id" class="form-control">
											<option value="">-- Selecione --</option>
											@foreach(\App\Software::all() as $software)
												<option value="{{$software->id}}">{{$software->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					</section>
				@endif
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Torneios</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_torneio" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Categorias</th>
											<th>Inscritos</th>
											<th>Resultados Importados?</th>
											<th>Tipo de Torneio</th>
											<th>Template de Torneio</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->torneios->all() as $torneio)
											<tr>
												<td>{{$torneio->id}}</td>
												<td>{{$torneio->name}}</td>
												<td>
													@foreach($torneio->categorias->all() as $categoria)
														{{$categoria->categoria->name}},
													@endforeach
												</td>
												<td>
                                                    Total de Inscritos: {{$torneio->getCountInscritos()}}<br/>
                                                    @if(
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                                    )
                                                        Confirmados: {{$torneio->getCountInscritosConfirmados()}}<br/>
                                                        Presentes: {{$torneio->quantosInscritosPresentes()}}
                                                        <hr/>
                                                        @if($evento->is_lichess_integration)
                                                            <strong>Torneio Lichess.org</strong><br/>
                                                            Inscritos: <strong>{{$torneio->getCountLichessConfirmadosnoTorneio()}}</strong><br/>
                                                            Não Inscritos: <strong>{{$torneio->getCountInscritos() - $torneio->getCountLichessConfirmadosnoTorneio()}}</strong>
                                                        @endif
                                                    @endif
												</td>
												<td>
                                                    {{$torneio->getIsResultadosImportados()}}
												</td>
												<td>
													{{$torneio->tipo_torneio->name}}
												</td>
												<td>
													@if($torneio->template)
														{{$torneio->template->name}}
													@else
														-
													@endif
												</td>
												<td>

													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/edit/".$torneio->id)}}" role="button">Editar</a>
														@if($torneio->tipo_torneio->id != 3)  <a class="btn btn-sm btn-warning" href="{{url("/evento/".$evento->id."/torneios/union/".$torneio->id)}}" role="button">Unir Torneios</a><br/> @endif
													@endif
													<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes")}}" role="button">Inscrições</a>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														@if(!$evento->e_resultados_manuais) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/resultados/file")}}" role="button">Resultados</a><br/> @endif
														@if(!$evento->e_resultados_manuais) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/emparceiramentos")}}" role="button">Emparceiramentos</a><br/> @endif

														@if($torneio->tipo_torneio->id == 3) <a class="btn btn-block btn-lg btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3")}}" role="button">Gerenciamento do Torneio</a><br/> @endif

                                                        <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm")}}" role="button" target="_blank">Baixar Inscrições Confirmadas</a><br/>
														<a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/all")}}" role="button" target="_blank">Baixar Todas as Inscrições</a><br/>
													@endif
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes")}}" role="button" target="_blank">Imprimir Inscrições</a><br/>
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético)</a><br/>
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico/cidade")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético por Cidade/Clube)</a><br/>
													@if($torneio->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/delete/".$torneio->id)}}" role="button">Apagar</a> @endif
                                                    @if($torneio->evento->is_lichess_integration)
                                                        <hr/>
                                                        <strong>Opções Lichess.org</strong><br/>
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/check_players_in")}}" role="button">Conferir Inscrições no Torneio do Lichess.org</a><br/>
                                                        Última Atualização: {{$torneio->getLastLichessPlayersUpdate()}}<br/>
														@if($torneio->evento->data_inicio <= date("Y-m-d")) <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/get_results")}}" role="button">Inserir Resultados do Torneio do Lichess.org</a><br/> @endif
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/remove_lichess_players_not_found")}}" role="button">REMOVER os Players do Lichess.org que NÃO foram encontrados</a><br/>
                                                    @endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>

			<div role="tabpanel" class="tab-pane" id="campo_personalizado">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Caso necessite de alguma informação adicional para este evento, você pode criar um campo personalizado para o Evento, porém, se esta informação é necessária a todos os eventos do Grupo de Evento, o correto é criar um campo personalizado para o Grupo de Evento.
					</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
					    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Novo Campo Personalizado</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/campos/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="campo_name">Nome *</label>
										<input name="name" id="campo_name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="campo_question">Questão *</label>
										<input name="question" id="campo_question" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="campo_type">Tipo de Campo *</label>
										<select name="type" id="campo_type" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif>
											<option value="">--- Selecione um tipo de campo ---</option>
											<option value="select">Seleção</option>
										</select>
									</div>
									<div class="form-group">
										<label for="campo_validator">Validação</label>
										<select name="validator" id="campo_validator" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif>
											<option value="">--- Você pode selecionar uma validação ---</option>
											<option value="cpf">CPF</option>
										</select>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					@endif

					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Campos Personalizados Adicionais</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Questão</th>
											<th>Ativo?</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->campos_adicionais->all() as $campo)
											<tr>
												<td>{{$campo->id}}</td>
												<td>{{$campo->name}}</td>
												<td>{{$campo->question}}</td>
												<td>@if($campo->is_active) Sim @else Não @endif</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/campos/dashboard/".$campo->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($campo->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/campos/delete/".$campo->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>
            <div role="tabpanel" class="tab-pane" id="email_template">
                <br/>
                <section class="col-lg-12 connectedSortable">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Templates de E-mail</h3>
                        </div>
                        <!-- form start -->
                            <div class="box-body">
                                <table id="tabela_email_templates" class="table-responsive table-condensed table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th>Assunto do E-mail</th>
                                            <th width="20%">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->email_templates->all() as $template)
                                            <tr>
                                                <td>{{$template->id}}</td>
                                                <td>{{$template->name}}</td>
                                                <td>{{$template->subject}}</td>
                                                <td>
                                                    <a class="btn btn-default" href="{{url("/emailtemplate/edit/".$template->id)}}" role="button">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                    </div>
                </section>
            </div>
		</div>

	</div>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript" src="{{url("/vendor/bower/ckeditor/ckeditor.js")}}"></script>
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
    	CKEDITOR.replace('texto');
    	CKEDITOR.replace('orientacao_pos_inscricao');
		$("#torneio_template_id").select2();
		$("#tipo_modalidade").select2();
		$("#exportacao_sm_modelo").select2();
		$("#categoria_id").select2();
		$("#criterio_desempate_id").select2();
		$("#criterio_desempate_geral_id").select2();
		$("#tipo_torneio_id").select2();
		$("#torneio_softwares_id").select2();
		$("#tipo_ratings_id").select2();
		$("#cidade_id").select2();
		$("#cidade_id").val([{{$evento->cidade_id}}]).change();
		$("#tipo_modalidade").val([{{$evento->tipo_modalidade}}]).change();
		$("#exportacao_sm_modelo").val([{{$evento->exportacao_sm_modelo}}]).change();
		@if($evento->tipo_rating)
			$("#tipo_ratings_id").val([{{$evento->tipo_rating->tipo_ratings_id}}]).change();
		@endif

        @if($evento->classificador)
		    $("#evento_classificador_id").select2();
			$("#evento_classificador_id").val([{{$evento->classificador->id}}]).change();
        @endif
        @if($evento->grupo_evento_classificador)
		    $("#grupo_evento_classificador_id").select2();
			$("#grupo_evento_classificador_id").val([{{$evento->grupo_evento_classificador->id}}]).change();
        @endif
		$("#tabela_torneio").DataTable({
			responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
		});
		$("#tabela_categoria").DataTable({
				responsive: true,
		});
		$("#tabela_criterio_desempate").DataTable({
				responsive: true,
				"ordering": false,
		});
		$("#tabela_criterio_desempate_geral").DataTable({
				responsive: true,
				"ordering": false,
		});
		$("#tabela_pontuacao").DataTable({
				responsive: true,
				"ordering": false,
		});
		setTimeout(function(){
			$(".select2").css("width","100%");
		},"1000");
		@if($tab)
			$("#tab_{{$tab}}").tab("show");
		@endif
		$("#evento_data_inicio").mask("00/00/0000");
		$("#evento_data_fim").mask("00/00/0000");
		$("#evento_data_limite_inscricoes_abertas").mask("00/00/0000 00:00");
		$("#confirmacao_publica_inicio").mask("00/00/0000 00:00");
		$("#confirmacao_publica_final").mask("00/00/0000 00:00");
  });
</script>
@endsection

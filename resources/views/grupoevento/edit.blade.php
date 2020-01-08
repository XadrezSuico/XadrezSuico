@extends('adminlte::page')

@section("title", "Dashboard de Grupo de Evento")

@section('content_header')
  <h1>Dashboard de Grupo de Evento: {{$grupo_evento->name}}</h1>
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
  <li role="presentation"><a href="/grupoevento">Voltar a Lista de Grupos de Evento</a></li>
  @if($user->hasPermissionGlobal()) <li role="presentation"><a href="/grupoevento/new">Novo Grupo de Evento</a></li>
  <li role="presentation"><a href="/grupoevento/classificar/{{$grupo_evento->id}}">Classificar Grupo de Evento</a></li>@endif
  <li role="presentation"><a href="/grupoevento/classificacao/{{$grupo_evento->id}}">Visualizar Classificação Pública</a></li>
</ul>
<div class="row">
  <!-- Left col -->
	<div>

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a id="tab_editar_evento" href="#editar_evento" aria-controls="editar_evento" role="tab" data-toggle="tab">Editar Grupo de Evento</a></li>
			<li role="presentation"><a id="tab_evento" href="#evento" aria-controls="evento" role="tab" data-toggle="tab">Eventos</a></li>
			@if($user->hasPermissionGlobal())			
				<li role="presentation"><a id="tab_template_torneio" href="#template_torneio" aria-controls="template_torneio" role="tab" data-toggle="tab">Template de Torneio</a></li>
				<li role="presentation"><a id="tab_criterio_desempate" href="#criterio_desempate" aria-controls="criterio_desempate" role="tab" data-toggle="tab">Critério de Desempate</a></li>
				<li role="presentation"><a id="tab_criterio_desempate_geral" href="#criterio_desempate_geral" aria-controls="criterio_desempate_geral" role="tab" data-toggle="tab">Critério de Desempate Geral</a></li>
				<li role="presentation"><a id="tab_categoria" href="#categoria" aria-controls="categoria" role="tab" data-toggle="tab">Categoria</a></li>
				<li role="presentation"><a id="tab_pontuacao" href="#pontuacao" aria-controls="pontuacao" role="tab" data-toggle="tab">Pontuação</a></li>
				<li role="presentation"><a id="tab_campo_personalizado" href="#campo_personalizado" aria-controls="campo_personalizado" role="tab" data-toggle="tab">Campo Personalizado</a></li>
			@endif
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="editar_evento">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary" id="inscricao">
						<div class="box-header">
							<h3 class="box-title">Editar Grupo de Evento</h3>
						</div>
						<!-- form start -->
						<form method="post">
							<div class="box-body">
								<div class="form-group">
									<label for="name">Nome</label>
									<input name="name" id="name" class="form-control" type="text" value="{{$grupo_evento->name}}" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="limite_calculo_geral">Limite de Valores para Cálculo de Pontuação Geral</label>
									<input name="limite_calculo_geral" id="limite_calculo_geral" class="form-control" type="text" value="{{$grupo_evento->limite_calculo_geral}}" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="tipo_ratings_id">Tipo de Rating</label>
									<select name="tipo_ratings_id" id="tipo_ratings_id" class="form-control width-100" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de rating ---</option>
										@foreach($tipos_rating as $tipo_rating)
											<option value="{{$tipo_rating->id}}">{{$tipo_rating->id}} - {{$tipo_rating->name}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="e_pontuacao_resultado_para_geral" name="e_pontuacao_resultado_para_geral" @if($grupo_evento->e_pontuacao_resultado_para_geral) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif > A pontuação do enxadrista será composta pelos seus resultados?</label>
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif>Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
						</form>
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="evento">
				<br/>
				@if($user->hasPermissionGlobal())
					<section class="col-md-6 col-lg-4 connectedSortable">
						<!-- Evento -->
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Criar Evento</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/evento/new")}}">
								<div class="box-body">
									<div class="form-group">
										<div class="form-group">
											<label for="evento_name">Nome *</label>
											<input name="name" id="evento_name" class="form-control" type="text" />
										</div>
										<div class="form-group">
											<label for="evento_data_inicio">Data de Início *</label>
											<input name="data_inicio" id="evento_data_inicio" class="form-control" type="text" />
										</div>
										<div class="form-group">
											<label for="evento_data_fim">Data de Fim *</label>
											<input name="data_fim" id="evento_data_fim" class="form-control" type="text" />
										</div>
										<div class="form-group">
											<label for="evento_cidade_id">Cidade *</label>
											<select name="cidade_id" id="evento_cidade_id" class="form-control width-100">
												<option value="">--- Selecione ---</option>
												@foreach($cidades as $cidade)
													<option value="{{$cidade->id}}">{{$cidade->id}} - {{$cidade->name}}</option>
												@endforeach
											</select>
										</div>
										<div class="form-group">
											<label for="evento_local">Local *</label>
											<input name="local" id="evento_local" class="form-control" type="text" />
										</div>
										<div class="form-group">
											<label for="evento_link">Link</label>
											<input name="link" id="evento_link" class="form-control" type="text" />
										</div>
										<div class="form-group">
											<label for="evento_data_limite_inscricoes_abertas">Data e Hora Limite para Inscrições</label>
											<input name="data_limite_inscricoes_abertas" id="evento_data_limite_inscricoes_abertas" class="form-control" type="text" />
										</div>
										<div class="form-group">
											<label><input type="checkbox" id="usa_cbx" name="usa_cbx"> Utiliza Rating CBX?</label>
										</div>
										<div class="form-group">
											<label><input type="checkbox" id="usa_fide" name="usa_fide"> Utiliza Rating FIDE?</label>
										</div>
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
				<section class="col-md-6 col-lg-8 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Eventos</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_evento" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Período</th>
											<th>Local</th>
											<th>Inscritos</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($grupo_evento->eventos->all() as $evento)
											@if(
												\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
												\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4,5]) ||
												\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
											)
												<tr>
													<td>{{$evento->id}}</td>
													<td>{{$evento->name}}</td>
													<td>{{$evento->getDataInicio()}}<br/>{{$evento->getDataFim()}}</td>
													<td>{{$evento->cidade->name}} - {{$evento->local}}</td>
													<td>
														Total: {{$evento->quantosInscritos()}}<br/>
														Confirmados: {{$evento->quantosInscritosConfirmados()}}<br/>
                                    					Presentes: {{$evento->quantosInscritosPresentes()}}
													</td>
													<td>
													    @if(
															\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
															\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4]) ||
															\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
														)
															<a class="btn btn-default" href="{{url("/evento/dashboard/".$evento->id)}}" role="button">Dashboard</a>
														@endif
														@if(
															\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
															\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5])
														)
															<a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id)}}" role="button">Nova Inscrição</a>
															<a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id."/confirmacao")}}" role="button">Confirmar Inscrição</a>
														@endif
														@if($evento->isDeletavel() && \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) <a class="btn btn-danger" href="{{url("/evento/delete/".$evento->id)}}" role="button">Apagar</a> @endif
													</td>
												</tr>
											@endif
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>	
			</div>
			@if($user->hasPermissionGlobal())			
				<div role="tabpanel" class="tab-pane" id="template_torneio">
					<br/>
					<section class="col-lg-12 connectedSortable">
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Novo Template de Torneio</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/torneiotemplates/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="name">Nome</label>
										<input name="name" id="name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="torneio_name">Nome do Torneio</label>
										<input name="torneio_name" id="torneio_name" class="form-control" type="text" />
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Templates de Torneio</h3>
							</div>
							<!-- form start -->
								<div class="box-body">
									<table id="tabela_torneio_template" class="table-responsive table-condensed table-striped" style="width: 100%">
										<thead>
											<tr>
												<th>#</th>
												<th>Nome</th>
												<th width="20%">Opções</th>
											</tr>
										</thead>
										<tbody>
											@foreach($grupo_evento->torneios_template->all() as $torneio_template)
												<tr>
													<td>{{$torneio_template->id}}</td>
													<td>{{$torneio_template->name}}</td>
													<td>
														<a class="btn btn-success" href="{{url("/grupoevento/".$grupo_evento->id."/torneiotemplates/dashboard/".$torneio_template->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($torneio_template->isDeletavel())<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/torneiotemplates/delete/".$torneio_template->id)}}" role="button"><i class="fa fa-times"></i></a>@endif
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
				<div role="tabpanel" class="tab-pane" id="criterio_desempate">
					<br/>
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Relacionar Critério de Desempate</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/criteriodesempate/add")}}">
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
											@foreach($grupo_evento->criterios()->orderBy("tipo_torneio_id","ASC")->orderBy("softwares_id","ASC")->orderBy("prioridade","ASC")->get() as $criterio_desempate)
												<tr>
													<td>{{$criterio_desempate->criterio->id}}</td>
													<td>{{$criterio_desempate->criterio->name}}</td>
													<td>{{$criterio_desempate->tipo_torneio->name}}</td>
													<td>{{$criterio_desempate->software->name}}</td>
													<td>{{$criterio_desempate->prioridade}}</td>
													<td>
														<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/criteriodesempate/remove/".$criterio_desempate->id)}}" role="button"><i class="fa fa-times"></i></a>
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
				<div role="tabpanel" class="tab-pane" id="criterio_desempate_geral">
					<br/>
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Relacionar Critério de Desempate Geral</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/criteriodesempategeral/add")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="criterio_desempate_geral_id">Critério de Desempate</label>
										<select name="criterio_desempate_id" id="criterio_desempate_geral_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($criterios_desempate_geral as $criterio_desempate)
												<option value="{{$criterio_desempate->id}}">{{$criterio_desempate->id}} - {{$criterio_desempate->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="prioridade_geral">Prioridade</label>
										<input name="prioridade" id="prioridade_geral" class="form-control" type="number" />						
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
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Critérios de Desempate Geral</h3>
							</div>
							<!-- form start -->
								<div class="box-body">
									<table id="tabela_criterio_desempate_geral" class="table-responsive table-condensed table-striped" style="width: 100%">
										<thead>
											<tr>
												<th>#</th>
												<th>Nome</th>
												<th>Prior.</th>
												<th width="20%">Opções</th>
											</tr>
										</thead>
										<tbody>
											@foreach($grupo_evento->criterios_gerais()->orderBy("prioridade","ASC")->get() as $criterio_desempate)
												<tr>
													<td>{{$criterio_desempate->criterio->id}}</td>
													<td>{{$criterio_desempate->criterio->name}}</td>
													<td>{{$criterio_desempate->prioridade}}</td>
													<td>
														<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/criteriodesempategeral/remove/".$criterio_desempate->id)}}" role="button"><i class="fa fa-times"></i></a>
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
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Nova Categoria</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/categorias/new")}}">
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
											@foreach($grupo_evento->categorias->all() as $categoria)
												<tr>
													<td>{{$categoria->id}}</td>
													<td>{{$categoria->name}}</td>
													<td>@if(!$categoria->nao_classificar) Sim @else Não @endif</td>
													<td>
														<a class="btn btn-success" href="{{url("/grupoevento/".$grupo_evento->id."/categorias/dashboard/".$categoria->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($categoria->isDeletavel()) <a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/categorias/delete/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
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
				<div role="tabpanel" class="tab-pane" id="pontuacao">
					<br/>
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Relacionar Pontuação</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/pontuacao/add")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="posicao">Posição</label>
										<input name="posicao" id="posicao" class="form-control" type="number" />						
									</div>
									<div class="form-group">
										<label for="pontuacao">Pontuação</label>
										<input name="pontuacao" id="pontuacao" class="form-control" type="number" />						
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
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Pontuações por Posição</h3>
							</div>
							<!-- form start -->
								<div class="box-body">
									<table id="tabela_pontuacao" class="table-responsive table-condensed table-striped" style="width: 100%">
										<thead>
											<tr>
												<th>#</th>
												<th>Posição</th>
												<th>Pontuação</th>
												<th width="20%">Opções</th>
											</tr>
										</thead>
										<tbody>
											@foreach($grupo_evento->pontuacoes()->orderBy("posicao","ASC")->get() as $pontuacao)
												<tr>
													<td>{{$pontuacao->id}}</td>
													<td>{{$pontuacao->posicao}}</td>
													<td>{{$pontuacao->pontuacao}}</td>
													<td>
														<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/pontuacao/remove/".$pontuacao->id)}}" role="button"><i class="fa fa-times"></i></a>
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
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Novo Campo Personalizado</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/campos/new")}}">
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
										<select name="type" id="campo_type" class="form-control width-100" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif>
											<option value="">--- Selecione um tipo de campo ---</option>
											<option value="select">Seleção</option>
										</select>
									</div>
									<div class="form-group">
										<label for="campo_validator">Validação</label>
										<select name="validator" id="campo_validator" class="form-control width-100" @if(!$user->hasPermissionGlobal()) disabled="disabled" @endif>
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
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Campos Personalizados</h3>
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
											@foreach($grupo_evento->campos->all() as $campo)
												<tr>
													<td>{{$campo->id}}</td>
													<td>{{$campo->name}}</td>
													<td>{{$campo->question}}</td>
													<td>@if($campo->is_active) Sim @else Não @endif</td>
													<td>
														<a class="btn btn-success" href="{{url("/grupoevento/".$grupo_evento->id."/campos/dashboard/".$campo->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($campo->isDeletavel()) <a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/campos/delete/".$campo->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
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
			@endif
		</div>

	</div>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
		$("#torneio_template_id").select2();
		$("#categoria_id").select2();
		$("#criterio_desempate_id").select2();
		$("#criterio_desempate_geral_id").select2();
		$("#tipo_torneio_id").select2();
		$("#softwares_id").select2();
		$("#tipo_ratings_id").select2();
		$("#evento_cidade_id").select2();

		$("#campo_type").select2();
		$("#campo_validator").select2();

		@if($grupo_evento->tipo_rating)
			$("#tipo_ratings_id").val([{{$grupo_evento->tipo_rating->tipo_ratings_id}}]).change();
		@endif
		$("#tabela_torneio_template").DataTable({
				responsive: true,
		});
		$("#tabela_categoria").DataTable({
				responsive: true,
		});
		$("#tabela_evento").DataTable({
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
  });
</script>
@endsection

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
  <li role="presentation"><a href="/evento">Voltar a Lista de Eventos</a></li>
</ul>
<div class="row">
  <!-- Left col -->
	<div>

	<section class="col-lg-12 connectedSortable">
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Funções</h3>
			</div>
			
			<div class="box-body">
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5])
				)
					<a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id)}}" role="button">Nova Inscrição</a>
					<a class="btn btn-success" href="{{url("/evento/inscricao/".$evento->id."/confirmacao")}}" role="button">Confirmar Inscrição</a><br/><br/>
				@endif
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
				)
					<a href="/evento/classificar/{{$evento->id}}" class="btn btn-success">Classificar Evento</a><br/><br/>
					<a href="{{url("/evento/".$evento->id."/toggleresultados")}}" class="btn btn-warning">@if($evento->mostrar_resultados) Restringir @else Liberar @endif Classificação Pública</a>
					@if($evento->mostrar_resultados)
						<a href="{{url("/evento/classificacao/".$evento->id)}}" class="btn btn-default">Visualizar Classificação (Pública)</a>
					@endif
				@endif
				<a href="{{url("/evento/classificacao/".$evento->id)}}/interno" class="btn btn-default">Visualizar Classificação (Interna)</a><br/><br/>

				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()
				)
					<a href="{{url("/evento/".$evento->id."/toggleclassificavel")}}" class="btn btn-warning">Atual: @if($evento->classificavel) Permitir @else Não Permitir @endif Classificação Geral deste Evento</a>
					<a href="{{url("/evento/".$evento->id."/togglemanual")}}" class="btn btn-warning">Atual: Resultados @if($evento->e_resultados_manuais) Manuais @else Automáticos @endif</a>
				@endif<br/><br/>
				<a href="{{url("/inscricao/".$evento->id)}}" class="btn btn-default">Link para Inscrição Pública</a><br/><br/>

			</div>
			<!-- /.box-body -->
		</div>
	</section>

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a id="tab_editar_evento" href="#editar_evento" aria-controls="editar_evento" role="tab" data-toggle="tab">Editar Evento</a></li>
			<li role="presentation"><a id="tab_pagina" href="#pagina" aria-controls="pagina" role="tab" data-toggle="tab">Página</a></li>
			<li role="presentation"><a id="tab_criterio_desempate" href="#criterio_desempate" aria-controls="criterio_desempate" role="tab" data-toggle="tab">Critério de Desempate</a></li>
			<li role="presentation"><a id="tab_categoria" href="#categoria" aria-controls="categoria" role="tab" data-toggle="tab">Categoria</a></li>
			<li role="presentation"><a id="tab_torneio" href="#torneio" aria-controls="torneio" role="tab" data-toggle="tab">Torneios</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="editar_evento">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary" id="inscricao">
						<div class="box-header">
							<h3 class="box-title">Editar Evento</h3>
						</div>
						<!-- form start -->
						
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
					)
						<form method="post">
					@endif	
							<div class="box-body">
								<div class="form-group">
									<label for="evento_name">Nome *</label>
									<input name="name" id="evento_name" class="form-control" type="text" value="{{$evento->name}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_inicio">Data de Início *</label>
									<input name="data_inicio" id="evento_data_inicio" class="form-control" type="text" value="{{$evento->getDataInicio()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_fim">Data de Fim *</label>
									<input name="data_fim" id="evento_data_fim" class="form-control" type="text" value="{{$evento->getDataFim()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="cidade_id">Cidade *</label>
									<select name="cidade_id" id="cidade_id" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif>
										<option value="">--- Selecione ---</option>
										@foreach($cidades as $cidade)
											<option value="{{$cidade->id}}">{{$cidade->id}} - {{$cidade->name}}</option>
										@endforeach
									</select>
								</div>
								<div class="form-group">
									<label for="evento_local">Local *</label>
									<input name="local" id="evento_local" class="form-control" type="text" value="{{$evento->local}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_link">Link</label>
									<input name="link" id="evento_link" class="form-control" type="text" value="{{$evento->link}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_limite_inscricoes_abertas">Data e Hora Limite para Inscrições</label>
									<input name="data_limite_inscricoes_abertas" id="evento_data_limite_inscricoes_abertas" class="form-control" type="text" value="{{$evento->getDataFimInscricoesOnline()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="maximo_inscricoes_evento">Número Máximo de Inscrições Permitidas no Evento</label>
									<input name="maximo_inscricoes_evento" id="maximo_inscricoes_evento" class="form-control" type="text" value="{{$evento->maximo_inscricoes_evento}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="usa_cbx" name="usa_cbx" @if($evento->usa_cbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif > Utiliza Rating CBX?</label>
								</div>
								<div class="form-group">
									<label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="usa_fide" name="usa_fide" @if($evento->usa_fide) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) || $evento->usa_lbx) disabled="disabled" @endif> Utiliza Rating FIDE?</label>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="usa_lbx" name="usa_lbx" @if($evento->usa_lbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif > Utiliza Rating LBX?</label>
								</div>
								<div class="form-group">
									<label for="tipo_ratings_id">Tipo de Rating</label>
									<select name="tipo_ratings_id" id="tipo_ratings_id" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de rating ---</option>
										@foreach($tipos_rating as $tipo_rating)
											<option value="{{$tipo_rating->id}}">{{$tipo_rating->id}} - {{$tipo_rating->name}}</option>
										@endforeach
									</select>
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
					)
						</form>
					@endif
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane active" id="pagina">
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
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
					)
						<form method="post" action="{{url("/evento/".$evento->id."/pagina")}}" enctype="multipart/form-data">
					@endif	
							<div class="box-body">
								<div class="form-group">
									<label for="imagem">Imagem</label>
									@if($evento->imagem)
										<br/><img src="data:image/png;base64, {!!$evento->imagem!!}" width="100%" style="max-width: 400px"/>
										<label><input type="checkbox" name="remover_imagem" /> Remover Imagem?</label>
									@endif
									<input type="file" id="imagem" name="imagem">
									@if ($errors->has('imagem'))
										<span class="help-block">
											<strong>{{ $errors->first('imagem') }}</strong>
										</span>
									@endif
								</div>
								
								<div class="form-group">
									<label for="texto">Texto</label>
									<textarea class="form-control" id="texto" name="texto" placeholder="Texto sobre o Evento">{{$evento->texto}}</textarea>				
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
					)
						</form>
					@endif
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="criterio_desempate">
				<div class="alert alert-warning alert-dismissible" role="alert">
					<strong>Alerta!</strong><br/>
					Lembre-se que, o <b>Grupo de Evento</b> poderá possuir critérios de desempate também.<br/>
					Caso você escolha um ou mais critérios nesta tela, os critérios de desempate do Grupo de Evento <strong>serão desconsiderados!</strong>
				</div>
				<br/>
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
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
													<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/criteriodesempate/remove/".$criterio_desempate->id)}}" role="button"><i class="fa fa-times"></i></a>
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
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
				)
				<section class="col-lg-6 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Nova Relação de Categoria</h3>
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
				</section>	
				@endif
				<section class="col-lg-6 connectedSortable">
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
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->categorias->all() as $categoria)
											<tr>
												<td>{{$categoria->categoria->id}}</td>
												<td>{{$categoria->categoria->name}}</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
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
					(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]))
					&& env("NOVO_TORNEIO",false)
				)
					<section class="col-lg-12 connectedSortable">
					
						<!-- Torneio -->
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Novo Torneio</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/torneio/add")}}">
								<div class="box-body">
								
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
								<table id="tabela_torneio_template" class="table-responsive table-condensed table-striped" style="width: 100%">
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
													Total: {{$torneio->getCountInscritos()}}<br/>
													Confirmados: {{$torneio->getCountInscritosConfirmados()}}<br/>
													Não Confirmados: {{$torneio->getCountInscritosNaoConfirmados()}}<br/>
													Presentes: <strong>{{$torneio->quantosInscritosPresentes()}}</strong>
												</td>
												<td>
													@if($torneio->hasCriteriosDesempateNasInscricoes())
														Sim
													@else
														<strong>Não</strong>
													@endif
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
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
													)
														<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/edit/".$torneio->id)}}" role="button">Editar</a>
														<a class="btn btn-sm btn-warning" href="{{url("/evento/".$evento->id."/torneios/union/".$torneio->id)}}" role="button">Unir Torneios</a><br/>
													@endif
													<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes")}}" role="button">Inscrições</a>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
													)
														@if(!$evento->e_resultados_manuais) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/resultados")}}" role="button">Resultados</a><br/> @endif
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm")}}" role="button" target="_blank">Baixar Inscrições Confirmadas</a><br/>
														<a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/all")}}" role="button" target="_blank">Baixar Todas as Inscrições</a><br/>
													@endif
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes")}}" role="button" target="_blank">Imprimir Inscrições</a><br/>
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético)</a><br/>
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico/cidade")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético por Cidade/Clube)</a><br/>
													@if($torneio->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/delete/".$torneio->id)}}" role="button">Apagar</a> @endif
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
<script type="text/javascript">
  $(document).ready(function(){
		$("#torneio_template_id").select2();
		$("#categoria_id").select2();
		$("#criterio_desempate_id").select2();
		$("#criterio_desempate_geral_id").select2();
		$("#tipo_torneio_id").select2();
		$("#softwares_id").select2();
		$("#tipo_ratings_id").select2();
		$("#cidade_id").select2();
		$("#cidade_id").val([{{$evento->cidade_id}}]).change();
		@if($evento->tipo_rating)
			$("#tipo_ratings_id").val([{{$evento->tipo_rating->tipo_ratings_id}}]).change();
		@endif
		$("#tabela_torneio_template").DataTable({
				responsive: true,
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
  });
</script>
@endsection

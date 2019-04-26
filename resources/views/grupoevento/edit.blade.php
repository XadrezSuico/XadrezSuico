@extends('adminlte::page')

@section("title", "Dashboard de Grupo de Evento")

@section('content_header')
  <h1>Dashboard de Grupo de Evento</h1>
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
  <li role="presentation"><a href="/grupoevento/new">Novo Grupo de Evento</a></li>
</ul>
<div class="row">
  <!-- Left col -->
	<div>

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a href="#editar_evento" aria-controls="editar_evento" role="tab" data-toggle="tab">Editar Evento</a></li>
			<li role="presentation"><a href="#template_torneio" aria-controls="template_torneio" role="tab" data-toggle="tab">Template de Torneio</a></li>
			<li role="presentation"><a href="#criterio_desempate" aria-controls="criterio_desempate" role="tab" data-toggle="tab">Critério de Desempate</a></li>
			<li role="presentation"><a href="#criterio_desempate_geral" aria-controls="criterio_desempate_geral" role="tab" data-toggle="tab">Critério de Desempate Geral</a></li>
			<li role="presentation"><a href="#categoria" aria-controls="categoria" role="tab" data-toggle="tab">Categoria</a></li>
			<li role="presentation"><a href="#pontuacao" aria-controls="pontuacao" role="tab" data-toggle="tab">Pontuação</a></li>
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
									<input name="name" id="name" class="form-control" type="text" value="{{$grupo_evento->name}}" />
								</div>
									<label for="tipo_ratings_id">Tipo de Rating</label>
									<select name="tipo_ratings_id" id="tipo_ratings_id" class="form-control width-100">
										<option value="">--- Você pode selecionar um tipo de rating ---</option>
										@foreach($tipos_rating as $tipo_rating)
											<option value="{{$tipo_rating->id}}">{{$tipo_rating->id}} - {{$tipo_rating->name}}</option>
										@endforeach
									</select>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
						</form>
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="template_torneio">
				<br/>
				<section class="col-lg-6 connectedSortable">
				
					<!-- Template de Torneio -->
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Relacionar Template de Torneio</h3>
						</div>
						<!-- form start -->
						<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/torneiotemplate/add")}}">
							<div class="box-body">
								<div class="form-group">
									<label for="torneio_template_id">Template de Torneio</label>
									<select name="torneio_template_id" id="torneio_template_id" class="form-control width-100">
										<option value="">--- Selecione ---</option>
										@foreach($torneio_templates as $torneio_template)
											<option value="{{$torneio_template->id}}">{{$torneio_template->id}} - {{$torneio_template->name}}</option>
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
				<section class="col-lg-6 connectedSortable">
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
												<td>{{$torneio_template->template->id}}</td>
												<td>{{$torneio_template->template->name}}</td>
												<td>
													<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/torneiotemplate/remove/".$torneio_template->id)}}" role="button"><i class="fa fa-times"></i></a>
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
				<section class="col-lg-6 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Nova Relação de Categoria</h3>
						</div>
						<!-- form start -->
						<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/categoria/add")}}">
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
										@foreach($grupo_evento->categorias->all() as $categoria)
											<tr>
												<td>{{$categoria->categoria->id}}</td>
												<td>{{$categoria->categoria->name}}</td>
												<td>
													<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/categoria/remove/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a>
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
		@if($grupo_evento->tipo_rating)
			$("#tipo_ratings_id").val([{{$grupo_evento->tipo_rating->tipo_ratings_id}}]).change();
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
  });
</script>
@endsection

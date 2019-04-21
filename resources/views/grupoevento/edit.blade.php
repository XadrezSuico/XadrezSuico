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
  <section class="col-lg-6 connectedSortable">
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
				</div>
				<!-- /.box-body -->

				<div class="box-footer">
					<button type="submit" class="btn btn-success">Enviar</button>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</div>
			</form>
		</div>



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
						<select name="torneio_template_id" id="torneio_template_id" class="form-control">
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
										<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/torneiotemplate/remove/".$torneio_template->id)}}" role="button">Remover Relação</a>
									</td>
								</tr>
							@endforeach
						</tbody>
          			</table>
				</div>
				<!-- /.box-body -->
		</div>



		<!-- Critérios de Desempate -->
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Relacionar Critério de Desempate</h3>
			</div>
			<!-- form start -->
			<form method="post" action="{{url("/grupoevento/".$grupo_evento->id."/criteriodesempate/add")}}">
				<div class="box-body">
					<div class="form-group">
						<label for="criterio_desempate_id">Critério de Desempate</label>
						<select name="criterio_desempate_id" id="criterio_desempate_id" class="form-control">
							<option value="">--- Selecione ---</option>
							@foreach($criterios_desempate as $criterio_desempate)
								<option value="{{$criterio_desempate->id}}">{{$criterio_desempate->id}} - {{$criterio_desempate->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="tipo_torneio_id">Tipo de Torneio</label>
						<select name="tipo_torneio_id" id="tipo_torneio_id" class="form-control">
							<option value="">--- Selecione ---</option>
							@foreach($tipos_torneio as $tipo_torneio)
								<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->id}} - {{$tipo_torneio->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="softwares_id">Software</label>
						<select name="softwares_id" id="softwares_id" class="form-control">
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
						<select name="categoria_id" id="categoria_id" class="form-control">
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
										<a class="btn btn-danger" href="{{url("/grupoevento/".$grupo_evento->id."/categoria/remove/".$categoria->id)}}" role="button">Remover Relação</a>
									</td>
								</tr>
							@endforeach
						</tbody>
          			</table>
				</div>
				<!-- /.box-body -->
		</div>
  </section>
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
		$("#tipo_torneio_id").select2();
		$("#softwares_id").select2();
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
  });
</script>
@endsection

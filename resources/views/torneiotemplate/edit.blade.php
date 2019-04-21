@extends('adminlte::page')

@section("title", "Dashboard de Template de Torneio")

@section('content_header')
  <h1>Dashboard de Template de Torneio</h1>
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
  <li role="presentation"><a href="/torneiotemplate">Voltar a Lista de Templates de Torneio</a></li>
  <li role="presentation"><a href="/torneiotemplate/new">Novo Template de Torneio</a></li>
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
						<input name="name" id="name" class="form-control" type="text" value="{{$torneio_template->name}}" />
					</div>
					<div class="form-group">
						<label for="torneio_name">Nome do Torneio</label>
						<input name="torneio_name" id="torneio_name" class="form-control" type="text" value="{{$torneio_template->torneio_name}}" />
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
				<h3 class="box-title">Nova Relação de Categoria</h3>
			</div>
			<!-- form start -->
			<form method="post" action="{{url("/torneiotemplate/".$torneio_template->id."/categoria/add")}}">
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
					<table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Nome</th>
								<th width="20%">Opções</th>
							</tr>
						</thead>
						<tbody>
							@foreach($torneio_template->categorias->all() as $categoria)
								<tr>
									<td>{{$categoria->categoria->id}}</td>
									<td>{{$categoria->categoria->name}}</td>
									<td>
										<a class="btn btn-danger" href="{{url("/torneiotemplate/".$torneio_template->id."/categoria/remove/".$categoria->id)}}" role="button">Remover Relação</a>
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
		$("#categoria_id").select2();
		$("#tabela").DataTable({
				responsive: true,
		});
  });
</script>
@endsection

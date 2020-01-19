@extends('adminlte::page')

@section("title", "Dashboard de Tipo de Rating")

@section('content_header')
  <h1>Dashboard de Tipo de Rating</h1>
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
		<li role="presentation"><a href="/tiporating">Voltar a Lista de Tipos de Rating</a></li>
		<li role="presentation"><a href="/tiporating/new">Novo Tipo de Rating</a></li>
	</ul>
	<div class="row">
  <section class="col-lg-6 connectedSortable">

	
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Editar Tipo de Rating</h3>
			</div>
			<!-- form start -->
			<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome</label>
						<input name="name" id="name" class="form-control" type="text" value="{{$tipo_rating->name}}" />
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
		<!-- Sexos da Categoria -->
		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Criar Regra</h3>
			</div>
			<!-- form start -->
			<form method="post" action="{{url("/tiporating/".$tipo_rating->id."/regra/add")}}">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Idade Mínima (Em anos)</label>
						<input name="idade_minima" id="idade_minima" class="form-control" type="text" />
					</div>
					<div class="form-group">
						<label for="name">Idade Máxima (Em anos)</label>
						<input name="idade_maxima" id="idade_maxima" class="form-control" type="text" />
					</div>
					<div class="form-group">
						<label for="name">Rating Inicial</label>
						<input name="inicial" id="inicial" class="form-control" type="text" />
					</div>
					<div class="form-group">
						<label for="name">Coeficiente K</label>
						<input name="k" id="k" class="form-control" type="text" />
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
				<h3 class="box-title">Regras</h3>
			</div>
			<!-- form start -->
				<div class="box-body">
					<table id="tabela_regras" class="table-responsive table-condensed table-striped" style="width: 100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Idade Mínima</th>
								<th>Idade Máxima</th>
								<th>Rating Inicial</th>
								<th>Coef. K</th>
								<th width="20%">Opções</th>
							</tr>
						</thead>
						<tbody>
							@foreach($tipo_rating->regras->all() as $regra)
								<tr>
									<td>{{$regra->id}}</td>
									<td>{{$regra->idade_minima}}</td>
									<td>{{$regra->idade_maxima}}</td>
									<td>{{$regra->inicial}}</td>
									<td>{{$regra->k}}</td>
									<td>
										<a class="btn btn-danger" href="{{url("/tiporating/".$tipo_rating->id."/regra/remove/".$regra->id)}}" role="button"><i class="fa fa-times"></i></a>
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
		$("#tabela_regras").DataTable();
  });
</script>
@endsection

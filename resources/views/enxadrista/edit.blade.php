@extends('adminlte::page')

@section("title", "Editar Enxadrista")

@section('content_header')
  <h1>Editar Enxadrista</h1>
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
  <li role="presentation"><a href="/enxadrista">Voltar a Lista de Enxadristas</a></li>
  <li role="presentation"><a href="/enxadrista/new">Novo Enxadrista</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Editar Clube</h3>
		</div>
	  <!-- form start -->
      <form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome Completo *</label>
						<input name="name" id="name" class="form-control" type="text" value="{{$enxadrista->name}}" />
					</div>
					<div class="form-group">
						<label for="born">Data de Nascimento *</label>
						<input name="born" id="born" class="form-control" type="text" value="{{$enxadrista->getBorn()}}" />
					</div>
					<div class="form-group">
						<label for="sexos_id">Sexo *</label>
						<select id="sexos_id" name="sexos_id" class="form-control">
							<option value="">--- Selecione ---</option>
							@foreach($sexos as $sexo)
								<option value="{{$sexo->id}}">{{$sexo->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="cbx_rating">Rating CBX</label>
								<input name="cbx_rating" id="cbx_rating" class="form-control" type="text" value="{{$enxadrista->cbx_rating}}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="fide_rating">Rating FIDE</label>
								<input name="fide_rating" id="fide_rating" class="form-control" type="text" value="{{$enxadrista->fide_rating}}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="cbx_id">ID CBX</label>
								<input name="cbx_id" id="cbx_id" class="form-control" type="text" value="{{$enxadrista->cbx_id}}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="fide_id">ID FIDE</label>
								<input name="fide_id" id="fide_id" class="form-control" type="text" value="{{$enxadrista->fide_id}}" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="email">E-mail</label>
								<input name="email" id="email" class="form-control" type="text" value="{{$enxadrista->email}}" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="celular">Celular</label>
								<input name="celular" id="celular" class="form-control" type="text" value="{{$enxadrista->celular}}" />
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="cidade_id">Cidade *</label>
						<select id="cidade_id" name="cidade_id" class="form-control">
							<option value="">--- Selecione uma cidade ---</option>
							@foreach($cidades as $cidade)
								<option value="{{$cidade->id}}">{{$cidade->id}} - {{$cidade->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="clube_id">Clube</label>
						<select id="clube_id" name="clube_id" class="form-control">
							<option value="">--- Você pode selecionar um clube ---</option>
							@foreach($clubes as $clube)
								<option value="{{$clube->id}}">{{$clube->cidade->name}} - {{$clube->name}}</option>
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
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
		$("#cidade_id").select2().val([{{$enxadrista->cidade_id}}]).change();
		$("#clube_id").select2().val([{{$enxadrista->clube_id}}]).change();
		$("#sexos_id").select2().val([{{$enxadrista->sexos_id}}]).change();
		$("#celular").mask("(00) 00000-0000");
  });
</script>
@endsection

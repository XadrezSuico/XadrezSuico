@extends('adminlte::page')

@section("title", "Editar Sexo")

@section('content_header')
  <h1>Editar Sexo</h1>
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
  <li role="presentation"><a href="/sexo">Voltar a Lista de Sexos</a></li>
  <li role="presentation"><a href="/sexo/new">Novo Sexo</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Editar Sexo</h3>
		</div>
	  <!-- form start -->
        <form method="post">
			<div class="box-body">
				<div class="form-group">
					<label for="name">Nome</label>
					<input name="name" id="name" class="form-control" type="text" value="{{$sexo->name}}" />
				</div>
				<div class="form-group">
					<label for="abbr">Abreviação</label>
					<input name="abbr" id="abbr" class="form-control" type="text" value="{{$sexo->abbr}}" />
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

  });
</script>
@endsection

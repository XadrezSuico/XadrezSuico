@extends('adminlte::page')

@section("title", "Editar Categoria")

@section('content_header')
  <h1>Editar Categoria</h1>
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
  <li role="presentation"><a href="/categoria">Voltar a Lista de Categorias</a></li>
  <li role="presentation"><a href="/categoria/new">Nova Categoria</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Editar Categoria</h3>
		</div>
	  <!-- form start -->
        <form method="post">
			<div class="box-body">
				<div class="form-group">
					<label for="name">Nome</label>
					<input name="name" id="name" class="form-control" type="text" value="{{$categoria->name}}" />
				</div>
				<div class="form-group">
					<label for="name">Idade Mínima (Em anos)</label>
					<input name="idade_minima" id="idade_minima" class="form-control" type="text" value="{{$categoria->idade_minima}}" />
				</div>
				<div class="form-group">
					<label for="name">Idade Máxima (Em anos)</label>
					<input name="idade_maxima" id="idade_maxima" class="form-control" type="text" value="{{$categoria->idade_maxima}}" />
				</div>
				<div class="form-group">
					<label for="name">Código Categoria (Padrão Swiss-Manager)</label>
					<input name="cat_code" id="cat_code" class="form-control" type="text" value="{{$categoria->cat_code}}" />
				</div>
				<div class="form-group">
					<label for="name">Código Grupo (Deve ser único em cada torneio, para evitar problemas de processamento do resultado)</label>
					<input name="code" id="code" class="form-control" type="text" value="{{$categoria->code}}" />
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

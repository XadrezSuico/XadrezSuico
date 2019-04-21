@extends('adminlte::page')

@section("title", "Novo Template de Torneio")

@section('content_header')
  <h1>Novo Template de Torneio</h1>
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
  <li role="presentation"><a href="/grupoevento">Voltar a Lista de Templates de Torneio</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Novo Template de Torneio</h3>
		</div>
	  <!-- form start -->
        <form method="post">
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

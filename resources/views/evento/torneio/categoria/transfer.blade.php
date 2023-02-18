@extends('adminlte::page')

@section("title", 'Evento #'.$torneio->evento->id." - Transferir Categoria")

@section('content_header')
  <h1>Evento #{{$torneio->evento->id}} ({{$torneio->evento->name}}) - Torneio #{{$torneio->id}} ({{$torneio->name}}) - Transferir Categoria</h1>
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
  <li role="presentation"><a href="/evento/{{$torneio->evento->id}}/torneios/edit/{{$torneio->id}}">Voltar a Edição do Torneio</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Transferir Categoria</h3>
			</div>
			<!-- form start -->
			<form method="post">
				<div class="box-body">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <strong>Alerta!</strong><br/>
                        Esta função serve para transferir uma categoria de um torneio para outro. Com isso, as inscrições desta categoria que estão neste torneio também serão migradas.
                    </div>

					<div class="form-group">
                        <label>Categoria: </label> #{{$categoria->categoria->id}} - {{$categoria->categoria->name}}
                    </div>
					<div class="form-group">
						<label for="tournament_id">Torneio para Transferência da Categoria</label>
						<select id="tournament_id" name="tournament_id" class="form-control">
							<option value="">-- Selecione --</option>
							@foreach($torneio->evento->torneios()->where([["id","!=",$torneio->id]])->get() as $outro_torneio)
								<option value="{{$outro_torneio->id}}"> #{{$outro_torneio->id}} - {{$outro_torneio->name}} </option>
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
		$("#tournament_id").select2();
    });
</script>
@endsection

@extends('adminlte::page')

@section("title", 'Evento #'.$evento->id." - Novo Torneio")

@section('content_header')
  <h1>Evento #{{$evento->id}} ({{$evento->name}}) - Novo Torneio</h1>
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
  <li role="presentation"><a href="/evento/dashboard/{{$evento->id}}?tab=torneio">Voltar a Lista de Torneio</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Novo Torneio</h3>
		</div>
	  <!-- form start -->
        <form method="post">
			<div class="box-body">
				<div class="form-group">
					<label for="name">Nome</label>
					<input name="name" id="name" class="form-control" type="text" />
				</div>
				<div class="form-group">
					<label for="tipo_torneio_id">Tipo de Torneio</label>
					<select id="tipo_torneio_id" name="tipo_torneio_id" class="form-control">
						<option value="">-- Selecione --</option>
						@foreach($tipos_torneio as $tipo_torneio)
							<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->name}}</option>
						@endforeach
					</select>
				</div>
                <div class="form-group">
                    <label for="softwares_id">Software</label>
                    <select id="softwares_id" name="softwares_id" class="form-control">
                        <option value="">-- Selecione --</option>
                        @foreach($softwares as $software)
                            <option value="{{$software->id}}">{{$software->name}}</option>
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
	$("#tipo_torneio_id").select2();
	$("#softwares_id").select2();
  });
</script>
@endsection

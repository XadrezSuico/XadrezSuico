@extends('adminlte::page')

@section("title", "Evento #".$evento->id." (".$evento->name.") >> XadrezSuíço Classificador >> Editar")
@section('content_header')
  <h1>Evento #{{$evento->id}} ({{$evento->name}}) >> XadrezSuíço Classificador >> Editar</h1>
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
		<li role="presentation"><a href="/evento/dashboard/{{$evento->id}}?tab=classificator">Voltar a Lista de Classificadores na Dashboard de Evento</a></li>
		<li role="presentation"><a href="/evento/{{$evento->id}}/classificator/new">Novo Classificador</a></li>
	</ul>
	<div class="row">
  <section class="col-lg-12 connectedSortable">


		<div class="box box-primary">
			<div class="box-header">
				<h3 class="box-title">Editar Classificador</h3>
			</div>
			<!-- form start -->
					<form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="event_classificator_id">Evento que Classifica a Este</label>
                        <select name="event_classificator_id" id="event_classificator_id" class="form-control width-100">
                            @foreach($evento->all() as $evento_class)
                                <option value="{{$evento_class->id}}">{{$evento_class->id}} - {{$evento_class->name}}</option>
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
        $("#event_classificator_id").select2();
        $("#event_classificator_id").val([{{$event_classificate->event_classificator_id}}]).change();
  });
</script>
@endsection

@extends('adminlte::page')

@section("title", 'Clube #'.$clube_base->id." - Unir Clubes")

@section('content_header')
  <h1>Clube #{{$clube_base->id}} ({{$clube_base->name}}) - Unir Clubes</h1>
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
  <li role="presentation"><a href="/clube">Voltar a Lista de Clubes</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
		<div class="box box-primary" id="inscricao">
			<div class="box-header">
				<h3 class="box-title">Unir Clubes</h3>
			</div>
			<!-- form start -->
			<form method="post">
				<div class="box-body">
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <strong>Alerta!</strong><br/>
                        Esta função serve para efetuar a união entre dois clubes.
                        Todas as inscrições, vínculos e enxadristas do "Clube a ser Unido ao Clube Base" serão importados para o "Clube Base", e por fim, o "Clube a ser Unido ao Clube Base" será excluído.<br/>
                        <strong>Tome cuidado com esta função, visto que não é possível voltar às configurações anteriores!</strong>
                    </div>

					<div class="form-group">
						<label for="name">Clube Base: </label> #{{$clube_base->id}} - {{$clube_base->getName()}} ({{$clube_base->getPlace()}})
					</div>
					<div class="form-group">
						<label for="clube_a_ser_unido">Clube a Ser Unido ao Clube Base</label>
						<select id="clube_a_ser_unido" name="clube_a_ser_unido" class="form-control">
							<option value="">-- Selecione --</option>
							@foreach($clubes as $clube)
								<option value="{{$clube->id}}"> #{{$clube->id}} - - {{$clube->getName()}} ({{$clube->getPlace()}})</option>
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
		$("#clube_a_ser_unido").select2();
    });
</script>
@endsection

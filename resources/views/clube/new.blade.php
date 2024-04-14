@extends('adminlte::page')

@section("title", "Novo Clube")

@section('content_header')
  <h1>Novo Clube</h1>
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
			<h3 class="box-title">Nova Cidade</h3>
		</div>
	  <!-- form start -->
      <form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome</label>
						<input name="name" id="name" class="form-control" type="text" />
					</div>
					<div class="form-group">
						<label for="abbr">Abreviação</label>
						<input name="abbr" id="abbr" class="form-control" type="text" maxlength="3" />
					</div>
					<div class="form-group">
						<label for="cidade_id">Cidade</label>
						<select id="cidade_id" name="cidade_id" class="form-control">
							<option value="">--- Selecione uma cidade ---</option>
							@foreach($cidades as $cidade)
								<option value="{{$cidade->id}}">{{$cidade->id}} - {{$cidade->name}}</option>
							@endforeach
						</select>
					</div>
                    @if(env("ENTITY_DOMAIN",null) == "fexpar.com.br")
                        <div class="form-group">
                            <label><input type="checkbox" name="is_fexpar___clube_filiado" /></label>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_fexpar___clube_valido_vinculo_federativo" /></label>
                        </div>
                    @endif
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
		$("#cidade_id").select2();
  });
</script>
@endsection

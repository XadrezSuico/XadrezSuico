@extends('adminlte::page')

@section("title", "Novo Enxadrista")

@section('content_header')
  <h1>Novo Enxadrista</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
		.select2{
			width: 100% !important;
		}
	</style>
@endsection

@section("content")
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/enxadrista">Voltar a Lista de Enxadristas</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Novo Enxadrista</h3>
		</div>
	  <!-- form start -->
      <form method="post">
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome Completo *</label>
						<input name="name" id="name" class="form-control" type="text" />
					</div>
					<div class="form-group">
						<label for="born">Data de Nascimento *</label>
						<input name="born" id="born" class="form-control" type="text" />
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
					<div class="form-group">
						<label for="pais_nascimento_id">País de Nascimento *</label>
						<select id="pais_nascimento_id" name="pais_nascimento_id" class="form-control this_is_select2">
							<option value="">--- Selecione um país ---</option>
							@foreach(\App\Pais::all() as $pais)
								<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
							@endforeach
						</select>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="cbx_id">ID CBX</label>
								<input name="cbx_id" id="cbx_id" class="form-control" type="text" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="fide_id">ID FIDE</label>
								<input name="fide_id" id="fide_id" class="form-control" type="text" />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="lbx_id">ID LBX</label>
								<input name="lbx_id" id="lbx_id" class="form-control" type="text" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="email">E-mail</label>
								<input name="email" id="email" class="form-control" type="text" />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="pais_celular_id">País do Celular *</label>
								<select id="pais_celular_id" name="pais_celular_id" class="form-control this_is_select2">
									<option value="">--- Selecione um país ---</option>
									@foreach(\App\Pais::all() as $pais)
										<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
									@endforeach
								</select>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="celular">Celular</label>
								<input name="celular" id="celular" class="form-control" type="text" />
							</div>
						</div>
					</div>
					<hr/>
					<h4>Vínculo do Enxadrista</h4>
					<div class="form-group">
						<label for="pais_id">País do Vínculo *</label>
						<select id="pais_id" name="pais_id" class="form-control this_is_select2">
							<option value="">--- Selecione um País ---</option>
							@foreach(\App\Pais::all() as $pais)
								<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="estados_id">Estado do Vínculo *</label>
						<select id="estados_id" name="estados_id" class="form-control this_is_select2">
							<option value="">--- Selecione um país antes ---</option>
						</select>
					</div>
					<div class="form-group">
						<label for="cidade_id">Cidade do Vínculo *</label>
						<select id="cidade_id" name="cidade_id" class="form-control this_is_select2">
							<option value="">--- Selecione um estado antes ---</option>
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
	  	$(".this_is_select2").select2();
		$("#cidade_id").select2();
		$("#clube_id").select2();
		$("#sexos_id").select2();
		$("#born").mask("00/00/0000");
		$("#celular").mask("(00) 00000-0000");


		$("#pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			buscaEstados(false,function(){
				buscaCidades(function(){
					Loading.destroy();
				});
			});
		});
		$("#estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			buscaCidades(function(){
				Loading.destroy();
			});
		});
	});

  
	function buscaEstados(buscaCidade,callback){
		$('#estados_id').html("").trigger('change');
		$.getJSON("{{url("/estado/search")}}/".concat($("#pais_id").val()),function(data){
			for (i = 0; i < data.results.length; i++) {
				var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
				$('#estados_id').append(newOptionEstado).trigger('change');
				if(i + 1 == data.results.length){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(false);
					}
				}
			}
			if(data.results.length == 0){
				if(callback){
					callback();
				}
				if(buscaCidade){
					buscaCidades(false);
				}
			}
		});
	}

	function buscaCidades(callback){
		$('#cidade_id').html("").trigger('change');
		$.getJSON("{{url("/cidade/search")}}/".concat($("#estados_id").val()),function(data){
			for (i = 0; i < data.results.length; i++) {
				var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
				$('#cidade_id').append(newOptionCidade).trigger('change');
				if(i + 1 == data.results.length){
					if(callback){
						callback();
					}
				}
			}
			if(data.results.length == 0){
				if(callback){
					callback();
				}
			}
		});
	}
</script>
@endsection

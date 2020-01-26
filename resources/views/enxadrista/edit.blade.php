@extends('adminlte::page')

@php
        $permitido_edicao = false;
        if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventsByPerfil([4])
        ){
            $permitido_edicao = true;
        }
@endphp

@section("title", "Editar Enxadrista")

@section('content_header')
  <h1>Editar Enxadrista</h1>
  <h3>Código: {{$enxadrista->id}}</h3>
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
			@if($permitido_edicao) <form method="post"> @endif
				<div class="box-body">
					<div class="form-group">
						<label for="name">Nome Completo *</label>
						<input name="name" id="name" class="form-control" type="text" value="{{$enxadrista->name}}" @if(!$permitido_edicao) disabled="disabled" @endif />
					</div>
					<div class="form-group">
						<label for="born">Data de Nascimento *</label>
						<input name="born" id="born" class="form-control" type="text" value="{{$enxadrista->getBorn()}}" @if(!$permitido_edicao) disabled="disabled" @endif />
					</div>
					<div class="form-group">
						<label for="sexos_id">Sexo *</label>
						<select id="sexos_id" name="sexos_id" class="form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione ---</option>
							@foreach($sexos as $sexo)
								<option value="{{$sexo->id}}">{{$sexo->name}}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="pais_nascimento_id">País de Nascimento *</label>
						<select id="pais_nascimento_id" name="pais_nascimento_id" class="form-control this_is_select2" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione um país ---</option>
							@foreach(\App\Pais::all() as $pais)
								<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
							@endforeach
						</select>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="cbx_rating">Rating CBX</label><br/>
								STD: {{$enxadrista->showRating(1,0)}}<br/>
								RPD: {{$enxadrista->showRating(1,1)}}<br/>
								BTZ: {{$enxadrista->showRating(1,2)}}<br/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="fide_rating">Rating FIDE</label><br/>
								STD: {{$enxadrista->showRating(0,0)}}<br/>
								RPD: {{$enxadrista->showRating(0,1)}}<br/>
								BTZ: {{$enxadrista->showRating(0,2)}}<br/>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="lbx_rating">Rating LBX</label><br/>
								STD: {{$enxadrista->showRating(2,0)}}<br/>
								RPD: {{$enxadrista->showRating(2,1)}}<br/>
								BTZ: {{$enxadrista->showRating(2,2)}}<br/>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="cbx_id">ID CBX</label>
								<input name="cbx_id" id="cbx_id" class="form-control" type="text" value="{{$enxadrista->cbx_id}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="fide_id">ID FIDE</label>
								<input name="fide_id" id="fide_id" class="form-control" type="text" value="{{$enxadrista->fide_id}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="lbx_id">ID LBX</label>
								<input name="lbx_id" id="lbx_id" class="form-control" type="text" value="{{$enxadrista->lbx_id}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label for="email">E-mail</label>
								<input name="email" id="email" class="form-control" type="text" value="{{$enxadrista->email}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="pais_celular_id">País do Celular *</label>
								<select id="pais_celular_id" name="pais_celular_id" class="form-control this_is_select2" @if(!$permitido_edicao) disabled="disabled" @endif>
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
								<input name="celular" id="celular" class="form-control" type="text" value="{{$enxadrista->celular}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
					</div>
					<hr/>
					<h4>Vínculo do Enxadrista</h4>
					<div class="form-group">
						<label for="pais_id">País do Vínculo *</label>
						<select id="pais_id" name="pais_id" class="form-control this_is_select2" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione um País ---</option>
							@foreach(\App\Pais::all() as $pais)
								<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label for="estados_id">Estado do Vínculo *</label>
						<select id="estados_id" name="estados_id" class="form-control this_is_select2" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione um país antes ---</option>
						</select>
					</div>
					<div class="form-group">
						<label for="cidade_id">Cidade do Vínculo *</label>
						<select id="cidade_id" name="cidade_id" class="form-control this_is_select2" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione um estado antes ---</option>
						</select>
					</div>
					<div class="form-group">
						<label for="clube_id">Clube do Vínculo</label>
						<select id="clube_id" name="clube_id" class="form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Você pode selecionar um clube ---</option>
							@foreach($clubes as $clube)
								<option value="{{$clube->id}}">{{$clube->cidade->name}} - {{$clube->name}}</option>
							@endforeach
						</select>
					</div>
				</div>
			<!-- /.box-body -->

			@if($permitido_edicao)
				<div class="box-footer">
					<button type="submit" class="btn btn-success">Enviar</button>
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
				</div>
      		 </form> 
			@endif
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

		@if($enxadrista->cidade)
			@if($enxadrista->cidade->estado)
				@if($enxadrista->cidade->estado->pais)
					Loading.enable(loading_default_animation, 10000);
					$("#pais_id").val({{$enxadrista->cidade->estado->pais->id}}).change();
					buscaEstados(false,function(){
						setTimeout(function(){
							$("#estados_id").val({{$enxadrista->cidade->estado->id}}).change();
							setTimeout(function(){
								buscaCidades(function(){
									$("#cidade_id").val({{$enxadrista->cidade_id}}).change();
									Loading.destroy();
								});
							},200);
						},200);
					});
				@endif
			@endif
		@endif

		$("#clube_id").select2().val([{{$enxadrista->clube_id}}]).change();
		$("#sexos_id").select2().val([{{$enxadrista->sexos_id}}]).change();
		$("#pais_nascimento_id").select2().val([{{$enxadrista->pais_id}}]).change();
		$("#pais_celular_id").select2().val([{{$enxadrista->pais_celular_id}}]).change();



		if($("#pais_celular_id").val() == 33){
			setTimeout(function(){
				$("#celular").mask("(00) 00000-0000");
			},300);
		}else{
			$("#celular").unmask();
		}


		$("#pais_celular_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,700);
			$("#celular").unmask();
			if($("#pais_celular_id").val() == 33){
				setTimeout(function(){
					$("#celular").mask("(00) 00000-0000");
				},300);
			}
		});

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

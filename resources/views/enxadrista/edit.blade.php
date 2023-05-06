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
					<div class="row">
						<div class="col-md-12">
							<div class="alert alert-warning">
								Os campos de <strong>Primeiros Nomes e Sobrenomes</strong> devem ser preenchidos ou corrigidos conforme <a href="http://www.ligabrasileiradexadrez.com.br/Sistema%20LBX%20de%20Rating.pdf">regulamento do Rating da LBX</a>, mais precisamente referente ao Item 29.b (considerando a questão do nome do enxadrista).<br/>
								Esta divisão é realizada automaticamente. Só há a necessidade de efetuar alterações caso não esteja conforme regulamento.
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="firstname">Primeiros Nomes *</label>
								<input name="firstname" id="firstname" class="form-control" type="text" value="{{$enxadrista->firstname}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="lastname">Sobrenomes *</label>
								<input name="lastname" id="lastname" class="form-control" type="text" value="{{$enxadrista->lastname}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
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
					<hr/>
					<h4>Documentos:</h4>
					<div class="alert alert-warning">
						<strong>É OBRIGATÓRIO informar ao menos um documento.</strong> Além disto, poderá haver documentos que são obrigatórios, porém, estes estarão identificados com <strong>*</strong>.
					</div>
					<div id="documentos">
						<p>Não há documentos para este país.</p>
					</div>
					<hr/>
					<h4>Outras Informações:</h4>
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
					<h4>Cadastros nas Entidades:</h4>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label>CBX</label><br/>
								@if($enxadrista->cbx_id)
									Nome: @if($enxadrista->encontrado_cbx) <u>{{$enxadrista->cbx_name}}</u> @else <strong>ENXADRISTA NÃO ENCONTRADO</strong> @endif<br/>
									@if($enxadrista->encontrado_cbx)
										<label>Rating</label><br/>
										STD: {{$enxadrista->showRating(1,0)}}<br/>
										RPD: {{$enxadrista->showRating(1,1)}}<br/>
										BTZ: {{$enxadrista->showRating(1,2)}}<br/>
									@endif
								@else
									<strong>ID não informado.</strong>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>FIDE</label><br/>
								@if($enxadrista->fide_id)
									Nome: @if($enxadrista->encontrado_fide) <u>{{$enxadrista->fide_name}}</u> @else <strong>ENXADRISTA NÃO ENCONTRADO</strong> @endif<br/>
									@if($enxadrista->encontrado_fide)
										<label>Rating</label><br/>
										STD: {{$enxadrista->showRating(0,0)}}<br/>
										RPD: {{$enxadrista->showRating(0,1)}}<br/>
										BTZ: {{$enxadrista->showRating(0,2)}}<br/>
									@endif
								@else
									<strong>ID não informado.</strong>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label>LBX</label><br/>
								@if($enxadrista->lbx_id)
									Nome: @if($enxadrista->encontrado_lbx) <u>{{$enxadrista->lbx_name}}</u> @else <strong>ENXADRISTA NÃO ENCONTRADO</strong> @endif<br/>
									@if($enxadrista->encontrado_lbx)
										<label>Rating</label><br/>
										STD: {{$enxadrista->showRating(2,0)}}<br/>
										RPD: {{$enxadrista->showRating(2,1)}}<br/>
										BTZ: {{$enxadrista->showRating(2,2)}}<br/>
									@endif
								@else
									<strong>ID não informado.</strong>
								@endif
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
							@if(!$enxadrista->lbx_id)
									Pesquisa pelo Nome Completo:<br/>
									@foreach($json_lbx as $row)
										ID: {{$row->id}}<br/>
										Nome: {{$row->sobrenome}}, {{$row->nome}}<br/>
										FED: {{$row->fed}}<br/>
										Cidade: {{$row->codigo_cidade}} - {{$row->nome_cidade}}<br/>
										@if(isset($row->nascimento)) Data de Nascimento: {{$row->nascimento}}<br/> @endif
										<hr/>
									@endforeach
							@endif
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="lichess_username">Nome de Usuário - Lichess.org</label>
								<input name="lichess_username" id="lichess_username" class="form-control" type="text"value="{{$enxadrista->lichess_username}}" @if(!$permitido_edicao) disabled="disabled" @endif />
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="chess_com_username">Nome de Usuário - Chess.com</label>
								<input name="chess_com_username" id="chess_com_username" class="form-control" type="text"value="{{$enxadrista->chess_com_username}}" @if(!$permitido_edicao) disabled="disabled" @endif />
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
								<option value="{{$clube->id}}">@if($clube->cidade) {{$clube->cidade->name}} - @endif{{$clube->name}}</option>
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
		Loading.enable(loading_default_animation,10000);
		buscaTipoDocumentos(function(){
			Loading.destroy();
		});

		$("#pais_nascimento_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			buscaTipoDocumentos(function(){
				Loading.destroy();
			});
		});

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

	function buscaTipoDocumentos(callback){
		if($("#pais_nascimento_id").val() > 0){
			$('#documentos').html("");
			$.getJSON("{{url("/tipodocumento/searchByPais")}}/".concat($("#pais_nascimento_id").val()),function(data){
				for (i = 0; i < data.data.length; i++) {

					html = "";
					html = html.concat('<div class="form-group">');
					if(data.data[i].is_required){
						html = html.concat('<label for="tipo_documento_').concat(data.data[i].id).concat('">').concat(data.data[i].name).concat(' *</label>');
					}else{
						html = html.concat('<label for="tipo_documento_').concat(data.data[i].id).concat('">').concat(data.data[i].name).concat('</label>');
					}
					html = html.concat('<input name="tipo_documento_').concat(data.data[i].id).concat('" id="tipo_documento_').concat(data.data[i].id).concat('" class="form-control" type="text" />');
					html = html.concat('</div>');

					$('#documentos').append(html);

					if(data.data[i].pattern){
						$("#tipo_documento_".concat(data.data[i].id)).mask(data.data[i].pattern);
					}

					if(i+1 == data.data.length){
						if(callback){
							preencheDocumentos(data.data,callback);
						}else{
							preencheDocumentos(data.data,false);
						}
					}
				}
				if(data.data.length == 0){
					if(callback){
						callback();
					}
				}
			});
		}else{
			if(callback){
				callback();
			}
			$('#documentos').html("<p>Selecione antes um país de nascimento...</p>");
		}
	}

	function preencheDocumentos(tipo_documentos,callback){
		if(tipo_documentos){
			for (i = 0; i < tipo_documentos.length; i++) {
				$.getJSON("{{url("/enxadrista/".$enxadrista->id."/documentos/getDocumento")}}/".concat(tipo_documentos[i].id),function(data){
					if(data.ok){
						$("#tipo_documento_".concat(data.data.id)).val(data.data.number);
					}
					if(i + 1 == tipo_documentos.length){
						if(callback){
							callback();
						}
					}
				})
				.fail(function(){
					if(i + 1 == tipo_documentos.length){
						if(callback){
							callback();
						}
					}
				});
			}
		}else{
			if(callback){
				callback();
			}
		}

	}
</script>
@endsection

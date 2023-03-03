@extends('adminlte::page')

@section("title", "Confirmar Inscrição")

@section('content_header')
  <h1>Confirmar Inscrição</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}

		.box-title.evento{
			font-size: 2.5rem;
			font-weight: bold;
		}
		#texto_pesquisa{
			font-size: 2rem;
		}
		#processo_inscricao .box-body{
			min-height: 500px;
		}
		#pesquisa{
			min-height: 400px;
		}
		#pesquisa ul li{
			font-size: 1.5rem;
		}
		.this_is_select2, .select2{
			width: 100% !important;
		}
        #successMessage a{
            color: #fff !important;
        }


        .field-required{
            color: red;
        }

        #successMessage a.btn-default{
            color: green !important;
        }
	</style>
@endsection

@section("content")

<div class="modal fade modal-warning" id="novoEstado" tabindex="-1" role="dialog" aria-labelledby="alerts">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastrar Novo Estado</h4>
            </div>
            <div class="modal-body">
				<div class="form-group">
					<label for="estado_pais_id" class="field-required">País *</label>
					<select id="estado_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
                <div class="form-group">
                    <label for="name" class="field-required">Nome *</label>
                    <input type="text" name="name" class="form-control" id="estado_nome" placeholder="Insira o Nome Completo da Cidade" required="required">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Não Quero Mais</button>
            <button type="button" id="cadastrarEstado" class="btn btn-success">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-warning" id="novaCidade" tabindex="-1" role="dialog" aria-labelledby="alerts">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastrar Nova Cidade</h4>
            </div>
            <div class="modal-body">
				<div class="form-group">
					<label for="cidade_pais_id" class="field-required">País *</label>
					<select id="cidade_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="cidade_estados_id" class="field-required">Estado/Província *</label>
					<select id="cidade_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
				</div>
                <div class="form-group">
                    <label for="name" class="field-required">Nome *</label>
                    <input type="text" name="name" class="form-control" id="cidade_nome" placeholder="Insira o Nome Completo da Cidade" required="required">
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Não Quero Mais</button>
            <button type="button" id="cadastrarCidade" class="btn btn-success">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade modal-warning" id="novoClube" tabindex="-1" role="dialog" aria-labelledby="alerts">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastrar Novo Clube/Instituição/Escola</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name" class="field-required">Nome *</label>
                    <input type="text" name="name" class="form-control" id="clube_nome" placeholder="Insira o Nome Completo do Clube" required="required">
                </div>
				<div class="form-group">
					<label for="clube_pais_id" class="field-required">País *</label>
					<select id="clube_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="clube_estados_id" class="field-required">Estado/Província *</label>
					<select id="clube_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
				</div>
                <div class="form-group">
                    <label for="clube_cidade_id" class="field-required">Cidade *</label>
                    <select id="clube_cidade_id" class="this_is_select2 form-control">
                        <option value="">--- Selecione uma cidade ---</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-warning" data-dismiss="modal">Não Quero Mais</button>
            <button type="button" id="cadastrarClube" class="btn btn-success">Cadastrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Main row -->
<ul class="nav nav-pills">
  @if(\Illuminate\Support\Facades\Auth::check())
  	@if(
		\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
		\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4]) ||
		\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
	)
		<li role="presentation"><a href="/evento/dashboard/{{$evento->id}}"><strong>Gerenciar Evento (ADMIN)</strong></a></li>
	@endif
  @endif
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title evento">Evento: {{$evento->name}}</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
            <button type="button" id="go_to_inscricao" class="btn btn-lg btn-success btn-block">
                <strong>Faça a Confirmação</strong>
            </button><br/>
            @if($evento->e_permite_visualizar_lista_inscritos_publica)
                <a href="{{url("/inscricao/visualizar/".$evento->id)}}" class="btn btn-lg btn-info btn-block">
                    Visualizar Lista de Inscrições
                </a><br/>
            @endif
            @if($evento->hasTorneiosEmparceiradosByXadrezSuico())
                <a href="{{url("/evento/acompanhar/".$evento->id)}}" class="btn btn-lg btn-primary btn-block">
                    Acompanhe o Emparceiramento do Torneio
                </a><br/>
            @endif
			@if($evento->pagina)
				@if($evento->pagina->imagem) <div style="width: 100%; text-align: center;"><img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 800px"/></div> <br/> @endif
				@if($evento->pagina->texto) {!!$evento->pagina->texto!!} <br/> @endif
				@if($evento->pagina->imagem || $evento->pagina->texto) <hr/> @endif
			@endif
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}},
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong>
            @if($evento->getDataInicio() == $evento->getDataFim())
                {{$evento->getDataInicio()}}
            @else
                {{$evento->getDataInicio()}} - {{$evento->getDataFim()}}
            @endif<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
			@if($evento->maximo_inscricoes_evento)
				<hr/>
				<strong>Total de Inscritos até o presente momento:</strong> {{$evento->quantosInscritos()}}.<br/>
				<strong>Total de Confirmados até o presente momento:</strong> {{$evento->quantosInscritosConfirmados()}}.<br/>
				<hr/>
			@endif
			@if($evento->getConfirmacoesDataInicial() && $evento->getConfirmacoesDataFim()) <h3><strong>Período de Confirmações:</strong> {{$evento->getConfirmacoesDataInicial()}} - {{$evento->getConfirmacoesDataFim()}}.</h3>@endif
		</div>
	</div>

	<div class="box box-primary" id="processo_inscricao">
		<div class="box-header">
			<h3 class="box-title">Processo de Confirmação</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
		<div class="box-body">
			<div id="form_pesquisa">
				<div class="alert alert-success" role="alert">
					Comece digitando o nome do(a) enxadrista, e caso o(a) mesmo(a) esteja inscrito, ele irá aparecer logo abaixo em "Resultado da Pesquisa", aí é só clicar no nome do(a) mesmo(a) e continuar o processo.<br/>
				</div>
				<h3>Comece a Inserir o nome ou documento do enxadrista</h3>
				<input type="text" id="texto_pesquisa" class="form-control" placeholder="Comece a digitar o nome ou documento do enxadrista para efetuar a pesquisa..." val="" />
				<hr/>
			</div>
			<div id="pesquisa">
				<h3>Resultado da Pesquisa:</h3>
				<div>
					<p>Comece a digitar o nome do enxadrista para começar a pesquisa...</p>
				</div>
			</div>
			<div id="confirmacao" style="display:none">
				<h3>Confirmar Inscrição:</h3>
				<h4>ID: <span id="enxadrista_confirmar_id">Carregando...</span></h4>
				<h4>Nome Completo: <span id="enxadrista_confirmar_nome">Carregando...</span></h4>
				<h4>Data de Nascimento: <span id="enxadrista_confirmar_born">Carregando...</span></h4>
				<h4>ID CBX: <span id="enxadrista_confirmar_id_cbx">Carregando...</span></h4>
				<h4>ID FIDE: <span id="enxadrista_confirmar_id_fide">Carregando...</span></h4>
				<h4>ID LBX: <span id="enxadrista_confirmar_id_lbx">Carregando...</span></h4>

                @if($evento->isPaid())
                    <hr/>
                    <h4>Status do Pagamento: <span id="enxadrista_confirmar_pagamento_status">Carregando...</span></h4>
                @endif

				<hr/>
				<input type="hidden" id="enxadrista_id" />
                <div class="alert alert-warning" role="alert">
                    <h4><strong>Verifique a categoria da inscrição:</strong></h4>
                    <div class="form-group">
                        <label for="confirmacao_categoria_id" class="field-required">Categoria *</label>
                        <select id="confirmacao_categoria_id" class="this_is_select2 form-control">
                            <option value="">--- Selecione ---</option>
                        </select>
                    </div>
					<label class="field-required"><input type="checkbox" id="confirmacao_categoria_conferida"> Categoria conferida *</label><br/>
                </div>
				<div class="form-group">
					<label for="confirmacao_pais_id" class="field-required">País *</label>
					<select id="confirmacao_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="confirmacao_estados_id" class="field-required">Estado/Província *</label>
					<select id="confirmacao_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
                    <button id="estadoNaoCadastradoConfirmacao" onclick="chamaCadastroEstado(2)" class="btn btn-success">O meu estado não está cadastrado</button>
				</div>
				<div class="form-group">
					<label for="confirmacao_cidade_id" class="field-required">Cidade *</label>
					<select id="confirmacao_cidade_id" class="cidade_id this_is_select2 form-control">
						<option value="">--- Selecione um estado primeiro ---</option>
					</select>
                    <button id="cidadeNaoCadastradaConfirmacao" onclick="chamaCadastroCidade(2)" class="btn btn-success">A minha cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="confirmacao_clube_id">Clube/Instituição/Escola</label>
					<select id="confirmacao_clube_id" class="clube_id this_is_select2 form-control">
						<option value="">--- Você pode escolher um clube/instituição/escola ---</option>
						@foreach(\App\Clube::all() as $clube)
							<option value="{{$clube->id}}">{{$clube->cidade->estado->pais->name}}-{{$clube->cidade->estado->name}}/{{$clube->cidade->name}} - {{$clube->name}}</option>
						@endforeach
					</select>
                    <button id="clubeNaoCadastradoInscricao" onclick="chamaCadastroClube(2)" class="btn btn-success">O meu clube/instituição/escola não está cadastrado</button>
				</div>
				<button id="confirmar_inscricao" class="btn btn-success">Confirmar Inscrição</button>
				<button id="cancelar_confirmacao" class="btn btn-danger">Cancelar Confirmação</button>
			</div>
		</div>
		<div class="box-footer">
		</div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->
<input type="hidden" id="temporary_enxadrista_id" class="temporary_enxadrista" />
<input type="hidden" id="temporary_estados_id" class="temporary_enxadrista" />
<input type="hidden" id="temporary_cidade_id" class="temporary_enxadrista" />
<input type="hidden" id="enxadrista_id" />
<input type="hidden" id="inscricao_id" />

<!-- Para processo de confirmação -->
<input type="hidden" id="temporary_confirmacao_categoria_id" class="temporary_confirmacao" />
<input type="hidden" id="temporary_confirmacao_cidade_id" class="temporary_confirmacao" />
<input type="hidden" id="temporary_confirmacao_clube_id" class="temporary_confirmacao" />

<!-- Para saber de onde veio o clique para o cadastro de estado -->
<input type="hidden" id="where_from_cadastro_estado" />

<!-- Para saber de onde veio o clique para o cadastro de cidade -->
<input type="hidden" id="where_from_cadastro_cidade" />

<!-- Para saber de onde veio o clique para o cadastro de clube -->
<input type="hidden" id="where_from_cadastro_clube" />
@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
	nome_enxadrista = "";
	last_timeOut = 0;
	tipo_documentos = false;
  	$(document).ready(function(){
		$(".this_is_select2").select2();

		$("#inscricao_clube_id").select2({
			ajax: {
				url: '{{url("/inscricao/v2/".$evento->id."/busca/clube")}}',
				delay: 250,
				processResults: function (data) {
					return {
						results: data.results
					};
				}
			}
		});

		$("#texto_pesquisa").on("keyup",function(){
			$("#pesquisa div").html("<div class='loading_circle_div'><span class='loading-circle'></span></div>");
			nome_enxadrista = $("#texto_pesquisa").val();
			if(last_timeOut > 0){
				clearTimeout(last_timeOut);
				console.log("Cancelando TimeOut: ".concat(last_timeOut));
				last_timeOut = 0;
			}
			last_timeOut = setTimeout(function(){
				last_timeOut = 0;
				$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/enxadrista/confirmacao")}}?q=".concat(nome_enxadrista),function(data){
					html = "";
					for (i = 0; i < data.results.length; i++) {
                        if(data.results[i].status == 2){
                            @if($evento->isPaid())
                                if(data.results[i].is_paid || data.results[i].is_free){
                                    html = html.concat("<a class='btn btn-success btn-large permitida_inscricao' onclick='selectConfirmarEnxadrista(").concat(data.results[i].inscricao_id).concat(",false)' title='Confirmar Enxadrista'>").concat(data.results[i].name).concat(" (Inscrito)</a><br/>");
                                }else{
                                    @if(
                                        !Auth::check()
                                    )
                                        html = html.concat("<a class='btn btn-warning btn-large' title='Enxadrista com Pagamento Pendente' disabled='disabled'>").concat(data.results[i].name).concat(" (PAGAMENTO PENDENTE - Para efetuar a confirmação, procure a organização.)</a><br/>");
                                    @else
                                        @if(
                                            Auth::user()->hasPermissionGlobal() ||
                                            Auth::user()->hasPermissionEventByPerfil($evento->id,[3,4]) ||
                                            Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[6])
                                        )
                                            html = html.concat("<a class='btn btn-warning btn-large permitida_inscricao' onclick='selectConfirmarEnxadrista(").concat(data.results[i].inscricao_id).concat(",false)' title='Confirmar Enxadrista com Pagamento Pendente'>").concat(data.results[i].name).concat(" (PAGAMENTO PENDENTE)</a><br/>");
                                        @else
                                            html = html.concat("<a class='btn btn-warning btn-large' title='Enxadrista com Pagamento Pendente' disabled='disabled'>").concat(data.results[i].name).concat(" (PAGAMENTO PENDENTE - Para efetuar a confirmação, procure a organização.)</a><br/>");
                                        @endif
                                    @endif
                                }
                            @else
                                html = html.concat("<a class='btn btn-success btn-large permitida_inscricao' onclick='selectConfirmarEnxadrista(").concat(data.results[i].inscricao_id).concat(",false)' title='Confirmar Enxadrista'>").concat(data.results[i].name).concat(" (Inscrito)</a><br/>");
                            @endif
                        }else{
                            html = html.concat("<a class='btn btn-danger btn-large' title='Enxadrista Confirmado' disabled='disabled'>").concat(data.results[i].name).concat(" (CONFIRMADO - Para desconfirmar, favor avisar a organização)</a><br/>");
                        }
						html = html.concat("Informações: ").concat(data.results[i].text).concat("<br/><br/>");
					}
					if(data.results.length == 0){
						html = html.concat("<p>A pesquisa não retornou resultado.</p><br/>");
					}
					if(data.hasMore){
						html = html.concat("<p>Há um limte de até 30 nomes por consulta. Para permitir o cadastro de um novo enxadrista é necessário que o nome do enxadrista esteja completo para a pesquisa.</p><br/>");
					}
					$("#pesquisa div").html(html);
				});
			},"1000");
		});

		$("#cancelar_confirmacao").on("click",function(){
			Loading.enable(loading_default_animation, 800);
			$(".permitida_inscricao").removeAttr("disabled");

			zeraConfirmacao();
		});

		$("#confirmar_inscricao").on("click",function(){
			Loading.enable(loading_default_animation, 10000);

			enviarConfirmacao();
		});

		$("#clube_pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			verificaLiberaCadastro(-1);
			buscaEstados(-1,function(){
				buscaCidades(-1,false);
			})
		});
		$("#confirmacao_pais_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,10000);
			verificaLiberaCadastro(2);
			buscaEstados(2,function(){
				buscaCidades(2,false);
			})
		});

		$("#clube_estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,800);
			buscaCidades(-1,false);
		});
		$("#confirmacao_estados_id").on("select2:select",function(){
			Loading.enable(loading_default_animation,800);
			buscaCidades(2,false);
		});





		$("#cadastrarCidade").on("click",function(){
			salvarCadastroCidade();
		});
		$("#cadastrarEstado").on("click",function(){
			salvarCadastroEstado();
		});
		$("#cadastrarClube").on("click",function(){
			salvarCadastroClube();
		});
		$("#go_to_inscricao").on("click",function(){
			goToInscricao();
		});
        @if($go_to_inscricao)
            goToInscricao();
        @endif
  	});

	fields = "";
	function selectConfirmarEnxadrista(id,callback_on_ok){
    	Loading.enable(loading_default_animation, 10000);
		$("#inscricao_id").val(id);
		$(".cadastro_enxadrista_input").removeAttr("disabled");
		$(".cadastro_enxadrista_select").removeAttr("disabled");

		$("#texto_pesquisa").attr("disabled","disabled");
		$(".permitida_inscricao").attr("disabled","disabled");

		$.getJSON("{{url("/inscricao/v2/".$evento->id."/inscricao/get/")}}/".concat($("#inscricao_id").val()).concat("/public"),function(data){
			if(data.ok == 1){
				$("#enxadrista_id").val(data.data.enxadrista_id);
				$("#temporary_confirmacao_categoria_id").val(data.data.categoria.id);
				$("#temporary_confirmacao_cidade_id").val(data.data.cidade.id);
				$("#temporary_confirmacao_clube_id").val(data.data.clube.id);

				$("#enxadrista_confirmar_id").html(data.data.id);
				$("#enxadrista_confirmar_nome").html(data.data.name);
				$("#enxadrista_confirmar_born").html(data.data.born);
				$("#enxadrista_confirmar_id_cbx").html(data.data.cbx_id);
				$("#enxadrista_confirmar_id_fide").html(data.data.fide_id);
				$("#enxadrista_confirmar_id_lbx").html(data.data.lbx_id);

                @if($evento->isPaid())
                    if(data.data.is_paid){
				        $("#enxadrista_confirmar_pagamento_status").html("Confirmado.");
                    }else{
                        if(data.data.is_free){
    				        $("#enxadrista_confirmar_pagamento_status").html("Gratuidade (Categoria Gratuita).");
                        }else{
    				        $("#enxadrista_confirmar_pagamento_status").html("<span style='color:red; font-weight:bold;'>PENDENTE</span>.");
                        }
                    }
                @endif

				$('#confirmacao_categoria_id').html("").trigger('change');
				for (i = 0; i < data.data.categorias.length; i++) {
					var newOptionCategoria = new Option(data.data.categorias[i].name, data.data.categorias[i].id, false, false);
					$('#confirmacao_categoria_id').append(newOptionCategoria).trigger('change');
					if(data.data.categorias.length == 1){
						if($("#temporary_confirmacao_categoria_id").val() == data.data.categorias[i].id){
							$('#confirmacao_categoria_id').val($("#temporary_confirmacao_categoria_id").val()).trigger('change');
						}else{
							$('#confirmacao_categoria_id').val(data.data.categorias[i].id).trigger('change');
						}
						$("#confirmacao_categoria_id").attr("disabled","disabled").change();
					}else{
						if(i+1 == data.data.categorias.length){
							$('#confirmacao_categoria_id').val($("#temporary_confirmacao_categoria_id").val()).trigger('change');
						}
						$("#confirmacao_categoria_id").removeAttr("disabled").change();
					}
				}
				$("#confirmacao_pais_id").val(data.data.cidade.estado.pais.id).change();
				setTimeout(function(){
					buscaEstados(2,false,function(){
						$("#confirmacao_estados_id").val(data.data.cidade.estado.id).change();
						setTimeout(function(){
							buscaCidades(2,function(){
								$("#confirmacao_cidade_id").val(data.data.cidade.id).change();
							});
						},200);
					});
					verificaLiberaCadastro(2);
				},200);

				if(data.data.clube.id != 0){
					var newOptionClube = new Option(data.data.clube.name, data.data.clube.id, false, false);
					$('#confirmacao_clube_id').append(newOptionClube).trigger('change');
					$("#confirmacao_clube_id").val(data.data.clube.id).change();
				}else{
                    $("#confirmacao_clube_id").val(0).change();
                }

				$("#form_pesquisa").css("display","none");
				$("#pesquisa").css("display","none");
				$("#enxadrista").css("display","none");
				$("#inscricao").css("display","none");
				$("#confirmacao").css("display","");
                $("#confirmacao_categoria_conferida").removeAttr('checked');
                $("#confirmacao_categoria_conferida").prop("checked",false);

				if(callback_on_ok){
					callback_on_ok();
					setTimeout(function(){
						$("#enxadrista_id").val("");
						$("#temporary_confirmacao_categoria_id").val("");
						$("#temporary_confirmacao_cidade_id").val("");
						$("#temporary_confirmacao_clube_id").val("");
						Loading.destroy();
					},1000);
				}
			}else{
				$("#texto_pesquisa").removeAttr("disabled");
				$(".permitida_inscricao").removeAttr("disabled","disabled");

                $("#alertsMessage").html(data.message);
                $("#alerts").modal();

                setTimeout(function(){
                    Loading.destroy();
                },1000);
			}

		})
		.fail(function(){
			$("#alertsMessage").html("Ocorreu um erro inesperado. Por favor, tente novamente mais tarde.");
			$("#alerts").modal();
			$("#texto_pesquisa").removeAttr("disabled");
			$(".permitida_inscricao").removeAttr("disabled","disabled");
			Loading.destroy();
		});

	}

	function buscaEstados(place,buscaCidade,callback){
		if(place == -2){
			// CADASTRO DE CIDADE
			$('#cidade_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#cidade_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#cidade_estados_id').append(newOptionEstado).trigger('change');
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
		}else if(place == -1){
			// CADASTRO DE CLUBE
			$('#clube_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#clube_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#clube_estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}else if(place == 0){
			// CADASTRO DE ENXADRISTA
			$('#estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}else if(place == 1){
			// INSCRIÇÃO
			$('#inscricao_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#inscricao_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#inscricao_estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}else if(place == 2){
			// CONFIRMAÇÃO DE INSCRIÇÃO
			$('#confirmacao_estados_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#confirmacao_pais_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#confirmacao_estados_id').append(newOptionEstado).trigger('change');
					if(i + 1 == data.results.length){
						if(callback){
							callback();
						}
						if(buscaCidade){
							buscaCidades(place,false);
						}
					}
				}
				if(data.results.length == 0){
					if(callback){
						callback();
					}
					if(buscaCidade){
						buscaCidades(place,false);
					}
				}
			});
		}
	}

	function buscaCidades(place, callback){
		if(place == -1){
			$('#clube_cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#clube_estados_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#clube_cidade_id').append(newOptionCidade).trigger('change');
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
		}else if(place == 0){
			$('#cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#estados_id").val()),function(data){
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
		}else if(place == 1){
			$('#inscricao_cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#inscricao_estados_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#inscricao_cidade_id').append(newOptionCidade).trigger('change');
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
		}else if(place == 2){
			$('#confirmacao_cidade_id').html("").trigger('change');
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#confirmacao_estados_id").val()),function(data){
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#confirmacao_cidade_id').append(newOptionCidade).trigger('change');
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
	}

	function verificaLiberaCadastro(place){
		if(place == 0){
			if($("#pais_id").val() == 33){
				$("#estadoNaoCadastradoEnxadrista").css("display","none");
				$("#cidadeNaoCadastradaEnxadrista").css("display","none");

			}else{
				$("#estadoNaoCadastradoEnxadrista").css("display","");
				$("#cidadeNaoCadastradaEnxadrista").css("display","");
			}
		}else if(place == 1){
			if($("#inscricao_pais_id").val() == 33){
				$("#estadoNaoCadastradoInscricao").css("display","none");
				$("#cidadeNaoCadastradaInscricao").css("display","none");

			}else{
				$("#estadoNaoCadastradoInscricao").css("display","");
				$("#cidadeNaoCadastradaInscricao").css("display","");
			}
		}else if(place == 2){
			if($("#confirmacao_pais_id").val() == 33){
				$("#estadoNaoCadastradoConfirmacao").css("display","none");
				$("#cidadeNaoCadastradaConfirmacao").css("display","none");

			}else{
				$("#estadoNaoCadastradoConfirmacao").css("display","");
				$("#cidadeNaoCadastradaConfirmacao").css("display","");
			}
		}
		Loading.destroy();
	}

	function enviarConfirmacao(){
		var data = "evento_id={{$evento->id}}&inscricao_id=".concat($("#inscricao_id").val()).concat("&categoria_id=").concat($("#confirmacao_categoria_id").val()).concat("&cidade_id=").concat($("#confirmacao_cidade_id").val()).concat("&clube_id=").concat($("#confirmacao_clube_id").val());
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif

        if($("#confirmacao_categoria_conferida").is(":checked")){
            data = data.concat("&categoria_conferida=true");
        }
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/inscricao/confirmar/public")}}",
			data: data,
			dataType: "json",
			success: function(data){
				Loading.destroy();
				if(data.ok == 1){
					zeraConfirmacao();
					$("#texto_pesquisa").val("");
					$("#pesquisa div").html("");
					if(data.updated == 0){
						$("#successMessage").html("<strong>Sua inscrição foi confirmada com sucesso!</strong>");
					}else if(data.updated == 1){
						$("#successMessage").html("<strong>A inscrição foi confirmada e o cadastro de enxadrista atualizado com sucesso!</strong>");
					}
					$("#success").modal();
				}else{
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();
				}
			}
		});
	}

	function zeraConfirmacao(){
		$("#enxadrista_id").val("");
		$("#enxadrista_confirmar_id").html("");
		$("#enxadrista_confirmar_nome").html("");
		$("#enxadrista_confirmar_born").html("");
		$('#confirmacao_categoria_id').html("").trigger('change');
		$("#confirmacao_pais_id").val(0).change();


		$("#confirmacao").css("display","none");
		$("#form_pesquisa").css("display","");
		$("#pesquisa").css("display","");
		$("#texto_pesquisa").removeAttr("disabled");
	}

	function zeraCidade(){
		$("#where_from_cadastro_cidade").val("");
		$("#cidade_pais_id").val("");
		$("#cidade_estados_id").val("");
		$("#cidade_nome").val("");
	}

	function zeraClube(){
		$("#where_from_cadastro_clube").val("");
		$("#clube_pais_id").val("");
		$("#clube_estados_id").val("");
		$("#clube_cidade_id").val("");
		$("#clube_nome").val("");
	}


	// CADASTRO DE CIDADE
	function chamaCadastroCidade(whereFrom){
		$("#where_from_cadastro_cidade").val(whereFrom);
		if(whereFrom == 0){
			$("#cidade_pais_id").val($("#pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-2,false,function(){
					$("#cidade_estados_id").val($("#estados_id").val()).change();
				});
			},200);
		}
		if(whereFrom == 1){
			$("#cidade_pais_id").val($("#inscricao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-2,false,function(){
					$("#cidade_estados_id").val($("#inscricao_estados_id").val()).change();
				});
			},200);
		}
		if(whereFrom == 2){
			$("#cidade_pais_id").val($("#confirmacao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-2,false,function(){
					$("#cidade_estados_id").val($("#confirmacao_estados_id").val()).change();
				});
			},200);
		}
		$("#novaCidade").modal();
	}

	function salvarCadastroCidade(){
		Loading.enable(loading_default_animation,10000);
		var data = "estados_id=".concat($("#cidade_estados_id").val()).concat("&name=").concat($("#cidade_nome").val());
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/cidade/nova")}}",
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					Loading.destroy();
					$("#successMessage").html("<strong>A cidade foi cadastrada com sucesso!</strong>");
					$("#success").modal();
					$("#novaCidade").modal('hide');

					if($("#where_from_cadastro_cidade").val() == 0){
						$("#pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(0,false,function(){
								$("#estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(0,function(){
										Loading.destroy();
										$("#cidade_id").val(data.cidade_id).change();
										$("#novaCidade").modal('hide');
									});
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_cidade").val() == 1){
						$("#inscricao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(1,false,function(){
								$("#inscricao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(1,function(){
										Loading.destroy();
										$("#inscricao_cidade_id").val(data.cidade_id).change();
										$("#novaCidade").modal('hide');
									});
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_cidade").val() == 2){
						$("#confirmacao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(2,false,function(){
								$("#confirmacao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(2,function(){
										Loading.destroy();
										$("#confirmacao_cidade_id").val(data.cidade_id).change();
										$("#novaCidade").modal('hide');
									});
								},200);
							});
						},200);
					}

					setTimeout(function(){
						$("#novaCidade").modal('hide');
						zeraCidade();
					},1000);
				}else{
					if(data.registred == 1){
						if($("#where_from_cadastro_cidade").val() == 0){
							$("#pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(0,false,function(){
									$("#estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(0,function(){
											Loading.destroy();
											$("#cidade_id").val(data.cidade_id).change();
											$("#novaCidade").modal('hide');
										});
									},200);
								});
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 1){
							$("#inscricao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(1,false,function(){
									$("#inscricao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(1,function(){
											Loading.destroy();
											$("#inscricao_cidade_id").val(data.cidade_id).change();
											$("#novaCidade").modal('hide');
										});
									},200);
								})
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 2){
							$("#confirmacao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(2,false,function(){
									$("#confirmacao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(2,function(){
											Loading.destroy();
											$("#confirmacao_cidade_id").val(data.cidade_id).change();
											$("#novaCidade").modal('hide');
										});
									},200);
								})
							},200);
						}

						setTimeout(function(){
							$("#novaCidade").modal('hide');
							zeraCidade();
						},1000);
					}else{
						Loading.destroy();
					}
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();

				}
			}
		});
	}


	// CADASTRO DE ESTADO
	function chamaCadastroEstado(whereFrom){
		$("#where_from_cadastro_estado").val(whereFrom);
		if(whereFrom == 0){
			$("#estado_pais_id").val($("#pais_id").val()).change();
		}
		if(whereFrom == 1){
			$("#estado_pais_id").val($("#inscricao_pais_id").val()).change();
		}
		if(whereFrom == 2){
			$("#estado_pais_id").val($("#confirmacao_pais_id").val()).change();
		}
		$("#novoEstado").modal();
	}

	function salvarCadastroEstado(){
		Loading.enable(loading_default_animation,10000);
		var data = "pais_id=".concat($("#estado_pais_id").val()).concat("&name=").concat($("#estado_nome").val());
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/estado/novo")}}",
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					Loading.destroy();
					$("#successMessage").html("<strong>O estado foi cadastrado com sucesso!</strong>");
					$("#success").modal();
					$("#novoEstado").modal('hide');

					if($("#where_from_cadastro_estado").val() == 0){
						$("#pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(0,false,function(){
								$("#estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(0,false);
									$("#novoEstado").modal('hide');
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_estado").val() == 1){
						$("#inscricao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(1,false,function(){
								$("#inscricao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(1,false);
									$("#novoEstado").modal('hide');
								},200);
							});
						},200);
					}else if($("#where_from_cadastro_estado").val() == 2){
						$("#confirmacao_pais_id").val(data.pais_id).change();
						setTimeout(function(){
							buscaEstados(2,false,function(){
								$("#confirmacao_estados_id").val(data.estados_id).change();
								setTimeout(function(){
									buscaCidades(2,false);
									$("#novoEstado").modal('hide');
								},200);
							});
						},200);
					}

					setTimeout(function(){
						$("#novoEstado").modal('hide');
						zeraCidade();
					},1000);
				}else{
					if(data.registred == 1){
						if($("#where_from_cadastro_cidade").val() == 0){
							$("#pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(0,false,function(){
									$("#estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(0,false);
										$("#novoEstado").modal('hide');
									},200);
								});
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 1){
							$("#inscricao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(1,false,function(){
									$("#inscricao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(1,false);
										$("#novoEstado").modal('hide');
									},200);
								})
							},200);
						}else if($("#where_from_cadastro_cidade").val() == 2){
							$("#confirmacao_pais_id").val(data.pais_id).change();
							setTimeout(function(){
								buscaEstados(2,false,function(){
									$("#confirmacao_estados_id").val(data.estados_id).change();
									setTimeout(function(){
										buscaCidades(2,false);
										$("#novoEstado").modal('hide');
									},200);
								})
							},200);
						}

						setTimeout(function(){
							$("#novoEstado").modal('hide');
							zeraCidade();
						},1000);
					}else{
						Loading.destroy();
					}
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();

				}
			}
		});
	}

	// CADASTRO DE CLUBE
	function chamaCadastroClube(whereFrom){
		$("#where_from_cadastro_clube").val(whereFrom);
		if(whereFrom == 0){
			$("#cidade_pais_id").val($("#pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-1,false,function(){
					$("#clube_estados_id").val($("#estados_id").val()).change();
					setTimeout(function(){
						buscaCidades(-1,function(){
							$("#clube_cidade_id").val($("#cidade_id").val()).change();
						});
					},200);
				});
			},200);
		}
		if(whereFrom == 1){
			$("#clube_pais_id").val($("#inscricao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-1,false,function(){
					$("#clube_estados_id").val($("#inscricao_estados_id").val()).change();
					setTimeout(function(){
						buscaCidades(-1,function(){
							$("#clube_cidade_id").val($("#inscricao_cidade_id").val()).change();
						});
					},200);
				});
			},200);
		}
		if(whereFrom == 2){
			$("#clube_pais_id").val($("#confirmacao_pais_id").val()).change();
			setTimeout(function(){
				buscaEstados(-1,false,function(){
					$("#clube_estados_id").val($("#confirmacao_estados_id").val()).change();
					setTimeout(function(){
						buscaCidades(-1,function(){
							$("#clube_cidade_id").val($("#confirmacao_cidade_id").val()).change();
						});
					},200);
				});
			},200);
		}
		$("#novoClube").modal();
	}

	function salvarCadastroClube(){
		Loading.enable(loading_default_animation,10000);
		var data = "cidade_id=".concat($("#clube_cidade_id").val()).concat("&name=").concat($("#clube_nome").val());
		@if(isset($token))
			@if($token != "")
				data = data.concat("&token=").concat("{{$token}}");
			@endif
		@endif
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/v2/".$evento->id."/clube/novo")}}",
			data: data,
			dataType: "json",
			success: function(data){
				if(data.ok == 1){
					Loading.destroy();
					$("#successMessage").html("<strong>O clube/instituição/escola foi cadastrado com sucesso!</strong>");
					$("#success").modal();
					$("#novoClube").modal('hide');

					if($("#where_from_cadastro_clube").val() == 0){
						var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
						$('#clube_id').append(newOptionClube).trigger('change');
						setTimeout(function(){
							$("#clube_id").val(data.clube_id).change();
						},200);
					}else if($("#where_from_cadastro_clube").val() == 1){
						var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
						$('#inscricao_clube_id').append(newOptionClube).trigger('change');
						setTimeout(function(){
							$("#inscricao_clube_id").val(data.clube_id).change();
						},200);
					}else if($("#where_from_cadastro_clube").val() == 2){
						var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
						$('#confirmacao_clube_id').append(newOptionClube).trigger('change');
						setTimeout(function(){
							$("#confirmacao_clube_id").val(data.clube_id).change();
						},200);
					}

					setTimeout(function(){
						$("#novoClube").modal('hide');
						zeraClube();
					},1000);
				}else{
					if(data.registred == 1){
						if($("#where_from_cadastro_clube").val() == 0){
							var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
							$('#clube_id').append(newOptionClube).trigger('change');
							setTimeout(function(){
								Loading.destroy();
								$("#clube_id").val(data.clube_id).change();
							},200);
						}else if($("#where_from_cadastro_clube").val() == 1){
							var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
							$('#inscricao_clube_id').append(newOptionClube).trigger('change');
							setTimeout(function(){
								Loading.destroy();
								$("#inscricao_clube_id").val(data.clube_id).change();
							},200);
						}else if($("#where_from_cadastro_clube").val() == 2){
							var newOptionClube = new Option(data.cidade_nome.concat(" | ").concat(data.clube_id).concat(" - ").concat(data.clube_nome), data.clube_id, false, false);
							$('#confirmacao_clube_id').append(newOptionClube).trigger('change');
							setTimeout(function(){
								Loading.destroy();
								$("#confirmacao_clube_id").val(data.clube_id).change();
							},200);
						}

						setTimeout(function(){
							$("#novoClube").modal('hide');
							zeraClube();
						},1000);
					}else{
						Loading.destroy();
					}
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();

				}
			}
		});
	}

    function goToInscricao(){
        $('html, body').stop().animate({
            'scrollTop': $("#processo_inscricao").offset().top
        }, 600, 'swing');
    }
</script>
@endsection

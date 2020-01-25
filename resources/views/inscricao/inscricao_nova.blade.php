@extends('adminlte::page')

@section("title", "Efetuar Nova Inscrição")

@section('content_header')
  <h1>Efetuar Nova Inscrição</h1>
  </ol>
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
		#pesquisa{
			min-height: 400px;
		}
		#pesquisa ul li{
			font-size: 1.5rem;
		}
		.this_is_select2, .select2{
			width: 100% !important;
		}
	</style>
@endsection

@section("content")

<div class="modal fade modal-warning" id="novaCidade" tabindex="-1" role="dialog" aria-labelledby="alerts">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Cadastrar Nova Cidade</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Nome *</label>
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
            <h4 class="modal-title">Cadastrar Novo Clube</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="name">Nome *</label>
                    <input type="text" name="name" class="form-control" id="clube_nome" placeholder="Insira o Nome Completo do Clube" required="required">
                </div>
                <div class="form-group">
                    <label for="clube_cidade_id">Cidade *</label>
                    <select id="clube_cidade_id" class="form-control">
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
  <li role="presentation"><a href="/inscricao/{{$evento->id}}">Nova Inscrição</a></li>
  @if($evento->e_permite_visualizar_lista_inscritos_publica) <li role="presentation"><a href="/inscricao/visualizar/{{$evento->id}}">Visualizar Lista de Inscrições</a></li> @endif
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
			@if($evento->pagina)
				@if($evento->pagina->imagem) <img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 800px"/> <br/> @endif
				@if($evento->pagina->texto) {!!$evento->pagina->texto!!} <br/> @endif
				@if($evento->pagina->imagem || $evento->pagina->texto) <hr/> @endif
			@endif
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}}, 
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong> {{$evento->getDataInicio()}}<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
			@if($evento->maximo_inscricoes_evento) 
				<hr/>
				<strong>Total de Inscritos até o presente momento:</strong> {{$evento->quantosInscritos()}}.<br/>
				<strong>Limite de Inscritos:</strong> {{$evento->maximo_inscricoes_evento}}.<br/>
				<hr/>
			@endif
			@if($evento->getDataFimInscricoesOnline()) <h3><strong>Inscrições antecipadas até:</strong> {{$evento->getDataFimInscricoesOnline()}}.</h3>@endif
		</div>
	</div>
	
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Processo de Inscrição</h3>
			<div class="pull-right box-tools">
			</div>
		</div>
		<div class="box-body">
			<div id="form_pesquisa">
				<div class="alert alert-success" role="alert">
					<strong><h4>O processo para inscrição mudou... e para melhor!</h4></strong><br/>
					Comece digitando o nome do(a) enxadrista, e caso o(a) mesmo(a) já possua cadastro, ele irá aparecer logo abaixo em "Resultado da Pesquisa", aí é só clicar no nome do(a) mesmo(a) e continuar o processo.<br/>
					Caso ele(a) não apareça na lista, clique em "O(a) enxadrista não aparece na lista" para efetuar o cadastro dele(a).
				</div>
				<h3>Comece a Inserir o nome do enxadrista</h3>
				<input type="text" id="texto_pesquisa" class="form-control" placeholder="Comece a digitar o nome do enxadrista para efetuar a pesquisa..." val="" />
				<hr/>
			</div>
			<div id="pesquisa">
				<h3>Resultado da Pesquisa:</h3>
				<div>
					<p>Comece a digitar o nome do enxadrista para começar a pesquisa...</p>
				</div>
			</div>
			<div id="enxadrista" style="display:none">
				<h3>Cadastro de Enxadrista:</h3>
			</div>
			<div id="inscricao" style="display:none">
				<h3>Inscrição:</h3>
				<h4>Enxadrista: <span id="enxadrista_nome">XXXXXXXX</span></h4>
				<input type="hidden" id="enxadrista_id" />
				<div class="form-group">
					<label for="enxadrista_categoria_id">Categoria *</label>
					<select id="enxadrista_categoria_id" class="this_is_select2 form-control">
						<option value="">--- Selecione ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="enxadrista_pais_id">País *</label>
					<select id="enxadrista_pais_id" class="pais_id this_is_select2 form-control">
						<option value="">--- Selecione um país ---</option>
						@foreach(\App\Pais::all() as $pais)
							<option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
						@endforeach
					</select>
				</div>
				<div class="form-group">
					<label for="enxadrista_estado_id">Estado *</label>
					<select id="enxadrista_estados_id" class="estados_id this_is_select2 form-control">
						<option value="">--- Selecione um país primeiro ---</option>
					</select>
                    <button id="estadoNaoCadastradoInscricao" class="btn btn-success">O meu estado não está cadastrado</button>
				</div>
				<div class="form-group">
					<label for="cidade_id">Cidade *</label>
					<select id="enxadrista_cidade_id" class="cidade_id this_is_select2 form-control">
						<option value="">--- Selecione um estado primeiro ---</option>
					</select>
                    <button id="cidadeNaoCadastradaInscricao" class="btn btn-success">A minha cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="clube_id">Clube *</label>
					<select id="enxadrista_clube_id" class="clube_id this_is_select2 form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select>
                    <button id="clubeNaoCadastradoInscricao" class="btn btn-success">O meu clube não está cadastrado</button>
				</div>
				@foreach($evento->campos() as $campo)
					<div class="form-group">
						<label for="campo_personalizado_{{$campo->id}}">{{$campo->question}} @if($campo->is_required) * @endif </label>
						<select id="campo_personalizado_{{$campo->id}}" class="campo_personalizado form-control this_is_select2">
							<option value="">--- Selecione uma opção ---</option>
							@foreach($campo->opcoes->all() as $opcao)
								<option value="{{$opcao->id}}">{{$opcao->response}}</option>
							@endforeach
						</select>
					</div>
				@endforeach
				<div class="form-group">
					<label><input type="checkbox" id="regulamento_aceito"> Eu aceito o regulamento do {{$evento->grupo_evento->name}} integralmente.</label><br/>
					<label><input type="checkbox" id="termos_aceito"> Eu aceito o termo de uso da Plataforma de Gerenciamento de Circuitos de Xadrez - XadrezSuíço.</label>
				</div>
				<button id="enviar_inscricao" class="btn btn-success">Enviar Inscrição</button>
				<button id="cancelar_inscricao" class="btn btn-danger">Cancelar Inscrição</button>
			</div>
		</div>
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
	nome_enxadrista = "";
	last_timeOut = 0;
  	$(document).ready(function(){
		$(".this_is_select2").select2();
		$("#enxadrista_clube_id").select2({
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
			$("#pesquisa div").html("");
			nome_enxadrista = $("#texto_pesquisa").val();
			if(last_timeOut > 0){
				clearTimeout(last_timeOut);
				console.log("Cancelando TimeOut: ".concat(last_timeOut));
				last_timeOut = 0;
			}
			last_timeOut = setTimeout(function(){
				last_timeOut = 0;
				$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/enxadrista")}}?q=".concat(nome_enxadrista),function(data){
					html = "";
					for (i = 0; i < data.results.length; i++) {
						html = html.concat("<a class='btn btn-default btn-large' onclick='selectEnxadrista(").concat(data.results[i].id).concat(")'>").concat(data.results[i].text).concat("</a><br/><br/>");
					} 
					if(!data.hasMore){
						html = html.concat("<a class='btn btn-warning' onclick='novoEnxadrista()'>O(a) enxadrista não aparece na lista.</a><br/>");
					}
					$("#pesquisa div").html(html);
				});
			},"3000");
		});
		$("#cancelar_inscricao").on("click",function(){
			Loading.enable('double-bounce', 1000);
			
			$("#enxadrista_id").val("");
			$("#enxadrista_nome").html("");
			$('#enxadrista_categoria_id').html("").trigger('change');
			$("#enxadrista_pais_id").val(0).change();


			$("#inscricao").hide(300);
			$("#form_pesquisa").show(300);
			$("#pesquisa").show(300);
			$("#texto_pesquisa").removeAttr("disabled");
		});
  	});
	  
	function selectEnxadrista(id){
    	Loading.enable('double-bounce', 4000);
		$("#enxadrista_id").val(id);
		$("#texto_pesquisa").attr("disabled","disabled");
		
		$.getJSON("{{url("/inscricao/v2/".$evento->id."/enxadrista")}}/".concat($("#enxadrista_id").val()),function(data){
			if(data.ok == 1){
				$("#enxadrista_nome").html(data.data.name);
				$('#enxadrista_categoria_id').html("").trigger('change');
				for (i = 0; i < data.data.categorias.length; i++) {
					var newOptionCategoria = new Option(data.data.categorias[i].name, data.data.categorias[i].id, false, false);
					$('#enxadrista_categoria_id').append(newOptionCategoria).trigger('change');
					if(data.data.categorias.length == 1){
						$("#enxadrista_categoria_id").val(data.data.categorias[i].id).change();
						$("#enxadrista_categoria_id").attr("disabled","disabled").change();
					}
				}
				$("#enxadrista_pais_id").val(data.data.cidade.estado.pais.id).change();
				setTimeout(function(){
					buscaEstados(1);
					verificaLiberaCadastro(1);
					setTimeout(function(){
						$("#enxadrista_estados_id").val(data.data.cidade.estado.id).change();
						buscaCidades(1);
						setTimeout(function(){
							$("#enxadrista_cidade_id").val(data.data.cidade.id).change();
						},300);
					},300);
				},300);

				if(data.data.clube.id != 0){
					var newOptionClube = new Option(data.data.clube.name, data.data.clube.id, false, false);
					$('#enxadrista_clube_id').append(newOptionClube).trigger('change');
					$("#enxadrista_clube_id").val(data.data.clube.id).change();
				}
			
				

				$("#form_pesquisa").hide(300);
				$("#pesquisa").hide(300);
				$("#inscricao").show(300);
			}else{
				$("#texto_pesquisa").removeAttr("disabled");
			}
		});
	}

	function buscaEstados(place){
		if(place == 0){

		}else if(place == 1){
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/estado/")}}/".concat($("#enxadrista_pais_id").val()),function(data){
				$('#enxadrista_estados_id').html("").trigger('change');
				for (i = 0; i < data.results.length; i++) {
					var newOptionEstado = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#enxadrista_estados_id').append(newOptionEstado).trigger('change');
				}
			});
		}
	}

	function buscaCidades(place){
		if(place == 0){

		}else if(place == 1){
			$.getJSON("{{url("/inscricao/v2/".$evento->id."/busca/cidade/")}}/".concat($("#enxadrista_estados_id").val()),function(data){
				$('#enxadrista_cidade_id').html("").trigger('change');
				for (i = 0; i < data.results.length; i++) {
					var newOptionCidade = new Option("#".concat(data.results[i].id).concat(" - ").concat(data.results[i].text), data.results[i].id, false, false);
					$('#enxadrista_cidade_id').append(newOptionCidade).trigger('change');
				}
			});
		}
	}

	function verificaLiberaCadastro(place){
		if(place == 0){

		}else if(place == 1){
			if($("#enxadrista_pais_id").val() == 33){
				$("#estadoNaoCadastradoInscricao").hide(100);
				$("#cidadeNaoCadastradaInscricao").hide(100);
			}else{
				$("#estadoNaoCadastradoInscricao").show(100);
				$("#cidadeNaoCadastradaInscricao").show(100);
			}
		}
	}
</script>
@endsection

@extends('adminlte::page')

@php
        $permitido_edicao = false;
        if(
            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
            \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4])
        ){
            $permitido_edicao = true;
        }
@endphp

@if($permitido_edicao)
    @section("title", "Editar Inscrição")
@else
    @section("title", "Visualizar Inscrição")
@endif

@if($permitido_edicao)
    @section('content_header')
        <h1>Editar Inscrição</h1>
    @stop
@else
    @section('content_header')
        <h1>Visualizar Inscrição</h1>
    @stop
@endif


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
	</style>
@endsection

@section("content")

@if($permitido_edicao)
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
@endif
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/evento/{{$evento->id}}/torneios/{{$torneio->id}}/inscricoes">Voltar a Lista de Inscritos</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<div class="box box-primary" id="inscricao">
		<div class="box-header">
            @if($permitido_edicao)
			    <h3 class="box-title">Editar Inscrição</h3>
            @else
			    <h3 class="box-title">Visualizar Inscrição</h3>
            @endif
		</div>
	  <!-- form start -->
        @if($permitido_edicao) <form method="post"> @endif
			<div class="box-body">
				<div class="form-group">
					<label for="inscricao_id">Enxadrista</label>
					<select id="inscricao_id" class="form-control" disabled="disabled">
						<option value="{{$inscricao->enxadrista->id}}">{{$inscricao->enxadrista->name}}</option>
					</select>
				</div>
				<div class="form-group">
					<label for="categoria_id">Categoria *</label>
					<select id="categoria_id" name="categoria_id" class="form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
					</select>
				</div>
				<div class="form-group">
					<label for="cidade_id">Cidade *</label>
					<select id="cidade_id" name="cidade_id" class="cidade_id form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
					</select>
                    @if($permitido_edicao) <button id="cidadeNaoCadastradaInscricao" class="btn btn-success">A cidade não está cadastrada</button> @endif
				</div>
				<div class="form-group">
					<label for="clube_id">Clube *</label>
					<select id="clube_id" name="clube_id" class="clube_id form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
                        <option value="">Sem Clube</option>
					</select>
                    @if($permitido_edicao) <button id="clubeNaoCadastradoInscricao" class="btn btn-success">O clube não está cadastrado</button> @endif
				</div>
				@foreach($evento->campos->all() as $campo)
					<div class="form-group">
						<label for="campo_personalizado_{{$campo->campo->id}}">{{$campo->campo->question}} *</label>
						<select id="campo_personalizado_{{$campo->campo->id}}" class="campo_personalizado form-control" @if(!$permitido_edicao) disabled="disabled" @endif>
							<option value="">--- Selecione uma opção ---</option>
							@foreach($campo->campo->opcoes->all() as $opcao)
								<option value="{{$opcao->id}}">{{$opcao->response}}</option>
							@endforeach
						</select>
					</div>
				@endforeach
                @if($permitido_edicao)
                    <div class="form-group">
                        <label><input type="checkbox" id="atualizar_cadastro" name="atualizar_cadastro"> Atualizar Cadastro</label>
                    </div>
                @endif
				<div class="form-group">
					<label><input type="checkbox" id="desconsiderar_pontuacao_geral" name="desconsiderar_pontuacao_geral" @if(!$permitido_edicao) disabled="disabled" @endif @if(!$inscricao->desconsiderar_pontuacao_geral) checked="checked" @endif> Desconsiderar Inscrição para Pontuação Geral</label>
				</div>
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button type="submit" class="btn btn-success">Enviar</button>
				<input type="hidden" name="_token" value="{{ csrf_token() }}">
			</div>
        @if($permitido_edicao) </form> @endif
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
		function setInscricaoSelects(){
            $.getJSON("{{url("/evento/inscricao/".$evento->id."/confirmacao/getInfo/".$inscricao->id)}}",function(data){
                if(data.ok == 1){
                    
                    $("#categoria_id").html('<option value="">--- Selecione uma Categoria ---</option>');
                    $("#categoria_id").select2({
                        ajax: {
                            url: '{{url("/evento/inscricao/".$evento->id."/busca/categoria")}}?evento_id={{$evento->id}}&enxadrista_id='.concat(data.enxadrista.id),
                            delay: 250,
                            processResults: function (data) {
                                return {
                                    results: data.results
                                };
                            }
                        }
                    });


                    var newOptionCategoria = new Option(data.categoria.name, data.categoria.id, false, false);
                    $('#categoria_id').append(newOptionCategoria).trigger('change');
                    $("#categoria_id").val(data.categoria.id).change();

                    var newOptionCidade = new Option(data.cidade.name, data.cidade.id, false, false);
                    $('#cidade_id').append(newOptionCidade).trigger('change');
                    $("#cidade_id").val(data.cidade.id).change();

                    if(data.clube.id > 0){
                        var newOptionClube = new Option(data.clube.name, data.clube.id, false, false);
                        $('#clube_id').append(newOptionClube).trigger('change');
                        $("#clube_id").val(data.clube.id).change();
                    }else{
                        $("#clube_id").val(null).trigger('change');
                    }
                }else{
                    $("#alertsMessage").html(data.message);
                    $("#alerts").modal();
                }
            });

			$("#cidade_id").select2({
				ajax: {
					url: '{{url("/evento/inscricao/".$evento->id."/busca/cidade")}}',
					delay: 250,
					processResults: function (data) {
						return {
							results: data.results
						};
					}
				}
			});
			
			$("#clube_id").select2({
				ajax: {
					url: '{{url("/evento/inscricao/".$evento->id."/busca/clube")}}',
					delay: 250,
					processResults: function (data) {
						return {
							results: data.results
						};
					}
				}
			});
			$(".campo_personalizado").select2();
            @foreach($inscricao->opcoes->all() as $opcao)
                $("#campo_personalizado_{{$opcao->campo->id}}").val([{{$opcao->opcao->id}}]).change();
            @endforeach
			setCidadeClubeFromEnxadrista();
		}

		function setCidadeClubeFromEnxadrista(){
		// 	$("#inscricao_id").on("select2:select",function(){
		// 		$.getJSON("/evento/inscricao/{{$evento->id}}/enxadrista/getCidadeClube/".concat($("#inscricao_id").val()),function(data){
		// 			if(data.ok == 1){
		// 				var newOptionCidade = new Option(data.cidade.name, data.cidade.id, false, false);
		// 				$('#cidade_id').append(newOptionCidade).trigger('change');
		// 				$("#cidade_id").val(data.cidade.id).change();
		// 				if(data.clube.id > 0){
		// 					var newOptionClube = new Option(data.clube.name, data.clube.id, false, false);
		// 					$('#clube_id').append(newOptionClube).trigger('change');
		// 					$("#clube_id").val(data.clube.id).change();
		// 				}else{
		// 					$("#clube_id").val(null).trigger('change');
		// 				}
		// 			}else{
		// 				$("#alertsMessage").html(data.message);
		// 				$("#alerts").modal();
		// 			}
		// 		});
		// 	});
		}

        function sendNovaCidade(select_id,data){
            $.ajax({
                type: "post",
                url: "{{url("/evento/inscricao/".$evento->id."/cidade/nova")}}",
                data: data,
                dataType: "json",
                success: function(data){
                    if(data.ok == 1){
                        $("#novaCidade").modal("hide");
                        setTimeout(function(){
                            var newOptionCidade = new Option(data.cidade.name, data.cidade.id, false, false);
                            $('#'.concat(select_id)).append(newOptionCidade).trigger('change');
                            $("#".concat(select_id)).val(data.cidade.id).change();

                            $("#successMessage").html("A Cidade foi cadastrada com sucesso!");
                            $("#success").modal("show");
                        },600);
                    }else{
                        if(data.registred == 1){
                            $("#novaCidade").modal("hide");

                            var newOptionCidade = new Option(data.cidade.name, data.cidade.id, false, false);
                            $('#'.concat(select_id)).append(newOptionCidade).trigger('change');
                            $("#".concat(select_id)).val(data.cidade.id).change();
                        }
                        $("#alertsMessage").html(data.message);
                        $("#alerts").modal();
                    }
                }
            });
        }
        function sendNovoClube(select_id,data){
            $.ajax({
                type: "post",
                url: "{{url("/evento/inscricao/".$evento->id."/clube/novo")}}",
                data: data,
                dataType: "json",
                success: function(data){
                    if(data.ok == 1){
                        $("#novoClube").modal("hide");
                        setTimeout(function(){
                            var newOptionClube = new Option(data.clube.name, data.clube.id, false, false);
                            $('#'.concat(select_id)).append(newOptionClube).trigger('change');
                            $("#".concat(select_id)).val(data.clube.id).change();

                            $("#successMessage").html("O Clube foi cadastrado com sucesso!");
                            $("#success").modal("show");
                        },600);
                    }else{
                        if(data.registred == 1){
                            $("#novoClube").modal("hide");
                            
                            var newOptionclube = new Option(data.clube.name, data.clube.id, false, false);
                            $('#'.concat(select_id)).append(newOptionclube).trigger('change');
                            $("#".concat(select_id)).val(data.clube.id).change();
                        }
                        $("#alertsMessage").html(data.message);
                        $("#alerts").modal();
                    }
                }
            });
        }

        
        $("#born").mask('00/00/0000');
        setInscricaoSelects();


        $("#cidadeNaoCadastradaInscricao").on("click",function(){
            $("#cidade_nome").val("");
            $("#novaCidade").modal("show");

            $("#cadastrarCidade").on("click",function(){
                sendNovaCidade("cidade_id","name=".concat($("#cidade_nome").val()));
            });
        });
        $("#clubeNaoCadastradoInscricao").on("click",function(){
            $("#clube_nome").val("");
            $("#clube_cidade_id").val("");
            $("#novoClube").modal("show");
            setTimeout(function(){
                $("#clube_cidade_id").select2({
                    ajax: {
                        url: '{{url("/evento/inscricao/".$evento->id."/busca/cidade")}}',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    }
                });
            },300);

            $("#cadastrarClube").on("click",function(){
                sendNovoClube("clube_id","name=".concat($("#clube_nome").val()).concat("&cidade_id=").concat($("#clube_cidade_id").val()));
            });
        });
  });
</script>
@endsection

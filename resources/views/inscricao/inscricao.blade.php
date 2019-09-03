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
			<strong>Categorias:</strong><br/>
			@foreach($evento->categorias->all() as $categoria)
				{{$categoria->categoria->name}}, 
			@endforeach<br/>
			<strong>Cidade:</strong> {{$evento->cidade->name}}<br/>
			<strong>Local:</strong> {{$evento->local}}<br/>
			<strong>Data:</strong> {{$evento->getDataInicio()}}<br/>
			<strong>Maiores informações em:</strong> <a href="{{$evento->link}}" target="_blank">{{$evento->link}}</a><br/>
			@if($evento->getDataFimInscricoesOnline()) <h3><strong>Inscrições antecipadas até:</strong> {{$evento->getDataFimInscricoesOnline()}}.</h3>@endif
		</div>
	</div>
	<div class="box box-primary" id="vocePossuiCadastro">
		<div class="box-header">
			<h3 class="box-title">1º Passo - Você já possui cadastro?</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			{{env("MESSAGE_INSCRICAO","Se você já jogou alguma etapa dos Circuitos Regionais de Xadrez de 2017 e 2018, você já possui cadastro.")}}
		</div>
		<div class="box-footer">
			<button id="tenhoCadastro" class="btn btn-success btn-lg">Tenho Cadastro - Segue para o 3º Passo</button>
			<button id="naoTenhoCadastro" class="btn btn-warning btn-lg">Não Tenho Cadastro - Segue para o 2º Passo</button>
		</div>
	</div>
	<div class="box box-primary collapsed-box" id="naoPossuiCadastro">
		<div class="box-header">
			<h3 class="box-title">2º Passo - Cadastro de Novo Enxadrista</h3>
		</div>
	  <!-- form start -->
			<div class="box-body">
				<div class="form-group">
					<label for="name">Nome Completo *</label>
					<input type="text" name="name" class="form-control" id="name" placeholder="Insira o Nome Completo do(a) Enxadrista" required="required">
				</div>
				<div class="form-group">
					<label for="born">Data de Nascimento *</label>
					<input type="text" name="born" class="form-control" id="born" placeholder="Insira a Data de Nascimento do(a) Enxadrista" required="required">
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
					<div class="col-md-6">
						<div class="form-group">
							<label for="email">E-mail *</label>
							<input name="email" id="email" class="form-control" type="text" />
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="celular">Celular *</label>
							<input name="celular" id="celular" class="form-control" type="text" value="55" />
							<br/>
							<button type="button" id="celular_brasileiro" disabled="disabled" class="btn btn-success">Celular Brasileiro</button>
							<button type="button" id="celular_paraguaio" class="btn btn-success">Celular Paraguaio</button>
							<button type="button" id="celular_argentino" class="btn btn-success">Celular Argentino</button>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="enxadrista_cidade_id">Cidade *</label>
					<select id="enxadrista_cidade_id" class="cidade_id form-control">
						<option value="">--- Selecione uma cidade ---</option>
					</select><br/>
                    <button id="cidadeNaoCadastradaEnxadrista" class="btn btn-success">A minha cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="enxadrista_clube_id">Clube</label>
					<select id="enxadrista_clube_id" class="clube_id form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select><br/>
                    <button id="clubeNaoCadastradoEnxadrista" class="btn btn-success">O meu clube não está cadastrado</button>
				</div>
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button id="cadastrarEnxadrista" class="btn btn-success">Enviar</button>
			</div>
	</div>


	<div class="box box-primary collapsed-box" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">3º Passo - Inscrição</h3>
		</div>
	  <!-- form start -->
			<div class="box-body">
				<div class="form-group">
					<label for="enxadrista_id">Enxadrista *</label>
					<select id="enxadrista_id" class="form-control">
						<option value="">--- Selecione um enxadrista ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="categoria_id">Categoria *</label>
					<select id="categoria_id" class="form-control">
						<option value="">--- Selecione primeiramente um Enxadrista ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="cidade_id">Cidade *</label>
					<select id="cidade_id" class="cidade_id form-control">
						<option value="">--- Selecione uma cidade ---</option>
					</select>
                    <button id="cidadeNaoCadastradaInscricao" class="btn btn-success">A minha cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="clube_id">Clube *</label>
					<select id="clube_id" class="clube_id form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select>
                    <button id="clubeNaoCadastradoInscricao" class="btn btn-success">O meu clube não está cadastrado</button>
				</div>
				@foreach($evento->campos->all() as $campo)
					<div class="form-group">
						<label for="campo_personalizado_{{$campo->campo->id}}">{{$campo->campo->question}} *</label>
						<select id="campo_personalizado_{{$campo->campo->id}}" class="campo_personalizado form-control">
							<option value="">--- Selecione uma opção ---</option>
							@foreach($campo->campo->opcoes->all() as $opcao)
								<option value="{{$opcao->id}}">{{$opcao->response}}</option>
							@endforeach
						</select>
					</div>
				@endforeach
				<div class="form-group">
					<label><input type="checkbox" id="regulamento_aceito"> Eu aceito o regulamento do {{$evento->grupo_evento->name}} integralmente.</label>
				</div>
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button id="enviarInscricao" class="btn btn-success">Enviar Inscrição</button>
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
  	$(document).ready(function(){
		function setInscricaoSelects(){
			$("#enxadrista_id").select2({
				ajax: {
					url: '{{url("/inscricao/".$evento->id."/busca/enxadrista")}}?evento_id={{$evento->id}}',
					delay: 250,
					processResults: function (data) {
						return {
							results: data.results
						};
					}
				}
			});
			$("#enxadrista_id").on("change",function(){
				if($("#enxadrista_id").val() > 0){
					$("#categoria_id").html('<option value="">--- Selecione uma Categoria ---</option>');
					$("#categoria_id").select2({
						ajax: {
							url: '{{url("/inscricao/".$evento->id."/busca/categoria")}}?evento_id={{$evento->id}}&enxadrista_id='.concat($("#enxadrista_id").val()),
							delay: 250,
							processResults: function (data) {
								return {
									results: data.results
								};
							}
						}
					});
				}
			});

			$("#cidade_id").select2({
				ajax: {
					url: '{{url("/inscricao/".$evento->id."/busca/cidade")}}',
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
					url: '{{url("/inscricao/".$evento->id."/busca/clube")}}',
					delay: 250,
					processResults: function (data) {
						return {
							results: data.results
						};
					}
				}
			});

			$(".campo_personalizado").select2();

			$("#enviarInscricao").on("click",function(){
				var data = "evento_id={{$evento->id}}&enxadrista_id=".concat($("#enxadrista_id").val()).concat("&categoria_id=").concat($("#categoria_id").val()).concat("&cidade_id=").concat($("#cidade_id").val()).concat("&clube_id=").concat($("#clube_id").val());
				if($("#regulamento_aceito").is(":checked")){
					data = data.concat("&regulamento_aceito=true");
				}
				@foreach($evento->campos->all() as $campo)
					data = data.concat("&campo_personalizado_{{$campo->campo->id}}=").concat($("#campo_personalizado_{{$campo->campo->id}}").val());
				@endforeach
				$.ajax({
					type: "post",
					url: "{{url("/inscricao/".$evento->id."/inscricao")}}",
					data: data,
					dataType: "json",
					success: function(data){
						if(data.ok == 1){
							$("#inscricao").boxWidget('collapse');
							$("#successMessage").html("<strong>Sua inscrição foi efetuada com sucesso!</strong>");
							$("#success").modal();
						}else{
							$("#alertsMessage").html(data.message);
							$("#alerts").modal();
						}
					}
				});
			});
			setCidadeClubeFromEnxadrista();
		}

		function setCidadeClubeFromEnxadrista(){
			$("#enxadrista_id").on("select2:select",function(){
				$.getJSON("/inscricao/{{$evento->id}}/enxadrista/getCidadeClube/".concat($("#enxadrista_id").val()),function(data){
					if(data.ok == 1){
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
			});
		}

        function sendNovaCidade(select_id,data){
            $.ajax({
                type: "post",
                url: "{{url("/inscricao/".$evento->id."/cidade/nova")}}",
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
                url: "{{url("/inscricao/".$evento->id."/clube/novo")}}",
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
		$("#tenhoCadastro").on("click",function(){
			$("#vocePossuiCadastro").boxWidget('collapse');
			$("#inscricao").boxWidget('expand');
			setInscricaoSelects();
		});
		$("#naoTenhoCadastro").on("click",function(){
			$("#vocePossuiCadastro").boxWidget('collapse');
			$("#naoPossuiCadastro").boxWidget('expand');
			$("#born").mask('00/00/0000');
			$("#celular").mask('+00 (00) 00000-0000');
			$("#celular").val('+55');
			$("#sexos_id").select2();
			$("#enxadrista_cidade_id").select2({
				ajax: {
					url: '{{url("/inscricao/".$evento->id."/busca/cidade")}}',
					delay: 250,
					processResults: function (data) {
						return {
							results: data.results
						};
					}
				}
			});
			$("#enxadrista_clube_id").select2({
				ajax: {
					url: '{{url("/inscricao/".$evento->id."/busca/clube")}}',
					delay: 250,
					processResults: function (data) {
						return {
							results: data.results
						};
					}
				}
			});
			$("#enxadrista_cidade_id").on("select2:select",function(){
				$("#cadastrarEnxadrista").on("click",function(){
					var data = "name=".concat($("#name").val())
						.concat("&born=").concat($("#born").val())
						.concat("&sexos_id=").concat($("#sexos_id").val())
						.concat("&cbx_id=").concat($("#cbx_id").val())
						.concat("&fide_id=").concat($("#fide_id").val())
						.concat("&lbx_id=").concat($("#lbx_id").val())
						.concat("&email=").concat($("#email").val())
						.concat("&celular=").concat($("#celular").val())
						.concat("&cidade_id=").concat($("#enxadrista_cidade_id").val())
						.concat("&clube_id=").concat($("#enxadrista_clube_id").val());
					
					$.ajax({
						type: "post",
						url: "{{url("/inscricao/".$evento->id."/enxadrista/novo")}}",
						data: data,
						dataType: "json",
						success: function(data){
							if(data.ok == 1){
								$("#naoPossuiCadastro").boxWidget('collapse');
								$("#inscricao").boxWidget('expand');
								setInscricaoSelects();
								$("#successMessage").html("<strong>O cadastro do enxadrista foi efetuado com sucesso!</strong>");
								$("#success").modal();


								setTimeout(function(){
									var newOption = new Option($("#name").val().concat(" | ").concat($("#born").val()), data.enxadrista_id, false, false);
									$('#enxadrista_id').append(newOption).trigger('change');
									$("#enxadrista_id").val(data.enxadrista_id).change();
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
								},"800");
							}else{
								if(data.registred == 1){
									$("#naoPossuiCadastro").boxWidget('collapse');
									$("#inscricao").boxWidget('expand');
									setInscricaoSelects();
									setTimeout(function(){
										var newOption = new Option(data.enxadrista_name, data.enxadrista_id, false, false);
										$('#enxadrista_id').append(newOption).trigger('change');
										$("#enxadrista_id").val(data.enxadrista_id).change();
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
									},"800");
								}
								$("#alertsMessage").html(data.message);
								$("#alerts").modal();
							}
						}
					});
				});
			});
		});


        $("#cidadeNaoCadastradaEnxadrista").on("click",function(){
            $("#cidade_nome").val("");
            $("#novaCidade").modal("show");

            $("#cadastrarCidade").on("click",function(){
                sendNovaCidade("enxadrista_cidade_id","name=".concat($("#cidade_nome").val()));
            });
        });
        $("#clubeNaoCadastradoEnxadrista").on("click",function(){
            $("#clube_nome").val("");
            $("#clube_cidade_id").val("");
            $("#novoClube").modal("show");
            setTimeout(function(){
                $("#clube_cidade_id").select2({
                    ajax: {
                        url: '{{url("/inscricao/".$evento->id."/busca/cidade")}}',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    },
        			dropdownParent: $('#novoClube')
                });
            },300);

            $("#cadastrarClube").on("click",function(){
                sendNovoClube("enxadrista_clube_id","name=".concat($("#clube_nome").val()).concat("&cidade_id=").concat($("#clube_cidade_id").val()));
            });
        });



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
                        url: '{{url("/inscricao/".$evento->id."/busca/cidade")}}',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data.results
                            };
                        }
                    },
        			dropdownParent: $('#novoClube')
                });
            },300);

            $("#cadastrarClube").on("click",function(){
                sendNovoClube("clube_id","name=".concat($("#clube_nome").val()).concat("&cidade_id=").concat($("#clube_cidade_id").val()));
            });
        });

		$("#celular_brasileiro").on("click",function(){
			$("#celular_paraguaio").removeAttr("disabled");
			$("#celular_argentino").removeAttr("disabled");
			$("#celular_brasileiro").attr("disabled","disabled");
			$("#celular").mask('+00 (00) 00000-0000');
			$("#celular").val('+55');
		});
		$("#celular_paraguaio").on("click",function(){
			$("#celular_brasileiro").removeAttr("disabled");
			$("#celular_argentino").removeAttr("disabled");
			$("#celular_paraguaio").attr("disabled","disabled");
			$("#celular").mask('+000 (000) 000-000');
			$("#celular").val('+595');
		});
		$("#celular_argentino").on("click",function(){
			$("#celular_brasileiro").removeAttr("disabled");
			$("#celular_paraguaio").removeAttr("disabled");
			$("#celular_argentino").attr("disabled","disabled");
			$("#celular").mask('+00 (0000) 00-0000');
			$("#celular").val('+54');
		});
  	});
</script>
@endsection

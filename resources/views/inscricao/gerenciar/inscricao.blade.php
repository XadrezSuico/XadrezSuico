@extends('adminlte::page')

@section("title", "[ADMIN] Efetuar Nova Inscrição")

@section('content_header')
  <h1>[ADMIN] Efetuar Nova Inscrição</h1>
  </ol>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
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
  <li role="presentation"><a href="/evento">Voltar a Lista de Eventos</a></li>
  <!--<li role="presentation"><a href="/evento/inscricao/{{$evento->id}}">Nova Inscrição</a></li>-->
  <li role="presentation"><a href="/evento/inscricao/{{$evento->id}}/confirmacao">Confirmar Inscrições</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Evento: {{$evento->name}}</h3>
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
		</div>
	</div>
	<div class="box box-primary" id="naoPossuiCadastro">
		<div class="box-header">
			<h3 class="box-title">Cadastro de Novo Enxadrista</h3>
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
					<label for="enxadrista_cidade_id">Cidade *</label>
					<select id="enxadrista_cidade_id" class="cidade_id form-control">
						<option value="">--- Selecione uma cidade ---</option>
					</select><br/>
                    <button id="cidadeNaoCadastradaEnxadrista" class="btn btn-success">A cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="enxadrista_clube_id">Clube</label>
					<select id="enxadrista_clube_id" class="clube_id form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select><br/>
                    <button id="clubeNaoCadastradoEnxadrista" class="btn btn-success">O clube não está cadastrado</button>
				</div>

			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button id="cadastrarEnxadrista" class="btn btn-success">Enviar</button>
			</div>
	</div>


	<div class="box box-primary" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Inscrição</h3>
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
                    <button id="cidadeNaoCadastradaInscricao" class="btn btn-success">A cidade não está cadastrada</button>
				</div>
				<div class="form-group">
					<label for="clube_id">Clube *</label>
					<select id="clube_id" class="clube_id form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select>
                    <button id="clubeNaoCadastradoInscricao" class="btn btn-success">O clube não está cadastrado</button>
				</div>
				<div class="form-group">
					<label><input type="checkbox" id="confirmado"> Inscrição Confirmada</label>
				</div>
				<div class="form-group">
					<label><input type="checkbox" id="atualizar_cadastro"> Atualizar Cadastro</label>
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
					url: '{{url("/evento/inscricao/".$evento->id."/busca/enxadrista")}}?evento_id={{$evento->id}}',
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
							url: '{{url("/evento/inscricao/".$evento->id."/busca/categoria")}}?evento_id={{$evento->id}}&enxadrista_id='.concat($("#enxadrista_id").val()),
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

			$("#enviarInscricao").on("click",function(){
					$(this).attr("disabled","disabled");
					var data = "evento_id={{$evento->id}}&enxadrista_id=".concat($("#enxadrista_id").val()).concat("&categoria_id=").concat($("#categoria_id").val()).concat("&cidade_id=").concat($("#cidade_id").val()).concat("&clube_id=").concat($("#clube_id").val());
					if($("#confirmado").is(":checked")){
						data = data.concat("&confirmado=true");
					}
					if($("#atualizar_cadastro").is(":checked")){
						data = data.concat("&atualizar_cadastro=true");
					}
					$.ajax({
						type: "post",
						url: "{{url("/evento/inscricao/".$evento->id."/inscricao")}}",
						data: data,
						dataType: "json",
						success: function(data){
							if(data.ok == 1){
								$("#enxadrista_id").val(null).change();
								$("#categoria_id").val(null).change();
								$("#cidade_id").val(null).change();
								$("#clube_id").val(null).change();
								if(data.updated == 1){
									if(data.confirmed == 1){
										$("#successMessage").html("<strong>A inscrição foi efetuada e confirmada e o cadastro do enxadrista atualizado com sucesso!</strong>");
									}else{
										$("#successMessage").html("<strong>A inscrição foi efetuada e o cadastro do enxadrista atualizado com com sucesso!</strong>");
									}
								}else{
									if(data.confirmed == 1){
										$("#successMessage").html("<strong>A inscrição foi efetuada e confirmada com sucesso!</strong>");
									}else{
										$("#successMessage").html("<strong>A inscrição foi efetuada com sucesso!</strong>");
									}
								}
								$("#success").modal();
							}else{
								$("#alertsMessage").html(data.message);
								$("#alerts").modal();
							}
							$("#enviarInscricao").removeAttr("disabled");
						}
					});
			});
			setCidadeClubeFromEnxadrista();
		}

		function setCidadeClubeFromEnxadrista(){
			$("#enxadrista_id").on("select2:select",function(){
				$.getJSON("/evento/inscricao/{{$evento->id}}/enxadrista/getCidadeClube/".concat($("#enxadrista_id").val()),function(data){
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
		$("#enxadrista_cidade_id").select2({
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
		$("#enxadrista_clube_id").select2({
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
		$("#enxadrista_cidade_id").on("select2:select",function(){
			$("#cadastrarEnxadrista").on("click",function(){
				
				$(this).attr("disabled","disabled");
				var data = "name=".concat($("#name").val()).concat("&born=").concat($("#born").val()).concat("&cidade_id=").concat($("#enxadrista_cidade_id").val()).concat("&clube_id=").concat($("#enxadrista_clube_id").val());
				
				$.ajax({
					type: "post",
					url: "{{url("/evento/inscricao/".$evento->id."/enxadrista/novo")}}",
					data: data,
					dataType: "json",
					success: function(data){
						if(data.ok == 1){
							$('html,body').animate({
								scrollTop: $("#inscricao").offset().top
							}, 'slow');
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
								setInscricaoSelects();
								$('html,body').animate({
									scrollTop: $("#inscricao").offset().top
								}, 'slow');
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
						
						$("#cadastrarEnxadrista").removeAttr("disabled");
					}
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
		setInscricaoSelects();
  });
</script>
@endsection

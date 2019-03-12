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
	</style>
@endsection

@section("content")
<div class="modal fade modal-danger" id="alerts" tabindex="-1" role="dialog" aria-labelledby="alerts">
  <div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">ERRO!</h4>
			</div>
			<div class="modal-body">
			<span id="alertsMessage"></span>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div>
  </div>
</div>
<div class="modal fade modal-success" id="success" tabindex="-1" role="dialog" aria-labelledby="alerts">
  <div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title">SUCESSO!</h4>
			</div>
			<div class="modal-body">
			<span id="successMessage"></span>
			</div>
			<div class="modal-footer">
			<button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
			</div>
		</div>
  </div>
</div>
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/senha">Listar Todas</a></li>
</ul>
<div class="row">
  <!-- Left col -->
  <section class="col-lg-12 connectedSortable">
	<!-- general form elements -->
	<div class="box box-primary" id="vocePossuiCadastro">
		<div class="box-header">
			<h3 class="box-title">Você já possui cadastro?</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
			Se você já jogou alguma etapa dos Circuitos Regionais de Xadrez de 2017 e 2018, você já possui cadastro.
		</div>
		<div class="box-footer">
			<button id="tenhoCadastro" class="btn btn-success">Tenho Cadastro</button>
			<button id="naoTenhoCadastro" class="btn btn-warning">Não Tenho Cadastro</button>
		</div>
	</div>
	<div class="box box-primary collapsed-box" id="naoPossuiCadastro">
		<div class="box-header">
			<h3 class="box-title">Cadastro de Novo Enxadrista</h3>
		</div>
	  <!-- form start -->
			<div class="box-body">
				<div class="form-group">
					<label for="name">Nome Completo</label>
					<input type="text" name="name" class="form-control" id="name" placeholder="Insira o Nome Completo do(a) Enxadrista" required="required">
				</div>
				<div class="form-group">
					<label for="born">Data de Nascimento</label>
					<input type="text" name="born" class="form-control" id="born" placeholder="Insira a Data de Nascimento do(a) Enxadrista" required="required">
				</div>
				<div class="form-group">
					<label for="enxadrista_cidade_id">Cidade</label>
					<select id="enxadrista_cidade_id" class="form-control">
						<option value="">--- Selecione uma cidade ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="enxadrista_clube_id">Clube</label>
					<select id="enxadrista_clube_id" class="form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select>
				</div>
			</div>
			<!-- /.box-body -->

			<div class="box-footer">
				<button id="cadastrarEnxadrista" class="btn btn-success">Enviar</button>
			</div>
	</div>


	<div class="box box-primary collapsed-box" id="inscricao">
		<div class="box-header">
			<h3 class="box-title">Inscrição</h3>
		</div>
	  <!-- form start -->
			<div class="box-body">
				<div class="form-group">
					<label for="enxadrista_id">Enxadrista</label>
					<select id="enxadrista_id" class="form-control">
						<option value="">--- Selecione um enxadrista ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="categoria_id">Categoria</label>
					<select id="categoria_id" class="form-control">
						<option value="">--- Selecione primeiramente um Enxadrista ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="cidade_id">Cidade</label>
					<select id="cidade_id" class="form-control">
						<option value="">--- Selecione uma cidade ---</option>
					</select>
				</div>
				<div class="form-group">
					<label for="clube_id">Clube</label>
					<select id="clube_id" class="form-control">
						<option value="">--- Você pode escolher um clube ---</option>
					</select>
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
					url: '{{url("/inscricao/".$evento->id."/busca/enxadrista")}}',
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

			$("#enviarInscricao").on("click",function(){
					var data = "evento_id={{$evento->id}}&enxadrista_id=".concat($("#enxadrista_id").val()).concat("&categoria_id=").concat($("#categoria_id").val()).concat("&cidade_id=").concat($("#cidade_id").val()).concat("&clube_id=").concat($("#clube_id").val());
					
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
					var data = "name=".concat($("#name").val()).concat("&born=").concat($("#born").val()).concat("&cidade_id=").concat($("#enxadrista_cidade_id").val()).concat("&clube_id=").concat($("#enxadrista_clube_id").val());
					
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
										$("#clube_id").val(data.cidade.id).change();
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
											$("#clube_id").val(data.cidade.id).change();
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
  });
</script>
@endsection

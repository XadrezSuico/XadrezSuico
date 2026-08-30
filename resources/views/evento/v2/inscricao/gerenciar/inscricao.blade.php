@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.inscricao.gerenciar.inscricao')
@endsection

@push('styles')
    <style>
.display-none, .displayNone{
			display: none;
		}
    </style>
@endpush

@push('event-scripts')
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
			

			$(".campo_personalizado").select2();

			$("#enviarInscricao").on("click",function(){
					$(this).attr("disabled","disabled");
					var data = "evento_id={{$evento->id}}&enxadrista_id=".concat($("#enxadrista_id").val()).concat("&categoria_id=").concat($("#categoria_id").val()).concat("&cidade_id=").concat($("#cidade_id").val()).concat("&clube_id=").concat($("#clube_id").val());
					if($("#confirmado").is(":checked")){
						data = data.concat("&confirmado=true");
					}
					if($("#atualizar_cadastro").is(":checked")){
						data = data.concat("&atualizar_cadastro=true");
					}
					@foreach($evento->campos() as $campo)
						data = data.concat("&campo_personalizado_{{$campo->id}}=").concat($("#campo_personalizado_{{$campo->id}}").val());
					@endforeach
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
		$("#celular").mask('+00 (00) 00000-0000');
		$("#celular").val('+55');
		$("#sexos_id").select2();
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
		$("#cadastrarEnxadrista").on("click",function(){
			
			$(this).attr("disabled","disabled");
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
							setNullEnxadristaFields();
						},"800");
					}else{
						if(data.registred == 1){
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
								setNullEnxadristaFields();
							},"800");
						}
						$("#alertsMessage").html(data.message);
						$("#alerts").modal();
					}
					
					$("#cadastrarEnxadrista").removeAttr("disabled");
				}
			});
		});

		function setNullEnxadristaFields(){
			setTimeout(function(){
				$("#name").val("");
				$("#born").val("");
				$("#sexos_id").val("").change();
				$("#cbx_id").val("");
				$("#fide_id").val("");
				$("#email").val("");
				$("#celular").val("");
				$("#enxadrista_cidade_id").val("").change();
				$("#enxadrista_clube_id").val("").change();

			}, 200);
		}


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
                        url: '{{url("/evento/inscricao/".$evento->id."/busca/cidade")}}',
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
		setInscricaoSelects();
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
@endpush

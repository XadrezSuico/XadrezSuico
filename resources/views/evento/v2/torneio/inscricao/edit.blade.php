@extends('layouts.v2.event-page')

@section('event-page-content')
    @include('evento.v2._content.torneio.inscricao.edit')
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
@endpush

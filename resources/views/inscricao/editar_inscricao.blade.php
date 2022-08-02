@extends('adminlte::page')

@section("title", "Editar Inscrição")

@section('content_header')
  <h1>Editar Inscrição</h1>
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

<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/inscricao/{{$evento->id}}">Voltar ao Formulário de Nova Inscrição</a></li>
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
				@if($evento->pagina->imagem) <div style="width: 100%; text-align: center;"><img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 800px"/></div> <br/> @endif
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
				<strong>Limite de Inscritos:</strong> {{$evento->maximo_inscricoes_evento}}.<br/>
				<hr/>
			@endif
			@if($evento->getDataFimInscricoesOnline()) <h3><strong>Inscrições antecipadas até:</strong> {{$evento->getDataFimInscricoesOnline()}}.</h3>@endif
            @if($evento->is_lichess_integration)
                Informações do Lichess.org são atualizadas a cada 6 horas.
            @endif
        </div>
	</div>
	<div class="box box-primary">
		<div class="box-header">
			<h3 class="box-title">Editar Inscrição</h3>
			<div class="pull-right box-tools">
			</div>
		</div>

		<div class="box-body">
            <h4>ID: <span id="enxadrista_mostrar_id">{{$inscricao->enxadrista->id}}</span></h4>
            <h4>Nome Completo: <span id="enxadrista_mostrar_nome">{{$inscricao->enxadrista->name}}</span></h4>
            <h4>Data de Nascimento: <span id="enxadrista_mostrar_born">{{$inscricao->enxadrista->getNascimentoPrivado()}}</span></h4>
            <h4>ID CBX: <span id="enxadrista_mostrar_id_cbx">{{$inscricao->enxadrista->cbx_id}}</span></h4>
            <h4>ID FIDE: <span id="enxadrista_mostrar_id_fide">{{$inscricao->enxadrista->fide_id}}</span></h4>
            <h4>ID LBX: <span id="enxadrista_mostrar_id_lbx">{{$inscricao->enxadrista->lbx_id}}</span></h4>
            <hr/>
            <div class="form-group">
                <label for="inscricao_categoria_id" class="field-required">Categoria *</label>
                <select id="inscricao_categoria_id" class="this_is_select2 form-control" @if(!$inscricao->categoria->is_changeable) disabled @endif >
                    @if($inscricao->categoria->is_changeable)
                        <option value="">--- Selecione ---</option>
                        @foreach($categorias as $categoria)
                            <option value="{{$categoria->categoria->id}}">{{$categoria->categoria->name}}</option>
                        @endforeach
                    @else
                        <option value="{{$inscricao->categoria->id}}">{{$inscricao->categoria->name}}</option>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <label for="inscricao_pais_id" class="field-required">País *</label>
                <select id="inscricao_pais_id" class="pais_id this_is_select2 form-control">
                    <option value="">--- Selecione um país ---</option>
                    @foreach(\App\Pais::all() as $pais)
                        <option value="{{$pais->id}}">{{$pais->nome}} @if($pais->codigo_iso) ({{$pais->codigo_iso}}) @endif</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="inscricao_estados_id" class="field-required">Estado/Província *</label>
                <select id="inscricao_estados_id" class="estados_id this_is_select2 form-control">
                    <option value="">--- Selecione um país primeiro ---</option>
                </select>
            </div>
            <div class="form-group">
                <label for="inscricao_cidade_id" class="field-required">Cidade *</label>
                <select id="inscricao_cidade_id" class="cidade_id this_is_select2 form-control">
                    <option value="">--- Selecione um estado primeiro ---</option>
                </select>
            </div>
            <div class="form-group">
                <label for="inscricao_clube_id">Clube</label>
                <select id="inscricao_clube_id" class="clube_id this_is_select2 form-control">
                    <option value="">--- Você pode escolher um clube ---</option>
                    @foreach(\App\Clube::all() as $clube)
                        <option value="{{$clube->id}}">{{$clube->cidade->estado->pais->name}}-{{$clube->cidade->estado->name}}/{{$clube->cidade->name}} - {{$clube->name}}</option>
                    @endforeach
                </select>
            </div>
            @foreach($evento->campos() as $campo)
                <div class="form-group">
                    <label for="campo_personalizado_{{$campo->id}}" @if($campo->is_required) class="field-required" @endif>{{$campo->question}} @if($campo->is_required) * @endif </label>
                    <select id="campo_personalizado_{{$campo->id}}" class="campo_personalizado form-control this_is_select2">
                        <option value="">--- Selecione uma opção ---</option>
                        @foreach($campo->opcoes->all() as $opcao)
                            <option value="{{$opcao->id}}">{{$opcao->response}}</option>
                        @endforeach
                    </select>
                </div>
            @endforeach
		</div>
        <div class="box-footer">
            <button type="button" id="save" class="btn btn-success">Editar Inscrição</button>
        </div>
	</div>

  </section>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
@foreach(array(
    "https://cdn.datatables.net/buttons/1.5.6/js/dataTables.buttons.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.flash.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js",
    "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.html5.min.js",
    "https://cdn.datatables.net/buttons/1.5.6/js/buttons.print.min.js"
    ) as $url)
<script type="text/javascript" src="{{$url}}"></script>
@endforeach
<script type="text/javascript">
    $(document).ready(function(){
		Loading.enable(loading_default_animation,10000);
        $(".this_is_select2").select2();


        $("#inscricao_pais_id").val({{$inscricao->cidade->estado->pais->id}}).change();
        setTimeout(function(){
            buscaEstados(1,false,function(){
                $("#inscricao_estados_id").val({{$inscricao->cidade->estado->id}}).change();
                setTimeout(function(){
                    buscaCidades(1,function(){
                        $("#inscricao_cidade_id").val({{$inscricao->cidade->id}}).change();
                        setTimeout(function(){
                            Loading.destroy();
                        },500);
                    });
                },200);
            });
        },200);

        @if($inscricao->clube)
            var newOptionClube = new Option("{{$inscricao->clube->name}}", {{$inscricao->clube->id}}, false, false);
            $('#inscricao_clube_id').append(newOptionClube).trigger('change');
            $("#inscricao_clube_id").val({{$inscricao->clube->id}}).change();
        @endif

        $("#inscricao_categoria_id").val({{$inscricao->categoria->id}}).change();


        @foreach($evento->campos() as $campo)
            @if($inscricao->hasOpcao($campo->id))
                $("#campo_personalizado_{{$campo->id}}").val({{$inscricao->getOpcao($campo->id)->opcao->id}}).change();
            @endif
        @endforeach

        $("#save").on("click",function(){
            send();
        });
    });




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


    function send(){
		Loading.enable(loading_default_animation,10000);
		var data = "categoria_id=".concat($("#inscricao_categoria_id").val()).concat("&cidade_id=").concat($("#inscricao_cidade_id").val()).concat("&clube_id=").concat($("#inscricao_clube_id").val());
		@foreach($evento->campos() as $campo)
			data = data.concat("&campo_personalizado_{{$campo->id}}=").concat($("#campo_personalizado_{{$campo->id}}").val());
		@endforeach
		$.ajax({
			type: "post",
			url: "{{url("/inscricao/".$inscricao->uuid."/editar")}}",
			data: data,
			dataType: "json",
			success: function(data){
				Loading.destroy();
				if(data.ok == 1){
                    $("#successMessage").html("Inscrição editada com sucesso!");
					$("#success").modal();
				}else{
					$("#alertsMessage").html(data.message);
					$("#alerts").modal();
				}
			}
		});
    }
</script>
@endsection

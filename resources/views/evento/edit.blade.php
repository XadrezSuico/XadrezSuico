@extends('adminlte::page')

@section("title", "Dashboard de Evento")

@section('content_header')
    @if($evento->parent_event)
    <h5>Filho do Evento: {{$evento->parent_event->name}}</h5>
    @endif
    <h1>Dashboard de Evento: {{$evento->name}}</h1>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
		.width-100{
			width: 100% !important;
		}
        /* Switch estilo Tabler */
        .form-switch {
            position: relative;
            display: inline-block;
            width: 40px;
            height: 20px;
            flex-shrink: 0;
        }
        .form-switch input {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }
        .form-switch .form-check-input {
            position: absolute;
            pointer-events: none;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 20px;
        }
        .form-switch .form-check-input:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 2px;
            bottom: 2px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        .form-switch input:checked + .form-check-input {
            background-color: #206bc4;
        }
        .form-switch input:checked + .form-check-input:before {
            transform: translateX(20px);
        }
        .form-switch input:disabled + .form-check-input {
            opacity: 0.5;
            cursor: not-allowed;
        }
        .switch-label {
            margin-left: 10px;
            vertical-align: middle;
        }
        .event-overview-strip {
            border-top: 3px solid #3c8dbc;
        }
        .event-overview-kpi-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 4px 8px;
        }
        .event-overview-kpi {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            min-width: 70px;
        }
        .event-overview-kpi-value {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.1;
        }
        .event-overview-kpi-label {
            font-size: 11px;
            color: #777;
            text-transform: uppercase;
        }
        .event-overview-kpi-sep {
            color: #ccc;
            font-size: 18px;
            line-height: 1;
        }
        .dashboard-alerts-compact {
            margin-top: 12px;
        }
        .dashboard-alert-item {
            padding: 8px 12px;
            margin-bottom: 8px;
        }
        .event-overview-link {
            margin-top: 8px;
        }
        @media (min-width: 992px) {
            .event-overview-link {
                margin-top: 0;
            }
            .event-overview-kpis {
                border-left: 1px solid #eee;
                border-right: 1px solid #eee;
                padding-left: 15px;
                padding-right: 15px;
            }
        }
        @media (max-width: 991px) {
            .event-overview-meta,
            .event-overview-kpis,
            .event-overview-link {
                margin-bottom: 10px;
            }
            .event-overview-link {
                text-align: left !important;
            }
        }
        a.small-box-link {
            color: inherit;
            display: block;
        }
        a.small-box-link:hover {
            color: inherit;
            text-decoration: none;
        }
        a.small-box-link:hover .small-box {
            opacity: 0.92;
        }
	</style>
@endsection

@php


@endphp

@section("content")
<div class="alert alert-info">
    <a href="{{ url('/evento/dashboard/' . $evento->id) }}">Abrir dashboard no layout novo (v2)</a>
</div>
<!-- Main row -->
<ul class="nav nav-pills">
@if($evento->parent_event)
  <li role="presentation"><a href="/evento/dashboard/{{$evento->parent_event->id}}?tab=evento_filho">Voltar a Dashboard do Evento Pai</a></li>
@else
  <li role="presentation"><a href="/grupoevento/dashboard/{{$evento->grupo_evento->id}}">Voltar a Dashboard de Grupo de Evento</a></li>
@endif
</ul>

@include('evento._partials.overview_strip')

<div class="row">
  <!-- Left col -->
	<div>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a id="tab_funcoes" href="#funcoes" aria-controls="funcoes" role="tab" data-toggle="tab">Funções</a></li>
			<li role="presentation"><a id="tab_resume" href="#resume" aria-controls="resume" role="tab" data-toggle="tab">Resumo</a></li>
			<li role="presentation"><a id="tab_editar_evento" href="#editar_evento" aria-controls="editar_evento" role="tab" data-toggle="tab">Editar Evento</a></li>
			<li role="presentation"><a id="tab_pagina" href="#pagina" aria-controls="pagina" role="tab" data-toggle="tab">Página</a></li>
			<li role="presentation"><a id="tab_timeline" href="#timeline" aria-controls="timeline" role="tab" data-toggle="tab">Timeline</a></li>
			<li role="presentation"><a id="tab_criterio_desempate" href="#criterio_desempate" aria-controls="criterio_desempate" role="tab" data-toggle="tab">Critério de Desempate</a></li>
			@if(
				\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
				\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
				\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
			)
			<li role="presentation"><a id="tab_premiacao_equipe" href="#premiacao_equipe" aria-controls="premiacao_equipe" role="tab" data-toggle="tab">Premiação por Equipes</a></li>
			@endif
			<li role="presentation"><a id="tab_categoria" href="#categoria" aria-controls="categoria" role="tab" data-toggle="tab">Categoria: Cadastro</a></li>
			<li role="presentation"><a id="tab_categorias_relacionadas" href="#categorias_relacionadas" aria-controls="categorias_relacionadas" role="tab" data-toggle="tab">Categorias Relacionadas</a></li>
			@if($evento->event_children()->count() > 0) <li role="presentation"><a id="tab_evento_filho" href="#evento_filho" aria-controls="torneio" role="tab" data-toggle="tab">Eventos Filhos</a></li> @endif
			<li role="presentation"><a id="tab_torneio" href="#torneio" aria-controls="torneio" role="tab" data-toggle="tab">Torneios</a></li>
			<li role="presentation"><a id="tab_campo_personalizado" href="#campo_personalizado" aria-controls="campo_personalizado" role="tab" data-toggle="tab">Campos Personalizados Adicionais</a></li>
            <li role="presentation"><a id="tab_email_template" href="#email_template" aria-controls="email_template" role="tab" data-toggle="tab">Templates de E-mail</a></li>
			@if(
                \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
				\Illuminate\Support\Facades\Auth::user()->hasPermissionEventsByPerfil([14,15,16])
            ) <li role="presentation"><a id="tab_classificator" href="#classificator" aria-controls="classificator" role="tab" data-toggle="tab">XadrezSuíço Classificador</a></li> @endif
        </ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="funcoes">
                @include("evento._tabs.funcoes")
			</div>
			<div role="tabpanel" class="tab-pane" id="resume">
                @include("evento._tabs.resume")
			</div>
			<div role="tabpanel" class="tab-pane" id="editar_evento">
                @include("evento._tabs.editar_evento")
			</div>
			<div role="tabpanel" class="tab-pane" id="pagina">
                @include("evento._tabs.pagina")
			</div>
			<div role="tabpanel" class="tab-pane" id="timeline">
                @include("evento._tabs.timeline")
			</div>
			<div role="tabpanel" class="tab-pane" id="criterio_desempate">
                @include("evento._tabs.criterio_desempate")
			</div>
			<div role="tabpanel" class="tab-pane" id="premiacao_equipe">
                @include("evento._tabs.premiacao_equipe")
			</div>
			<div role="tabpanel" class="tab-pane" id="categoria">
                @include("evento._tabs.categoria")
			</div>
			<div role="tabpanel" class="tab-pane" id="categorias_relacionadas">
                @include("evento._tabs.categorias_relacionadas")
			</div>
			<div role="tabpanel" class="tab-pane" id="torneio">
                @include("evento._tabs.torneio")
			</div>
			<div role="tabpanel" class="tab-pane" id="evento_filho">
                @include("evento._tabs.evento_filho")
			</div>
			<div role="tabpanel" class="tab-pane" id="campo_personalizado">
                @include("evento._tabs.campo_personalizado")
			</div>
            <div role="tabpanel" class="tab-pane" id="email_template">
                @include("evento._tabs.email_template")
			</div>
            <div role="tabpanel" class="tab-pane" id="classificator">
                @include("evento._tabs.classificator")
			</div>
		</div>

	</div>
  <!-- /.Left col -->
</div>
<!-- /.row (main row) -->

@endsection

@section("js")
<!-- Morris.js charts -->
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript" src="{{url("/vendor/bower/ckeditor/ckeditor.js")}}"></script>
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
    	CKEDITOR.replace('texto');
    	CKEDITOR.replace('orientacao_pos_inscricao');
		$("#torneio_template_id").select2();
		$("#tipo_modalidade").select2();
		$("#layout_version").select2();
		$("#exportacao_sm_modelo").select2();
		$("#categoria_id").select2();
		$("#category_xadrezsuicopag_uuid").select2();
		$("#criterio_desempate_id").select2();
		$("#criterio_desempate_geral_id").select2();
		$("#tipo_torneio_id").select2();
		$("#torneio_softwares_id").select2();
		$("#tipo_ratings_id").select2();
		$("#tipo_modalidade").val([{{$evento->tipo_modalidade}}]).change();
		$("#exportacao_sm_modelo").val([{{$evento->exportacao_sm_modelo}}]).change();
		$("#layout_version").val([{{$evento->layout_version}}]).change();
		@if($evento->tipo_rating)
			$("#tipo_ratings_id").val([{{$evento->tipo_rating->tipo_ratings_id}}]).change();
		@endif

		$(".pais_select2").select2({
            ajax: {
                url: '{{url("/api/v1/location/country/select2")}}',
                delay: 250,
                processResults: function (data) {
                    return {
                        results: data.results
                    };
                }
            }
        });

        $("#estados_id").select2();
        $("#cidade_id").select2();
        @if($evento->cidade)
			@if($evento->cidade->estado)
				@if($evento->cidade->estado->pais)
					Loading.enable(loading_default_animation, 10000);

                    var newOptionPais = new Option("{{$evento->cidade->estado->pais->nome}} ({{$evento->cidade->estado->pais->codigo_iso}})", "{{$evento->cidade->estado->pais->id}}", false, false);
                    $('#pais_id').append(newOptionPais).trigger('change');

					$("#pais_id").val({{$evento->cidade->estado->pais->id}}).change();
					buscaEstados(false,function(){
						setTimeout(function(){
							$("#estados_id").val({{$evento->cidade->estado->id}}).change();
							setTimeout(function(){
								buscaCidades(function(){
									$("#cidade_id").val({{$evento->cidade_id}}).change();
									Loading.destroy();
								});
							},200);
						},200);
					});
				@endif
			@endif
		@endif

        @if($evento->classificador)
		    $("#evento_classificador_id").select2();
			$("#evento_classificador_id").val([{{$evento->classificador->id}}]).change();
        @endif
        @if($evento->grupo_evento_classificador)
		    $("#grupo_evento_classificador_id").select2();
			$("#grupo_evento_classificador_id").val([{{$evento->grupo_evento_classificador->id}}]).change();
        @endif
		$("#tabela_torneio").DataTable({
			responsive: true,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
		});
		$("#tabela_categoria").DataTable({
				responsive: true,
		});
		$("#tabela_criterio_desempate").DataTable({
				responsive: true,
				"ordering": false,
		});
		$("#tabela_criterio_desempate_geral").DataTable({
				responsive: true,
				"ordering": false,
		});
		$("#tabela_pontuacao").DataTable({
				responsive: true,
				"ordering": false,
		});
		setTimeout(function(){
			$(".select2").css("width","100%");
		},"1000");
		@if($tab)
			$("#tab_{{$tab}}").tab("show");
		@endif
		$("#evento_data_inicio").mask("00/00/0000");
		$("#evento_data_fim").mask("00/00/0000");
		$("#date_start_registration").mask("00/00/0000 00:00");
		$("#evento_data_limite_inscricoes_abertas").mask("00/00/0000 00:00");
		$("#confirmacao_publica_inicio").mask("00/00/0000 00:00");
		$("#confirmacao_publica_final").mask("00/00/0000 00:00");
		$(".timeline-datetime").mask("00/00/0000 00:00");


        function eventToggleOk(response) {
            return response.ok === true || response.ok === 1 || response.ok === '1';
        }

        function applyEventToggleSwitch($input, response, previousChecked) {
            if (eventToggleOk(response)) {
                if (typeof response.enabled !== 'undefined') {
                    $input.prop('checked', !!response.enabled);
                }
                return true;
            }
            $input.prop('checked', previousChecked);
            return false;
        }

        function bindEventToggleSwitch(selector, url, options) {
            options = options || {};
            $(selector).change(function() {
                var $input = $(this);
                var previousChecked = ! $input.prop('checked');
                var isChecked = $input.prop('checked');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    data: { enabled: isChecked ? 1 : 0 },
                    success: function(response) {
                        if (applyEventToggleSwitch($input, response, previousChecked)) {
                            if (options.onEnabled && typeof response.enabled !== 'undefined') {
                                options.onEnabled(!!response.enabled);
                            }
                            Swal.fire({
                                icon: 'success',
                                title: response.message || options.successMessage || 'Atualizado com sucesso!',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: response.message || options.errorMessage || 'Erro ao atualizar',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    },
                    error: function(xhr) {
                        $input.prop('checked', previousChecked);
                        var msg = options.errorMessage || 'Erro ao comunicar com o servidor';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: msg,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                });
            });
        }

        bindEventToggleSwitch('#toggle_inscricoes', "{{url('/evento/'.$evento->id.'/toggleinscricoes')}}", {
            successMessage: 'Status das inscrições atualizado com sucesso!'
        });

        bindEventToggleSwitch('#toggle_edicao_inscricao', "{{url('/evento/'.$evento->id.'/toggleedicaoinscricao')}}", {
            successMessage: 'Status da edição de inscrição atualizado com sucesso!'
        });

        bindEventToggleSwitch('#toggle_classificavel', "{{url('/evento/'.$evento->id.'/toggleclassificavel')}}");

        bindEventToggleSwitch('#toggle_resultados_automaticos', "{{url('/evento/'.$evento->id.'/togglemanual')}}");

        bindEventToggleSwitch('#toggle_rating', "{{url('/evento/'.$evento->id.'/togglerating')}}", {
            successMessage: 'Status do cálculo de rating atualizado com sucesso!',
            onEnabled: function(enabled) {
                if (enabled) {
                    $('#toggle_rating_status_container').show();
                } else {
                    $('#toggle_rating_status_container').hide();
                }
            }
        });

        bindEventToggleSwitch('#toggle_resultados', "{{url('/evento/'.$evento->id.'/toggleresultados')}}");
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
</script>
@endsection

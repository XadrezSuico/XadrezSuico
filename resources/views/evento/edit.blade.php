@extends('adminlte::page')

@section("title", "Dashboard de Evento")

@section('content_header')
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
	</style>
@endsection

@php


@endphp

@section("content")
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/grupoevento/dashboard/{{$evento->grupo_evento->id}}">Voltar a Dashboard de Grupo de Evento</a></li>
</ul>
<div class="row">
  <!-- Left col -->
	<div>
		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a id="tab_funcoes" href="#funcoes" aria-controls="funcoes" role="tab" data-toggle="tab">Funções</a></li>
			<li role="presentation"><a id="tab_resume" href="#resume" aria-controls="resume" role="tab" data-toggle="tab">Resumo</a></li>
			<li role="presentation"><a id="tab_editar_evento" href="#editar_evento" aria-controls="editar_evento" role="tab" data-toggle="tab">Editar Evento</a></li>
			<li role="presentation"><a id="tab_pagina" href="#pagina" aria-controls="pagina" role="tab" data-toggle="tab">Página</a></li>
			<li role="presentation"><a id="tab_criterio_desempate" href="#criterio_desempate" aria-controls="criterio_desempate" role="tab" data-toggle="tab">Critério de Desempate</a></li>
			<li role="presentation"><a id="tab_categoria" href="#categoria" aria-controls="categoria" role="tab" data-toggle="tab">Categoria: Cadastro</a></li>
			<li role="presentation"><a id="tab_categorias_relacionadas" href="#categorias_relacionadas" aria-controls="categorias_relacionadas" role="tab" data-toggle="tab">Categorias Relacionadas</a></li>
			<li role="presentation"><a id="tab_torneio" href="#torneio" aria-controls="torneio" role="tab" data-toggle="tab">Torneios</a></li>
			<li role="presentation"><a id="tab_campo_personalizado" href="#campo_personalizado" aria-controls="campo_personalizado" role="tab" data-toggle="tab">Campos Personalizados Adicionais</a></li>
            <li role="presentation"><a id="tab_email_template" href="#email_template" aria-controls="email_template" role="tab" data-toggle="tab">Templates de E-mail</a></li>
			<?php if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()){ ?><li role="presentation"><a id="tab_classificator" href="#classificator" aria-controls="classificator" role="tab" data-toggle="tab">XadrezSuíço Classificador</a></li><?php } ?>
        </ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="funcoes">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Funções</h3>
						</div>

						<div class="box-body">
							<h4>Inscrições:</h4>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
                                @if($evento->layout_version == 2)
                                    <a href="{{url("/inscricao/".$evento->id)}}" class="btn btn-bg-green btn-app">
                                        <i class="fa fa-plus"></i>
                                        Nova ou Confirmar Inscrição (Privada)
                                    </a>
                                    <a href="{{$evento->getEventPublicLink()}}" class="btn btn-success btn-app">
                                        <i class="fa fa-link"></i>
                                        Link para Divulgação
                                    </a>
                                @else
                                    @if($evento->e_inscricao_apenas_com_link)
                                        <div class="alert alert-warning alert-dismissible" role="alert">
                                            <strong>Aviso!</strong><br/>
                                            A inscrição para este evento será efetuada apenas pelo link compartilhado (que é possível acessar logo abaixo).
                                        </div>
                                        <a href="{{url("/inscricao/".$evento->id."?token=".$evento->token)}}" class="btn btn-bg-green btn-app">
                                            <i class="fa fa-plus"></i>
                                            Nova ou Confirmar Inscrição
                                        </a>
                                    @else
                                        <a href="{{url("/inscricao/".$evento->id)}}" class="btn btn-bg-green btn-app">
                                            <i class="fa fa-plus"></i>
                                            Nova ou Confirmar Inscrição
                                        </a>
                                    @endif
                                    @if($evento->e_inscricao_apenas_com_link)
                                        <a href="{{url("/inscricao/".$evento->id."?token=".$evento->token)}}" class="btn btn-success btn-app">
                                            <i class="fa fa-link"></i>
                                            Link para Divulgação
                                        </a>
                                    @else
                                        <a href="{{url("/inscricao/".$evento->id)}}" class="btn btn-success btn-app">
                                            <i class="fa fa-link"></i>
                                            Link para Divulgação
                                        </a>
                                    @endif
                                @endif
							@endif
                            <br/>
                            @if($evento->e_permite_confirmacao_publica)
                                @if($evento->e_inscricao_apenas_com_link)
                                    <a href="{{url("/inscricao/".$evento->id."/confirmacao?token=".$evento->token)}}" class="btn btn-success btn-app">
                                        <i class="fa fa-check"></i>
                                        Link para Confirmação Pública
                                    </a>
                                @else
                                    <a href="{{url("/inscricao/".$evento->id)}}/confirmacao" class="btn btn-success btn-app">
                                        <i class="fa fa-check"></i>
                                        Link para Confirmação Pública
                                    </a>
                                @endif
                            @endif
                            <br/>

                            <a href="{{url("/evento/".$evento->id."/toggleinscricoes")}}" class="btn btn-warning btn-app">
                                @if(!$evento->is_inscricoes_bloqueadas)
                                    <i class="fa fa-lock"></i>
                                    Bloquear (Status Atual: Liberado)
                                @else
                                    <i class="fa fa-unlock"></i>
                                    Liberar  (Status Atual: Bloqueado)
                                @endif
                                Inscricoes
                            </a>

                            <a href="{{url("/evento/".$evento->id."/toggleedicaoinscricao")}}" class="btn btn-warning btn-app">
                                @if($evento->permite_edicao_inscricao)
                                    <i class="fa fa-lock"></i>
                                    Restringir (Status Atual: Permitido)
                                @else
                                    <i class="fa fa-unlock"></i>
                                    Permitir (Status Atual: Restringido)
                                @endif
                                Edição de Inscrição
                            </a>
                            @if($evento->isPaid())
                                <br/>
                                <a href="{{url("/evento/".$evento->id."/toggleregistrationpaidconfirmed")}}" class="btn btn-warning btn-app">
                                    @if($evento->hasConfig("flag__registration_paid_confirmed"))
                                        @if($evento->getConfig("flag__registration_paid_confirmed",true))
                                            <i class="fa fa-check"></i>
                                            Não Confirmar (Status Atual: Confirmado)
                                        @else
                                            <i class="fa fa-times"></i>
                                            Confirmar (Status Atual: Não Confirmar)
                                        @endif
                                    @else
                                        <i class="fa fa-times"></i>
                                        Confirmar (Status Atual: Não Confirmar)
                                    @endif
                                    Inscrição Paga Automaticamente
                                </a>
                            @endif
							<hr/>
                            <h4>XadrezSuíço Emparceirador:</h4>
							<a href="{{url("/evento/".$evento->id."/exports/xadrezsuicoemparceirador")}}" class="btn btn-app">
								<i class="fa fa-download"></i>
								Baixar Arquivo do XadrezSuíço Emparceirador (Todas as inscrições - Sem dados)
							</a>
							<a href="{{url("/evento/".$evento->id."/exports/xadrezsuicoemparceirador/data")}}" class="btn btn-app">
								<i class="fa fa-download"></i>
								Baixar Arquivo do XadrezSuíço Emparceirador (Inscrições Confirmadas - Com dados)
							</a>
                            <hr/>
                            <h4>Divulgação de Emparceiramentos:</h4>
							<a href="{{url("/evento/acompanhar/".$evento->id)}}" class="btn btn-app">
								<i class="fa fa-eye"></i>
								Link para Acompanhar os Emparceiramentos
							</a>
							<hr/>
							<h4>Classificação:</h4>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
								<a href="/evento/classificar/{{$evento->id}}" class="btn btn-success btn-app">
									<i class="fa fa-sort"></i>
									Classificar Evento
								</a>
								<a href="{{url("/evento/".$evento->id."/toggleresultados")}}" class="btn btn-warning btn-app">
									@if($evento->mostrar_resultados)
										<i class="fa fa-lock"></i>
										Restringir (Status Atual: Liberado)
									@else
										<i class="fa fa-unlock"></i>
										Liberar (Status Atual: Restringido)
									@endif
									Classificação Pública
								</a>
								@if($evento->mostrar_resultados)
									<a href="{{url("/evento/classificacao/".$evento->id)}}" class="btn btn-app">
										<i class="fa fa-eye"></i>
										Visualizar Classificação (Pública)
									</a>
								@endif
							@endif
							<a href="{{url("/evento/classificacao/".$evento->id)}}/interno" class="btn btn-app">
								<i class="fa fa-eye"></i>
								Visualizar Classificação (Interna)
							</a>
							<hr/>
							<h4>Inscritos:</h4>
							<a href="{{url("/evento/".$evento->id)}}/inscricoes/list" class="btn btn-app">
								<i class="fa fa-list"></i>
								Visualizar Lista de Inscritos (Completa)
							</a>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
								<hr/>
                                @if(false)
                                    <h4>Lista de Rating:</h4>
                                    <a href="{{url("/evento/".$evento->id)}}/enxadristas/sm" class="btn btn-app" target="_blank">
                                        <i class="fa fa-download"></i>
                                        Baixar para Uso neste Evento (Swiss-Manager)
                                    </a>
                                @endif

                                @if($evento->tipo_rating)
                                    <hr/>
                                    <h4>Rating:</h4>
                                    <a href="{{url("/evento/".$evento->id)}}/togglerating" class="btn btn-app">
                                        @if($evento->is_rating_calculate_enabled)
                                            <i class="fa fa-lock"></i>
                                            Não Permitir o Cálculo do Rating Interno (Status Atual: Permitido)
                                        @else
                                            <i class="fa fa-unlock"></i>
                                            Permitir o Cálculo do Rating Interno (Status Atual: Não Permitido)
                                        @endif
                                    </a>
                                    @if($evento->is_rating_calculate_enabled)
                                        @if ($evento->consegueCalcularRating() == 0)
                                            <button type="button" class="btn btn-app disabled" disabled>
                                                <i class="fa fa-calculator"></i>
                                                Calcular Rating (Não foi importado o emparceiramento)
                                            </button>
                                        @else
                                            <a href="{{url("/evento/".$evento->id)}}/rating/calculate" class="btn btn-app">
                                                <i class="fa fa-calculator"></i>
                                                Calcular Rating
                                            </a>
                                        @endif
                                    @endif
                                @endif

							@endif

							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
								<hr/>
								<h4>Demais Funções:</h4>
								<a href="{{url("/evento/".$evento->id."/toggleclassificavel")}}" class="btn btn-app">
									@if($evento->classificavel)
										<i class="fa fa-lock"></i>
										Não Permitir (Status Atual: Permitido)
									@else
										<i class="fa fa-unlock"></i>
										Permitir (Status Atual: Não Permitido)
									@endif
									Classificação Geral deste Evento
								</a>
								<a href="{{url("/evento/".$evento->id."/togglemanual")}}" class="btn btn-app">
									@if($evento->e_resultados_manuais)
										<i class="fa fa-lock"></i>
									@else
										<i class="fa fa-lock"></i>
									@endif
									Resultados @if($evento->e_resultados_manuais) Automáticos  (Status Atual: Manuais) @else Manuais  (Status Atual: Automáticos) @endif
								</a>
                                @if($evento->event_team_awards()->count() > 0)

                                    <a href="{{url("/evento/premiacao_time/classificar/".$evento->id)}}" class="btn btn-app">
                                        <i class="fa fa-sort"></i>
                                        Classificar Times no Evento
                                    </a>

                                    <a href="{{url("/evento/".$evento->id."/team_awards/standings")}}" class="btn btn-app">
                                        <i class="fa fa-list"></i>
                                        Listar Premiações de Times
                                    </a>
                                @endif
							@endif<br/><br/>
							@if(
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
								\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
							)
                                @if($evento->evento_classificador_id > 0)
                                    <h4>Funções de Evento que possui Classificador:</h4>
                                    <a href="{{url("/evento/".$evento->id."/gerenciamento/torneio_3/import")}}" class="btn btn-app">
                                        <i class="fa fa-upload"></i>
                                        Importar Inscrições do Evento Classificador
                                    </a>
                                    <a href="{{url("/evento/".$evento->id."/gerenciamento/torneio_3/removeAll")}}" class="btn btn-app">
                                        <i class="fa fa-times"></i>
                                        Remove todas as Inscrições do Evento
                                    </a>
                                @else
                                    @if($evento->grupo_evento_classificador_id > 0)
                                        <h4>Funções de Evento que possui Grupo de Evento Classificador:</h4>
                                        <a href="{{url("/evento/".$evento->id."/gerenciamento/import")}}" class="btn btn-app">
                                            <i class="fa fa-upload"></i>
                                            Importar Inscrições do Grupo de Evento Classificador
                                        </a>
                                        <a href="{{url("/evento/".$evento->id."/gerenciamento/removeAll")}}" class="btn btn-app">
                                            <i class="fa fa-times"></i>
                                            Remove todas as Inscrições do Evento
                                        </a>

                                    @endif
                                @endif
                            @endif
                            <br/><br/>
                            <hr/>
                            <h4>Relatórios:</h4>
                            <a href="{{url("/evento/".$evento->id."/relatorios/premiados")}}" class="btn btn-app">
                                <i class="fa fa-file"></i>
                                Enxadristas Premiados neste Evento
                            </a>
						</div>
						<!-- /.box-body -->
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="resume">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Resumo</h3>
						</div>
						<!-- form start -->

                        <div class="box-body">

                            <div class="row">
                                <!-- Total de Inscritos -->
                                <div class="col-sm-12 col-md-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                        <h3>{{$evento->quantosInscritos()}}</h3>

                                        <p>Total de Inscritos</p>
                                        </div>
                                        <div class="icon">
                                        <i class="fa fa-users"></i>
                                        </div>
                                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                    </div>
                                </div>
                                <!-- Total de Confirmados -->
                                <div class="col-sm-12 col-md-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                        <h3>{{$evento->quantosInscritosConfirmados()}}</h3>

                                        <p>Total de Confirmados</p>
                                        </div>
                                        <div class="icon">
                                        <i class="fa fa-check"></i>
                                        </div>
                                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                    </div>
                                </div>
                                <!-- Total de Presentes -->
                                <div class="col-sm-12 col-md-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                        <h3>{{$evento->quantosInscritosPresentes()}}</h3>

                                        <p>Total de Presentes</p>
                                        </div>
                                        <div class="icon">
                                        <i class="fa fa-check-circle"></i>
                                        </div>
                                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                    </div>
                                </div>
                                <!-- Total de Resultados (Importados) -->
                                <div class="col-sm-12 col-md-6">
                                    <!-- small box -->
                                    <div class="small-box bg-aqua">
                                        <div class="inner">
                                        <h3>{{$evento->quantosInscritosComResultados()}}</h3>

                                        <p>Total de Resultados</p>
                                        </div>
                                        <div class="icon">
                                        <i class="fa fa-check-circle"></i>
                                        </div>
                                        <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                @if($evento->isPaid())
                                    <!-- Total de Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyPaid()}}</h3>

                                            <p>Total de Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Gratuidades -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyFree()}}</h3>

                                            <p>Total de Gratuidades (Categorias Gratuitas)</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Não Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyNotPaid()}}</h3>

                                            <p>Total de Não Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>


                                    <!-- Total de Confirmados Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyConfirmedPaid()}}</h3>

                                            <p>Total de Confirmados Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Confirmados Gratuidades -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyConfirmedFree()}}</h3>

                                            <p>Total de Confirmados Gratuidades</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Confirmados Não Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyConfirmedNotPaid()}}</h3>

                                            <p>Total de Confirmados Não Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>


                                    <!-- Total de Presentes Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyPresentPaid()}}</h3>

                                            <p>Total de Presentes Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Presentes Gratuidades -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyPresentFree()}}</h3>

                                            <p>Total de Presentes Gratuidades</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Presentes Não Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyPresentNotPaid()}}</h3>

                                            <p>Total de Presentes Não Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>

                                    <!-- Total de Resultados Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyWithResultsPaid()}}</h3>

                                            <p>Total de Resultados Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Resultados Gratuidades -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyWithResultsFree()}}</h3>

                                            <p>Total de Resultados Gratuidades</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Total de Resultados Não Pagos -->
                                    <div class="col-sm-6 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$evento->howManyWithResultsNotPaid()}}</h3>

                                            <p>Total de Resultados Não Pagos</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-money"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                @endif

                                @php($bigger_tournament = $evento->getTournamentWithMoreRegistrations())
                                    <!-- Maior Torneio -->
                                    <div class="col-sm-12 col-md-8">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$bigger_tournament["status"] ? $bigger_tournament["tournament"]->name : $bigger_tournament["tournament"]}}</h3>

                                            <p>Maior Torneio</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-award"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>
                                    <!-- Maior Torneio Total -->
                                    <div class="col-sm-12 col-md-4">
                                        <!-- small box -->
                                        <div class="small-box bg-aqua">
                                            <div class="inner">
                                            <h3>{{$bigger_tournament["total"]}}</h3>

                                            <p>Total de Inscritos no Maior Torneio</p>
                                            </div>
                                            <div class="icon">
                                            <i class="fa fa-award"></i>
                                            </div>
                                            <!--<a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>-->
                                        </div>
                                    </div>


                            </div>
                        </div>
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="editar_evento">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Editar Evento</h3>
						</div>
						<!-- form start -->

					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<form method="post">
					@endif
							<div class="box-body">
								<div class="form-group">
									<label for="evento_name">Nome *</label>
									<input name="name" id="evento_name" class="form-control" type="text" value="{{$evento->name}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_inicio">Data de Início *</label>
									<input name="data_inicio" id="evento_data_inicio" class="form-control" type="text" value="{{$evento->getDataInicio()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_fim">Data de Fim *</label>
									<input name="data_fim" id="evento_data_fim" class="form-control" type="text" value="{{$evento->getDataFim()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="tipo_modalidade">Tipo de Modalidade *</label>
									<select name="tipo_modalidade" id="tipo_modalidade" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de modalidade ---</option>
										<option value="0">Convencional</option>
										<option value="1">Rápido</option>
										<option value="2">Relâmpago</option>
									</select>
								</div>
								<div class="form-group">
									<label for="layout_version">Tipo de Layout de Página *</label>
									<select name="layout_version" id="layout_version" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de layout de página ---</option>
										<option value="1">Versão 1 - Padrão</option>
										<option value="2">Versão 2 - Página de Evento</option>
									</select>
								</div>
								<div class="form-group">
									<label for="exportacao_sm_modelo">Tipo de Exportação - Swiss Manager *</label>
									<select name="exportacao_sm_modelo" id="exportacao_sm_modelo" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="0">Padrão XadrezSuíço</option>
										<option value="1">FIDE</option>
										<option value="7">FIDE - Exporta somente clube</option>
										<option value="2">LBX</option>
										<option value="3">Padrão XadrezSuíço (Nome no Sobrenome, e Sobrenome no Nome)</option>
										<option value="4">Padrão XadrezSuíço Chess.com (Nome no Sobrenome, Sobrenome no Nome e no Nome também informa o Usuário do Chess.com)</option>
										<option value="5">Padrão XadrezSuíço sem Cidade (Nome no Sobrenome, Sobrenome no Nome sem Cidade)</option>
										<option value="6">Padrão XadrezSuíço sem Cidade (Nome no Sobrenome, Sobrenome no Nome sem Cidade) em Torneio por Equipe</option>
									</select>
								</div>
                                <div class="form-group">
                                    <label for="pais_id">País *</label>
                                    <select id="pais_id" name="pais_id" class="form-control pais_select2" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
                                        <option value="">--- Selecione um país ---</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="estados_id">Estado *</label>
                                    <select id="estados_id" name="estados_id" class="form-control this_is_select2" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
                                        <option value="">--- Selecione um país antes ---</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="cidade_id">Cidade *</label>
                                    <select id="cidade_id" name="cidade_id" class="form-control this_is_select2" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
                                        <option value="">--- Selecione um estado antes ---</option>
                                    </select>
                                </div>
								<div class="form-group">
									<label for="evento_local">Local *</label>
									<input name="local" id="evento_local" class="form-control" type="text" value="{{$evento->local}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_link">Link</label>
									<input name="link" id="evento_link" class="form-control" type="text" value="{{$evento->link}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_limite_inscricoes_abertas">Data e Hora de Início das Inscrições</label>
									<input name="date_start_registration" id="date_start_registration" class="form-control" type="text" value="{{$evento->getDataInicioInscricoesOnline()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="evento_data_limite_inscricoes_abertas">Data e Hora Limite para Inscrições</label>
									<input name="data_limite_inscricoes_abertas" id="evento_data_limite_inscricoes_abertas" class="form-control" type="text" value="{{$evento->getDataFimInscricoesOnline()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="maximo_inscricoes_evento">Número Máximo de Inscrições Permitidas no Evento</label>
									<input name="maximo_inscricoes_evento" id="maximo_inscricoes_evento" class="form-control" type="text" value="{{$evento->maximo_inscricoes_evento}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
								</div>
								<div class="form-group">
									<label for="orientacao_pos_inscricao">Orientações Pós-Inscrição</label>
									<textarea class="form-control" id="orientacao_pos_inscricao" name="orientacao_pos_inscricao" placeholder="Texto com as Orientações Pós-Inscrição" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >{{$evento->orientacao_pos_inscricao}}</textarea>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="e_permite_visualizar_lista_inscritos_publica" name="e_permite_visualizar_lista_inscritos_publica" @if($evento->e_permite_visualizar_lista_inscritos_publica) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Permite a visualização da lista de inscrições de forma pública?</label>
								</div>
								<div class="form-group">
									<label><input type="checkbox" id="e_inscricao_apenas_com_link" name="e_inscricao_apenas_com_link" @if($evento->e_inscricao_apenas_com_link) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > As inscrições deste evento deverão ser feitas apenas pelo link divulgado (Inscrição Privada)</label>
								</div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="usa_fide" name="usa_fide" @if($evento->usa_fide) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Utiliza Rating FIDE?</label>
                                </div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="calcula_fide" name="calcula_fide" @if($evento->calcula_fide) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Calcula Rating FIDE?</label>
                                </div>
                                <div class="form-group">
                                    <label @if($evento->usa_lbx) title="Rating FIDE não disponível para este evento. Motivo: Usa Rating LBX, e por isto não permite o uso de rating FIDE." @endif><input type="checkbox" id="fide_required" name="fide_required" @if($evento->fide_required) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Não Calcula Rating FIDE mas Obriga ID FIDE?</label>
                                </div>
                                @if($evento->usa_fide || $evento->calcula_fide)
                                    <div class="form-group">
                                        <label><input type="checkbox" id="fide_sequence" name="fide_sequence" @if($evento->getConfig("fide_sequence",true)) checked="checked" @endif @if((!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) || $evento->usa_lbx) disabled="disabled" @endif> Utiliza Sequência do Padrão da FIDE?</label>
                                    </div>
                                @endif
                                @if(!$evento->tipo_rating)
                                    <div class="form-group">
                                        <label><input type="checkbox" id="usa_cbx" name="usa_cbx" @if($evento->usa_cbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Utiliza Rating CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="calcula_cbx" name="calcula_cbx" @if($evento->calcula_cbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Calcula Rating CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="cbx_required" name="cbx_required" @if($evento->cbx_required) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Não Calcula Rating CBX mas Obriga ID CBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="usa_lbx" name="usa_lbx" @if($evento->usa_lbx) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Utiliza Rating LBX?</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess" name="is_lichess" @if($evento->is_lichess) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Lichess.org</label>
                                    </div>
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess_integration" name="is_lichess_integration" @if($evento->is_lichess_integration) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Usa Integração com o Lichess.org</label>
                                    </div>
                                    <div class="form-group">
                                        <label for="lichess_team_id">Lichess.org: ID do Time/Equipe</label>
                                        <input name="lichess_team_id" id="lichess_team_id" class="form-control" type="text" value="{{$evento->lichess_team_id}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                        <small><strong>Importante!</strong> Aqui vai o ID do Time no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões. Segue exemplo de onde encontrar o ID do Time: https://lichess.org/team/<strong>circuito-sesc-de-xadrez-2021</strong></small>
                                        @if($evento->lichess_team_id) <br/><a href="https://lichess.org/team/{{$evento->lichess_team_id}}" target="_blank">Link do Time</a> @endif
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
                                        <div class="form-group">
                                            <label for="lichess_team_password">Lichess.org: Senha para a Entrada no Time/Equipe</label>
                                            <input name="lichess_team_password" id="lichess_team_password" class="form-control" type="text" value="{{$evento->lichess_team_password}}" />
                                            <small><strong>Importante!</strong> Aqui vai a Senha para a Entrada no Time no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões.</small>
                                        </div>
                                    @endif
                                    <div class="form-group">
                                        <label for="lichess_tournament_id">Lichess.org: ID do Torneio</label>
                                        <input name="lichess_tournament_id" id="lichess_tournament_id" class="form-control" type="text" value="{{$evento->lichess_tournament_id}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                        <small><strong>Importante!</strong> Aqui vai o ID do Torneio no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões. Segue exemplo de onde encontrar o ID do Torneio: https://lichess.org/swiss/<strong>ZDig8Z5Y</strong></small>
                                        @if($evento->lichess_tournament_id) <br/><a href="https://lichess.org/swiss/{{$evento->lichess_tournament_id}}" target="_blank">Link do Torneio</a> @endif
                                    </div>
                                    @if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
                                        <div class="form-group">
                                            <label for="lichess_tournament_password">Lichess.org: Senha para a Inscrição no Torneio</label>
                                            <input name="lichess_tournament_password" id="lichess_tournament_password" class="form-control" type="text" value="{{$evento->lichess_tournament_password}}" />
                                            <small><strong>Importante!</strong> Aqui vai a Senha para a Inscrição no Torneio no Lichess. Vale constar que para que um torneio tenha integração com o XadrezSuíço é necessário que seja efetuado por um time/equipe que XadrezSuíço tenha permissões.</small>
                                        </div>
                                    @endif
                                @else
                                    <div class="form-group">
                                        <label><input type="checkbox" id="is_lichess" name="is_lichess" @if($evento->is_lichess) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Lichess.org</label>
                                    </div>
                                @endif
                                <div class="form-group">
                                    <label><input type="checkbox" id="is_chess_com" name="is_chess_com" @if($evento->is_chess_com) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Necessita usuário da plataforma Chess.com</label>
                                </div>
								<hr/>
								<div class="form-group">
									<label for="tipo_ratings_id">Tipo de Rating</label>
									<select name="tipo_ratings_id" id="tipo_ratings_id" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif>
										<option value="">--- Você pode selecionar um tipo de rating ---</option>
										@foreach($tipos_rating as $tipo_rating)
											<option value="{{$tipo_rating->id}}">{{$tipo_rating->id}} - {{$tipo_rating->name}}</option>
										@endforeach
									</select>
								</div>

                                @if($evento->grupo_evento->classificador)
                                    <div class="form-group">
                                        <label for="evento_classificador_id">Evento Classificador ({{$evento->grupo_evento->classificador->name}})</label>
                                        <select name="evento_classificador_id" id="evento_classificador_id" class="form-control width-100">
                                            <option value="">--- Você pode selecionar um evento ---</option>
                                            @foreach($evento->grupo_evento->classificador->eventos->all() as $ec)
                                                <option value="{{$ec->id}}">{{$ec->id}} - {{$ec->name}}</option>
                                            @endforeach
                                        </select>
                                        <small><strong>IMPORTANTE!</strong> Essa configuração serve para caso tenha um grupo de evento que classifica para este grupo de evento. Aqui vai o Evento Classificador que classifica para este Evento.</small>
                                    </div>
                                @else
                                    @if($evento->grupo_evento_classificador)
                                        <div class="form-group">
                                            <label for="grupo_evento_classificador_id">Grupo de Evento Classificador</label>
                                            <select name="grupo_evento_classificador_id" id="grupo_evento_classificador_id" class="form-control width-100">
                                                <option value="">--- Você pode selecionar um grupo de evento ---</option>
                                                @foreach($evento->grupo_evento->all() as $gec)
                                                    <option value="{{$gec->id}}">{{$gec->id}} - {{$gec->name}}</option>
                                                @endforeach
                                            </select>
                                            <small><strong>IMPORTANTE!</strong> Essa configuração serve para caso tenha um grupo de evento que classifica para este grupo de evento.</small>
                                        </div>
                                    @endif
                                @endif
                                <hr/>
                                <div class="form-group">
                                    <label><input type="checkbox" id="e_permite_confirmacao_publica" name="e_permite_confirmacao_publica" @if($evento->e_permite_confirmacao_publica) checked="checked" @endif @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif > Permite Confirmação Pública</label>
                                </div>
								<div class="form-group">
									<label for="confirmacao_publica_inicio">Confirmações: Data e Hora Inicial</label>
									<input name="confirmacao_publica_inicio" id="confirmacao_publica_inicio" class="form-control" type="text" value="{{$evento->getConfirmacoesDataInicial()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                    <small><strong>IMPORTANTE!</strong> Os campos de Data e Hora Inicial e Data e Hora Final devem estar preenchidos para que a confirmação pública fique disponível.</small>
                                </div>
								<div class="form-group">
									<label for="confirmacao_publica_final">Confirmações: Data e Hora Final</label>
									<input name="confirmacao_publica_final" id="confirmacao_publica_final" class="form-control" type="text" value="{{$evento->getConfirmacoesDataFim()}}" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif />
                                    <small><strong>IMPORTANTE!</strong> Os campos de Data e Hora Inicial e Data e Hora Final devem estar preenchidos para que a confirmação pública fique disponível.</small>
								</div>

                                @if(
                                    env("XADREZSUICOPAG_URI",null) &&
                                    env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                    env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
						            \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11])
                                )
                                    <div class="form-group">
                                        <label for="xadrezsuicopag_uuid">XadrezSuíçoPAG: UUID do Evento na Plataforma</label>
                                        <input name="xadrezsuicopag_uuid" id="xadrezsuicopag_uuid" class="form-control" type="text" value="{{$evento->xadrezsuicopag_uuid}}" />
                                        <small><strong>IMPORTANTE!</strong> Lembre-se de colocar o UUID do Evento na Plataforma caso deseje ativar o pagamento para este evento.</small>
                                    </div>
                                @endif
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						</form>
					@endif
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="pagina">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Aviso!</strong><br/>
						Caso queira personalizar a página de inscrição do evento, será possível adicionar um texto e também uma imagem, não sendo itens obrigatórios.
					</div>
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Página</h3>
						</div>
						<!-- form start -->

					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<form method="post" action="{{url("/evento/".$evento->id."/pagina")}}" enctype="multipart/form-data">
					@endif
							<div class="box-body">
								<div class="form-group">
									<label for="imagem">Imagem</label>
									@if($evento->pagina)
										@if($evento->pagina->imagem)
											<br/><img src="data:image/png;base64, {!!$evento->pagina->imagem!!}" width="100%" style="max-width: 400px"/>
											@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
												<label><input type="checkbox" name="remover_imagem" /> Remover Imagem?</label>
											@endif
										@endif
									@endif
									@if(\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() || \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) || \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7]))
										<input type="file" id="imagem" name="imagem">
										@if ($errors->has('imagem'))
											<span class="help-block">
												<strong>{{ $errors->first('imagem') }}</strong>
											</span>
										@endif
									@endif
								</div>

								<div class="form-group">
									<label for="texto">Texto</label>
									<textarea class="form-control" id="texto" name="texto" placeholder="Texto sobre o Evento" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >@if($evento->pagina){{$evento->pagina->texto}}@endif</textarea>
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && !\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) && !\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])) disabled="disabled" @endif >Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						</form>
					@endif
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="criterio_desempate">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Lembre-se que, o <b>Grupo de Evento</b> poderá possuir critérios de desempate também.<br/>
						Caso você escolha um ou mais critérios nesta tela, os critérios de desempate do Grupo de Evento <strong>serão desconsiderados!</strong>
					</div>
				</section>
				<br/>
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
				)
					<section class="col-lg-6 connectedSortable">
						<div class="box box-primary">
							<div class="box-header">
								<h3 class="box-title">Relacionar Critério de Desempate</h3>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/criteriodesempate/add")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="criterio_desempate_id">Critério de Desempate</label>
										<select name="criterio_desempate_id" id="criterio_desempate_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($criterios_desempate as $criterio_desempate)
												<option value="{{$criterio_desempate->id}}">{{$criterio_desempate->id}} - {{$criterio_desempate->name}} @if($criterio_desempate->sm_code) [{{$criterio_desempate->sm_code}}] @endif</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="tipo_torneio_id">Tipo de Torneio</label>
										<select name="tipo_torneio_id" id="tipo_torneio_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($tipos_torneio as $tipo_torneio)
												<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->id}} - {{$tipo_torneio->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="softwares_id">Software</label>
										<select name="softwares_id" id="softwares_id" class="form-control width-100">
											<option value="">--- Selecione ---</option>
											@foreach($softwares as $software)
												<option value="{{$software->id}}">{{$software->id}} - {{$software->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="prioridade">Prioridade</label>
										<input name="prioridade" id="prioridade" class="form-control" type="number" />
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					</section>
				@endif
				<section class="col-lg-6 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Critérios de Desempate</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_criterio_desempate" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Tipo de Torneio</th>
											<th>Software</th>
											<th>Prior.</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->criterios()->orderBy("tipo_torneio_id","ASC")->orderBy("softwares_id","ASC")->orderBy("prioridade","ASC")->get() as $criterio_desempate)
											<tr>
												<td>{{$criterio_desempate->criterio->id}}</td>
												<td>{{$criterio_desempate->criterio->name}}</td>
												<td>{{$criterio_desempate->tipo_torneio->name}}</td>
												<td>{{$criterio_desempate->software->name}}</td>
												<td>{{$criterio_desempate->prioridade}}</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/criteriodesempate/remove/".$criterio_desempate->id)}}" role="button"><i class="fa fa-times"></i></a>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>

			<div role="tabpanel" class="tab-pane" id="categoria">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-danger alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Esta aba se destina apenas ao <strong>caso de necessitar de uma categoria específica para este evento</strong>. As categorias aqui cadastradas <strong>não serão replicadas</strong> a qualquer outro Evento ou então Grupo de Evento.<br/>
						Obs: O cadastro da categoria aqui <strong>não retira a necessidade de efetuar a relação</strong> da mesma na aba "Categorias Relacionadas".
					</div>
				</section>
				<br/>
				<section class="col-lg-12 connectedSortable">
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Nova Categoria</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/categorias/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="name">Nome</label>
										<input name="name" id="name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="name">Idade Mínima (Em anos)</label>
										<input name="idade_minima" id="idade_minima" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="name">Idade Máxima (Em anos)</label>
										<input name="idade_maxima" id="idade_maxima" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="name">Código Categoria (Padrão Swiss-Manager)</label>
										<input name="cat_code" id="cat_code" class="form-control" type="text" />
										<small>Exemplo: Para Sub-08, utilizar <strong>U08</strong>.</small>
									</div>
									<div class="form-group">
										<label for="name">Código Grupo (Deve ser único em cada evento, para evitar problemas de processamento do resultado)</label>
										<input name="code" id="code" class="form-control" type="text" />
										<small>Este código pode ser diferente de acordo com a sua forma de controle. Mas vale saber: é esta a informação que será utilizada para identificação da categoria quando ocorrer o processamento do resultado, e por isso é importante que esteja preenchida no Swiss-Manager e também que seja única para cada categoria.</small>
									</div>
									<div class="form-group">
										<label><input type="checkbox" id="nao_classificar" name="nao_classificar"> Não Classificar Categoria</label>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					@endif
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Categorias</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Classificar?</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->categorias_cadastradas->all() as $categoria)
											<tr>
												<td>{{$categoria->id}}</td>
												<td>{{$categoria->name}}</td>
												<td>@if(!$categoria->nao_classificar) Sim @else Não @endif</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/categorias/dashboard/".$categoria->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($categoria->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/categorias/delete/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="categorias_relacionadas">
				<section class="col-lg-12 connectedSortable">
				<br/>
				@if(
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
					\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
				)
					<div class="box box-primary collapsed-box">
						<div class="box-header">
							<h3 class="box-title">Nova Relação de Categoria</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
						</div>
						<!-- form start -->
						<form method="post" action="{{url("/evento/".$evento->id."/categoria/add")}}">
							<div class="box-body">
								<div class="form-group">
									<label for="categoria_id">Categoria</label>
									<select name="categoria_id" id="categoria_id" class="form-control width-100">
										<option value="">--- Selecione ---</option>
										@foreach($categorias as $categoria)
											<option value="{{$categoria->id}}">{{$categoria->id}} - {{$categoria->name}}</option>
										@endforeach
									</select>


                                    @if(
                                        env("XADREZSUICOPAG_URI",null) &&
                                        env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                        env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11]) &&
                                        $evento->xadrezsuicopag_uuid != ""
                                    )
                                        @if(
                                            $xadrezsuicopag_controller
                                        )
                                            @php($xadrezsuicopag_category_request = $xadrezsuicopag_controller->factory("categories")->list($evento->xadrezsuicopag_uuid))

                                            @if(
                                                $xadrezsuicopag_category_request->ok == 1
                                            )
                                                <label for="category_xadrezsuicopag_uuid">XadrezSuíçoPAG: Categoria</label>
                                                <select name="xadrezsuicopag_uuid" id="category_xadrezsuicopag_uuid" class="form-control width-100">
                                                    <option value="">--- Sem Categoria no XadrezSuíçoPAG ---</option>
                                                    @foreach($xadrezsuicopag_category_request->categories as $xadrezsuicopag_category)
                                                        <option value="{{$xadrezsuicopag_category->uuid}}">{{$xadrezsuicopag_category->uuid}} - {{$xadrezsuicopag_category->name}}</option>
                                                    @endforeach
                                                </select>
                                                <small><strong>IMPORTANTE!</strong> Apenas selecione uma categoria do XadrezSuíçoPAG caso esta necessite pagamento.</small>
                                            @endif
                                        @endif
                                    @endif
								</div>
							</div>
							<!-- /.box-body -->

							<div class="box-footer">
								<button type="submit" class="btn btn-success">Enviar</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
							</div>
						</form>
					</div>
				@endif
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Categorias Relacionadas</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Vínculo Principal</th>
                                            @if(
                                                env("XADREZSUICOPAG_URI",null) &&
                                                env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                                env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
                                                \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11]) &&
                                                $evento->xadrezsuicopag_uuid != ""
                                            )
                                                <th>Vínculo XadrezSuíçoPAG</th>
                                            @endif
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->categorias->all() as $categoria)
											<tr>
												<td>{{$categoria->categoria->id}}</td>
												<td>{{$categoria->categoria->name}}</td>
												<td>
													@if($categoria->categoria->grupo_evento_id)
														Grupo de Evento: #{{$categoria->categoria->grupo_evento->id}} - {{$categoria->categoria->grupo_evento->name}}
													@else
														@if($categoria->categoria->evento_id)
															Evento: #{{$categoria->categoria->evento->id}} - {{$categoria->categoria->evento->name}}
														@else
															Estou Confuso. Não há vínculo.
														@endif
													@endif
												</td>

                                                @if(
                                                    env("XADREZSUICOPAG_URI",null) &&
                                                    env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                                    env("XADREZSUICOPAG_SYSTEM_TOKEN",null) &&
                                                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1,10,11]) &&
                                                    $evento->xadrezsuicopag_uuid != ""
                                                )
                                                    <td>
                                                        @if($categoria->xadrezsuicopag_uuid)
                                                            @php($xadrezsuicopag_category_request = $xadrezsuicopag_controller->factory("category")->get($evento->xadrezsuicopag_uuid,$categoria->xadrezsuicopag_uuid))
                                                            @if($xadrezsuicopag_category_request->ok == 1)
                                                                {{$xadrezsuicopag_category_request->category->uuid}} -
                                                                {{$xadrezsuicopag_category_request->category->name}}
                                                            @else
                                                                Há um registro cadastrado, mas não existe uma categoria com este registro cadastrada no XadrezSuíçoPAG.
                                                            @endif
                                                        @else
                                                            -- Não há --
                                                        @endif
                                                    </td>
                                                @endif
												<td>
													<a class="btn btn-success" href="{{url("/evento/".$evento->id."/categoria/edit/".$categoria->id)}}" role="button"><i class="fa fa-edit"></i></a>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/categoria/remove/".$categoria->id)}}" role="button"><i class="fa fa-times"></i></a>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>
			<div role="tabpanel" class="tab-pane" id="torneio">
				<br/>
				@if(
					(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
				)
					<section class="col-lg-12 connectedSortable">

						<!-- Torneio -->
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Novo Torneio</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/torneios/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="name">Nome</label>
										<input name="name" id="name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="tipo_torneio_id">Tipo de Torneio</label>
										<select id="tipo_torneio_id" name="tipo_torneio_id" class="form-control">
											<option value="">-- Selecione --</option>
											@foreach(\App\TipoTorneio::all() as $tipo_torneio)
												<option value="{{$tipo_torneio->id}}">{{$tipo_torneio->name}}</option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<label for="softwares_id">Software</label>
										<select id="torneio_softwares_id" name="softwares_id" class="form-control">
											<option value="">-- Selecione --</option>
											@foreach(\App\Software::all() as $software)
												<option value="{{$software->id}}">{{$software->name}}</option>
											@endforeach
										</select>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					</section>
				@endif
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Torneios</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_torneio" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Categorias</th>
											<th>Inscritos</th>
											<th>Resultados Importados?</th>
											<th>Tipo de Torneio</th>
											<th>Template de Torneio</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->torneios->all() as $torneio)
											<tr>
												<td>{{$torneio->id}}</td>
												<td>{{$torneio->name}}</td>
												<td>
													@foreach($torneio->categorias->all() as $categoria)
														{{$categoria->categoria->name}},
													@endforeach
												</td>
												<td>
                                                    Total de Inscritos: {{$torneio->getCountInscritos()}}<br/>
                                                    @if(
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                                                    )
                                                        Confirmados: {{$torneio->getCountInscritosConfirmados()}}<br/>
                                                        Presentes: {{$torneio->quantosInscritosPresentes()}}<br/>
                                                        Com Resultado: {{$torneio->getCountInscritosResultados()}}
                                                        <hr/>
                                                        @if($evento->xadrezsuicopag_uuid)
                                                            <strong>Pagamento:</strong><br/>
                                                            Pagos: <strong>{{$torneio->howManyPaid()}}</strong><br/>
                                                            Pagamento Pendente: <strong>{{$torneio->howManyNotPaid()}}</strong><br/>
                                                            Gratuidades (Categorias Gratuitas): <strong>{{$torneio->howManyFree()}}</strong>
                                                        @endif
                                                        @if($evento->is_lichess_integration)
                                                            <strong>Torneio Lichess.org</strong><br/>
                                                            Inscritos: <strong>{{$torneio->getCountLichessConfirmadosnoTorneio()}}</strong><br/>
                                                            Não Inscritos: <strong>{{$torneio->getCountInscritos() - $torneio->getCountLichessConfirmadosnoTorneio()}}</strong>
                                                        @endif
                                                    @endif
												</td>
												<td>
                                                    {{$torneio->getIsResultadosImportados()}}
												</td>
												<td>
													{{$torneio->tipo_torneio->name}}
												</td>
												<td>
													@if($torneio->template)
														{{$torneio->template->name}}
													@else
														-
													@endif
												</td>
												<td>

													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/edit/".$torneio->id)}}" role="button">Editar</a>
														@if($torneio->tipo_torneio->id != 3)  <a class="btn btn-sm btn-warning" href="{{url("/evento/".$evento->id."/torneios/union/".$torneio->id)}}" role="button">Unir Torneios</a><br/> @endif
													@endif
													<a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes")}}" role="button">Inscrições</a>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														@if(!$evento->e_resultados_manuais && !$torneio->evento->is_lichess_integration && !$torneio->software->isChessCom()) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/resultados/file")}}" role="button">Resultados</a><br/> @endif
														@if(!$evento->e_resultados_manuais && !$torneio->evento->is_lichess_integration && !$torneio->software->isChessCom()) <a class="btn btn-default" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/emparceiramentos")}}" role="button">Emparceiramentos</a><br/> @endif

														@if($torneio->tipo_torneio->id == 3) <a class="btn btn-block btn-lg btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3")}}" role="button">Gerenciamento do Torneio</a><br/> @endif

                                                        <hr/>
                                                        <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm")}}" role="button" target="_blank">Baixar Inscrições Confirmadas</a><br/>
                                                        @if(
                                                            env("XADREZSUICOPAG_URI",null) &&
                                                            env("XADREZSUICOPAG_SYSTEM_ID",null) &&
                                                            env("XADREZSUICOPAG_SYSTEM_TOKEN",null)
                                                        )
                                                            @if($evento->isPaid())
                                                                <a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/paid")}}" role="button" target="_blank">Baixar Inscrições Pagas</a><br/>
                                                            @endif
                                                        @endif
														<a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/all")}}" role="button" target="_blank">Baixar Todas as Inscrições</a><br/>
														@if($evento->exportacao_sm_modelo == 6) <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/teams/confirmed")}}" role="button" target="_blank">Baixar Todos os Times com Enxadristas Confirmados</a><br/> @endif
														@if($evento->exportacao_sm_modelo == 6) <a class="btn btn-warning" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/sm/teams")}}" role="button" target="_blank">Baixar Todos os Times</a><br/> @endif
                                                        <hr/>

													@endif
													<a class="btn btn-info" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes")}}" role="button" target="_blank">Imprimir Inscrições</a><br/>
													<a class="btn btn-info" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético)</a><br/>
													<a class="btn btn-info" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/inscricoes/relatorio/inscricoes/alfabetico/cidade")}}" role="button" target="_blank">Imprimir Inscrições (Alfabético por Cidade/Clube)</a><br/>
													@if($torneio->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/delete/".$torneio->id)}}" role="button">Apagar</a> @endif
                                                    @if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()
                                                    )
                                                    @if($evento->torneios()->count() > 1)
                                                        <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/migrate_to_new_event")}}" role="button" target="_blank">Separar em um novo evento (Admin)</a><br/>

                                                        @endif
                                                    @endif
                                                    @if($torneio->evento->is_lichess_integration)
                                                        <hr/>
                                                        <strong>Opções Lichess.org</strong><br/>
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/check_players_in")}}" role="button">Conferir Inscrições no Torneio do Lichess.org</a><br/>
                                                        Última Atualização: {{$torneio->getLastLichessPlayersUpdate()}}<br/>
														@if($torneio->evento->data_inicio <= date("Y-m-d")) <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/get_results")}}" role="button">Inserir Resultados do Torneio do Lichess.org</a><br/> @endif
														<a class="btn btn-danger" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/lichess/remove_lichess_players_not_found")}}" role="button">REMOVER os Players do Lichess.org que NÃO foram encontrados</a><br/>
                                                    @endif
                                                    @if($torneio->software->isChessCom())
                                                        <hr/>
                                                        <strong>Opções Chess.com</strong><br/>
                                                        @if($torneio->hasConfig("chesscom_tournament_slug"))
                                                            <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/chesscom/check_players_in")}}" role="button">Conferir Inscrições no Torneio do Chess.com</a><br/>
                                                            Última Atualização: {{$torneio->getLastChessComPlayersUpdate()}}<br/>
                                                            @if($torneio->evento->data_inicio <= date("Y-m-d"))
                                                                <a class="btn btn-success" href="{{url("/evento/".$evento->id."/torneios/".$torneio->id."/chesscom/get_results")}}" role="button">Importar Resultados do Torneio do Chess.com</a><br/>
                                                            @endif
                                                        @else
                                                            <strong>Erro!</strong> O torneio ainda não possui a configuração do slug do torneio no Chess.com configurada. Edite este torneio e a configure para ser possível prosseguir.
                                                        @endif
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>

			<div role="tabpanel" class="tab-pane" id="campo_personalizado">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="alert alert-warning alert-dismissible" role="alert">
						<strong>Alerta!</strong><br/>
						Caso necessite de alguma informação adicional para este evento, você pode criar um campo personalizado para o Evento, porém, se esta informação é necessária a todos os eventos do Grupo de Evento, o correto é criar um campo personalizado para o Grupo de Evento.
					</div>
					@if(
						\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
						\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
					    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
					)
						<div class="box box-primary collapsed-box">
							<div class="box-header">
								<h3 class="box-title">Novo Campo Personalizado</h3>
								<div class="pull-right box-tools">
									<button type="button" class="btn btn-primary btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="" style="margin-right: 5px;" data-original-title="Collapse">
										<i class="fa fa-plus"></i></button>
								</div>
							</div>
							<!-- form start -->
							<form method="post" action="{{url("/evento/".$evento->id."/campos/new")}}">
								<div class="box-body">
									<div class="form-group">
										<label for="campo_name">Nome *</label>
										<input name="name" id="campo_name" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="campo_question">Questão *</label>
										<input name="question" id="campo_question" class="form-control" type="text" />
									</div>
									<div class="form-group">
										<label for="campo_type">Tipo de Campo *</label>
										<select name="type" id="campo_type" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif>
											<option value="">--- Selecione um tipo de campo ---</option>
											<option value="select">Seleção</option>
										</select>
									</div>
									<div class="form-group">
										<label for="campo_validator">Validação</label>
										<select name="validator" id="campo_validator" class="form-control width-100" @if(!\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal()) disabled="disabled" @endif>
											<option value="">--- Você pode selecionar uma validação ---</option>
											<option value="cpf">CPF</option>
										</select>
									</div>
								</div>
								<!-- /.box-body -->

								<div class="box-footer">
									<button type="submit" class="btn btn-success">Enviar</button>
									<input type="hidden" name="_token" value="{{ csrf_token() }}">
								</div>
							</form>
						</div>
					@endif

					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Campos Personalizados Adicionais</h3>
						</div>
						<!-- form start -->
							<div class="box-body">
								<table id="tabela_categoria" class="table-responsive table-condensed table-striped" style="width: 100%">
									<thead>
										<tr>
											<th>#</th>
											<th>Nome</th>
											<th>Questão</th>
											<th>Ativo?</th>
											<th width="20%">Opções</th>
										</tr>
									</thead>
									<tbody>
										@foreach($evento->campos_adicionais->all() as $campo)
											<tr>
												<td>{{$campo->id}}</td>
												<td>{{$campo->name}}</td>
												<td>{{$campo->question}}</td>
												<td>@if($campo->is_active) Sim @else Não @endif</td>
												<td>
													@if(
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
														\Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
													)
														<a class="btn btn-success" href="{{url("/evento/".$evento->id."/campos/dashboard/".$campo->id)}}" role="button"><i class="fa fa-dashboard"></i></a>
														@if($campo->isDeletavel()) <a class="btn btn-danger" href="{{url("/evento/".$evento->id."/campos/delete/".$campo->id)}}" role="button"><i class="fa fa-times"></i></a> @endif
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
							<!-- /.box-body -->
					</div>
				</section>
			</div>
            <div role="tabpanel" class="tab-pane" id="email_template">
                <br/>
                <section class="col-lg-12 connectedSortable">
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Templates de E-mail</h3>
                        </div>
                        <!-- form start -->
                            <div class="box-body">
                                <table id="tabela_email_templates" class="table-responsive table-condensed table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Nome</th>
                                            <th>Assunto do E-mail</th>
                                            <th width="20%">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->email_templates->all() as $template)
                                            <tr>
                                                <td>{{$template->id}}</td>
                                                <td>{{$template->name}}</td>
                                                <td>{{$template->subject}}</td>
                                                <td>
                                                    <a class="btn btn-default" href="{{url("/emailtemplate/edit/".$template->id)}}" role="button">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                    </div>
                </section>
            </div>
            <div role="tabpanel" class="tab-pane" id="classificator">
                <br/>
                <section class="col-lg-12 connectedSortable">
                    @foreach($evento->event_classificates->all() as $event_classificates)

                        <div class="box box-primary">
                            <div class="box-header">
                                <h3 class="box-title">XadrezSuíço Classificador - Regras e Processos de Classificação para o Evento #{{$event_classificates->event->id}} - {{$event_classificates->event->name}}</h3>
                            </div>
                            <!-- form start -->
                                <div class="box-body">
                                    @php($total_classified = $event_classificates->howMuchClassificated())
                                    @if($total_classified > 0)
                                        <div class="alert alert-success" role="alert">
                                            <strong>Classificado!</strong><br/>
                                            O presente classificador foi classificado.<br/>
                                            Total de Classificados: {{$total_classified}}
                                        </div>
                                    @endif
                                    <ul class="nav nav-pills">
                                        <li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/".$event_classificates->id."/process")}}">!!!! Processar Classificações (Use com cuidado)</a></li>
                                        <li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/".$event_classificates->id."/classificated/delete")}}">!!!! Remover classificados</a></li>
                                        @if($total_classified > 0)
                                            <li role="presentation"><a href="{{url("/inscricao/classificados/".$evento->id."/".$event_classificates->id)}}">[PÚBLICO] Lista de Classificados</a></li>
                                        @endif
                                    </ul>

                                    <ul class="nav nav-pills">
                                        <li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/".$event_classificates->id."/rule/new")}}">Nova Regra</a></li>
                                    </ul>
                                    <table id="tabela_classificators" class="table-responsive table-condensed table-striped" style="width: 100%">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Regra</th>
                                                <th>Configurações</th>
                                                @if($total_classified > 0)
                                                    <th>Total de Classificados</th>
                                                @endif
                                                <th width="20%">Opções</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($event_classificates->rules->all() as $rule)
                                                <tr>
                                                    <td>{{$rule->id}}</td>
                                                    <td>{{$rule->getRuleName()}}</td>
                                                    <td>
                                                        @switch($rule->type)
                                                            @case(\App\Enum\ClassificationTypeRule::POSITION)
                                                                Posição Relativa: {{$rule->value}}
                                                            @break
                                                            @case(\App\Enum\ClassificationTypeRule::POSITION_ABSOLUTE)
                                                                Posição Absoluta: {{$rule->value}}
                                                            @break
                                                            @case(\App\Enum\ClassificationTypeRule::PRE_CLASSIFICATE)
                                                                Pré-classificação pelo Evento: #{{$rule->event->id}} - {{$rule->event->name}}
                                                            @break
                                                            @case(\App\Enum\ClassificationTypeRule::PLACE_BY_QUANTITY)
                                                                @if($rule->is_absolute)
                                                                    Vagas a Cada: {{$rule->value}} (Completos).
                                                                @else
                                                                    Vagas a Cada: {{$rule->value}} (ou fração).
                                                                @endif
                                                            @break
                                                        @endswitch
                                                    </td>
                                                    @if($total_classified > 0)
                                                        <td>{{$rule->howMuchClassificated()}}</td>
                                                    @endif
                                                    <td>
                                                        <a class="btn btn-default" href="{{url("/evento/".$evento->id."/classificator/".$event_classificates->id."/rule/edit/".$rule->id)}}" role="button">Editar</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                        </div>
                    @endforeach
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">XadrezSuíço Classificador</h3>
                        </div>
                        <!-- form start -->
                            <div class="box-body">

                                <ul class="nav nav-pills">
                                    <li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/new")}}">Novo Classificador</a></li>
                                </ul>
                                <table id="tabela_classificators" class="table-responsive table-condensed table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Evento Classificador à Este</th>
                                            <th width="20%">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->event_classificators->all() as $event_classificate)
                                            <tr>
                                                <td>{{$event_classificate->id}}</td>
                                                <td>
                                                    {{$event_classificate->event_classificator->name}}<br/>
                                                    Grupo de Evento: {{$event_classificate->event_classificator->grupo_evento->name}}
                                                </td>
                                                <td>

                                                    @if(
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($event_classificate->event_classificator->id,[3,4,5]) ||
                                                        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($event_classificate->event_classificator->grupo_evento->id,[6,7])
                                                    )
                                                        <a class="btn btn-warning mr-1" href="{{url("/evento/dashboard/".$event_classificate->event_classificator->id)}}" role="button">Acessar Dashboard do Evento</a>
                                                    @endif
                                                    <a class="btn btn-default" href="{{url("/evento/".$evento->id."/classificator/edit/".$event_classificate->id)}}" role="button">Editar</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                    </div>
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title">Categorias Vinculadas</h3>
                        </div>
                        <!-- form start -->
                            <div class="box-body">

                                <ul class="nav nav-pills">
                                    <li role="presentation"><a href="{{url("/evento/".$evento->id."/classificator/category/new")}}">Novo Vínculo de Categoria</a></li>
                                </ul>
                                <table id="tabela_classificators" class="table-responsive table-condensed table-striped" style="width: 100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Categoria Base</th>
                                            <th>Categoria deste Evento</th>
                                            <th width="20%">Opções</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($evento->categorias_cadastradas->all() as $categoria)
                                            @foreach($categoria->event_classificates->all() as $event_class_category)
                                                <tr>
                                                    <td>{{$event_class_category->id}}</td>
                                                    <td>
                                                        {{$event_class_category->category->id}} - {{$event_class_category->category->name}}<br/>
                                                        @if($event_class_category->category->evento)
                                                            Evento: {{$event_class_category->category->evento->name}}
                                                        @else
                                                            Grupo de Evento: {{$event_class_category->category->grupo_evento->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{$event_class_category->category_classificator->id}} - {{$event_class_category->category_classificator->name}}<br/>
                                                        @if($event_class_category->category_classificator->evento)
                                                            Evento: {{$event_class_category->category_classificator->evento->name}}
                                                        @else
                                                            Grupo de Evento: {{$event_class_category->category_classificator->grupo_evento->name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-default" href="{{url("/evento/".$evento->id."/classificator/category/edit/".$event_class_category->id)}}" role="button">Editar</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.box-body -->
                    </div>
                </section>
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

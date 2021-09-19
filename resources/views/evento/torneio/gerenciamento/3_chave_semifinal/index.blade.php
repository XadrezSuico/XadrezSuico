@extends('adminlte::page')

@section("title", "Gerenciamento de Torneio de Chave Semi-final/Final sem Disputa de 3o lugar")

@section("header_meta")
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
@endsection

@section('content_header')
    <h1 style="text-align: center">Gerenciamento de Torneio</h1>
    <div class="row">
        <div class="col-md-12">
            <h2 style="font-size: 2rem; text-align: center">Evento: {{$torneio->evento->name}}</h2>
        </div>
        <div class="col-md-6">
            <h3 style="font-size: 1.5rem; text-align: center"><strong>Torneio: {{$torneio->name}}</strong></h3>
        </div>
        <div class="col-md-6">
            <h3 style="font-size: 1.5rem; text-align: center"><strong>Tipo de Torneio: {{$torneio->tipo_torneio->name}}</strong></h3>
        </div>
    </div>
@stop


@section('css')
	<style>
		.display-none, .displayNone{
			display: none;
		}
		.width-100{
			width: 100% !important;
		}

        .emparceiramento{
            width: 100%;
            border: 1px solid #000;
            padding: 3px;
            background: rgb(223, 223, 223);
        }

        .arrows{
            font-size: 3rem;
        }

        .enxadrista_white{
            display:inline-block;
            background: white;
            color: black;
            padding: 4px;
            border-radius: 2px;
        }

        .enxadrista_black{
            display:inline-block;
            background: black;
            color: white;
            padding: 4px;
            border-radius: 2px;
        }

        .enxadrista_without_color{
            display:inline-block;
            background: gray;
            color: white;
            padding: 4px;
            border-radius: 2px;
        }

        .btn_enxadrista_color{
            width: 100%;
            word-wrap: break-word !important;
            white-space: inherit !important;
        }
        .btn_enxadrista_color.bg-white{
            background: white;
            color: black;
        }
        .btn_enxadrista_color.bg-black{
            background: black;
            color: white;
        }

        .resultados_confrontos{
            font-size: 2rem;
        }

        .resultados_confrontos .resultado{
            display: inline-block;
            background: #d2d6de;
            border-radius: 4px;
            padding: 0.2rem 0.4rem;
            margin: 0.5rem 0;
            font-weight: bold;

        }
	</style>
@endsection

@section("content")
<!-- Main row -->
<ul class="nav nav-pills">
  <li role="presentation"><a href="/evento/dashboard/{{$torneio->evento->id}}?tab=torneio">Voltar a Lista de Torneios</a></li>
</ul>
<div class="row">
  <!-- Left col -->
	<div>

		<!-- Nav tabs -->
		<ul class="nav nav-tabs" role="tablist">
			<li role="presentation" class="active"><a id="tab_gerenciamento" href="#gerenciamento" aria-controls="gerenciamento" role="tab" data-toggle="tab">Gerenciamento</a></li>
		</ul>

		<!-- Tab panes -->
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="gerenciamento">
				<br/>
				<section class="col-lg-12 connectedSortable">
					<div class="box box-primary" id="inscricao">
						<div class="box-header">
							<h3 class="box-title">Gerenciamento</h3>
						</div>
                        <div class="box-body">
                                @foreach($torneio->rodadas->all() as $rodada)
                                    <!-- RODADA {{$rodada->id}} -->
                                    <div class="row">
                                        @foreach($rodada->emparceiramentos->all() as $emparceiramento)
                                            <!-- EMPARCEIRAMENTO {{$emparceiramento->id}} -->
                                            <div class=" @if($rodada->numero == 1) col-xs-4 col-xs-offset-1 @else col-xs-9 col-xs-offset-1 @endif">
                                                <div class="emparceiramento text-center">
                                                    <div id="emparceiramento_{{$emparceiramento->id}}_enxadrista_a" class="center-block @if($emparceiramento->cor_a == 1) enxadrista_white @else @if($emparceiramento->cor_a == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                        <strong>@if($emparceiramento->inscricao_a) {{$emparceiramento->inscricao_A->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$emparceiramento->inscricao_A->enxadrista->chess_com_username}}) @endif <span id="emparceiramento_{{$emparceiramento->id}}_a_trofeu" class=" @if($emparceiramento->resultado != -1) display-none @endif "><i class="fa fa-trophy"></i></span> @else - @endif</strong>
                                                    </div>
                                                    @if($emparceiramento->inscricao_a) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$emparceiramento->id}}_resultado_a_label_partida">{{$emparceiramento->getResultadoA()}}</div><br/></div>@endif
                                                    <i class="fa fa-times center-block"></i>
                                                    @if($emparceiramento->inscricao_b) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$emparceiramento->id}}_resultado_b_label_partida">{{$emparceiramento->getResultadoB()}}</div><br/></div>@endif
                                                    <div id="emparceiramento_{{$emparceiramento->id}}_enxadrista_b" class="center-block @if($emparceiramento->cor_b == 1) enxadrista_white @else @if($emparceiramento->cor_b == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                        <strong>@if($emparceiramento->inscricao_b) {{$emparceiramento->inscricao_B->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$emparceiramento->inscricao_B->enxadrista->chess_com_username}}) @endif<span id="emparceiramento_{{$emparceiramento->id}}_b_trofeu" class=" @if($emparceiramento->resultado != 1) display-none @endif "><i class="fa fa-trophy"></i></span> @else - @endif</strong>
                                                    </div>
                                                    @if($emparceiramento->inscricao_A || $emparceiramento->inscricao_B)
                                                        <hr/>
                                                        @if($emparceiramento->armageddons()->count() == 0 && !is_int($emparceiramento->resultado) )
                                                            <strong>Gerenciamento da Partida:</strong><br/>
                                                            Enxadrista de Brancas:<br/>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <button id="emparceiramento_{{$emparceiramento->id}}_cor_a_btn" class="btn btn_enxadrista_color @if($emparceiramento->cor_a == 1) bg-white @else @if($emparceiramento->cor_a == 2) bg-black @endif @endif" onclick="setWhite({{$emparceiramento->id}},'a')">@if($emparceiramento->inscricao_a) {{$emparceiramento->inscricao_A->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$emparceiramento->inscricao_A->enxadrista->chess_com_username}}) @endif @endif</button>
                                                                    <input type="hidden" id="emparceiramento_{{$emparceiramento->id}}_cor_a" value="{{$emparceiramento->cor_a}}" autocomplete="off"/>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <button id="emparceiramento_{{$emparceiramento->id}}_cor_b_btn" class="btn btn_enxadrista_color @if($emparceiramento->cor_b == 1) bg-white @else @if($emparceiramento->cor_b == 2) bg-black @endif @endif" onclick="setWhite({{$emparceiramento->id}},'b')">@if($emparceiramento->inscricao_b) {{$emparceiramento->inscricao_B->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$emparceiramento->inscricao_B->enxadrista->chess_com_username}}) @endif @endif</button>
                                                                    <input type="hidden" id="emparceiramento_{{$emparceiramento->id}}_cor_b" value="{{$emparceiramento->cor_b}}" autocomplete="off"/>
                                                                </div>
                                                            </div>
                                                            <br/><br/>
                                                            Resultado:<br/>
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="btn-group">
                                                                        @if($emparceiramento->inscricao_a) <h3><span class="label label-default" id="emparceiramento_{{$emparceiramento->id}}_resultado_a_label">{{$emparceiramento->getResultadoA()}}</span></h3> @endif
                                                                        <input type="hidden" id="emparceiramento_{{$emparceiramento->id}}_resultado_a" value="{{$emparceiramento->getResultadoA()}}" autocomplete="off" />
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="btn-group">
                                                                        @if($emparceiramento->inscricao_b) <h3><span class="label label-default" id="emparceiramento_{{$emparceiramento->id}}_resultado_b_label">{{$emparceiramento->getResultadoB()}}</span></h3> @endif
                                                                        <input type="hidden" id="emparceiramento_{{$emparceiramento->id}}_resultado_b" value="{{$emparceiramento->getResultadoB()}}" autocomplete="off" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row emparceiramento_{{$emparceiramento->id}}_controle_resultados @if($emparceiramento->resultado != NULL) display-none @endif">
                                                                <div class="col-sm-6">
                                                                    <div class="btn-group" role="group">
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_add({{$emparceiramento->id}},'a',1)">+1</button>
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_add({{$emparceiramento->id}},'a',0.5)">+0.5</button>
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_sub({{$emparceiramento->id}},'a',0.5)">-0.5</button>
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_sub({{$emparceiramento->id}},'a',1)">-1</button>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="btn-group" role="group">
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_add({{$emparceiramento->id}},'b',1)">+1</button>
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_add({{$emparceiramento->id}},'b',0.5)">+0.5</button>
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_sub({{$emparceiramento->id}},'b',0.5)">-0.5</button>
                                                                        <button type="button" class="btn btn-default btn-xs" onclick="resultado_sub({{$emparceiramento->id}},'b',1)">-1</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <br/><br/>
                                                            <button class="btn btn-success @if($emparceiramento->resultado != NULL) display-none @endif" onClick="enviarEmparceiramentoData({{$emparceiramento->id}})">Salvar</button><br/><br/>
                                                            <hr/>
                                                            <a id="emparceiramento_{{$emparceiramento->id}}_btn_desempate" class="btn btn-success @if(!($emparceiramento->getResultadoA() == $emparceiramento->getResultadoB() && $emparceiramento->getResultadoA() != 0)) display-none @endif" href="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3/armageddon/".$emparceiramento->id)}}">Gerar Armageddon</a><br/>
                                                            <button id="homologar_emp_{{$emparceiramento->id}}" class="btn btn-warning @if($emparceiramento->getResultadoA() == $emparceiramento->getResultadoB() || is_int($emparceiramento->resultado)) display-none @endif" onClick="homologarEmparceiramento({{$emparceiramento->id}})">Aprovar Resultado</button><br/><br/>
                                                        @endif
                                                        <button id="desaprovar_emp_{{$emparceiramento->id}}" class="btn btn-warning @if(($emparceiramento->resultado == NULL && !is_int($emparceiramento->resultado)) || $emparceiramento->hasArmageddonsAproved()) display-none @endif" onClick="desaprovarEmparceiramento({{$emparceiramento->id}})">Desaprovar Resultado</button><br/><br/>

                                                        @foreach($emparceiramento->armageddons->all() as $armageddon)
                                                        <hr/>
                                                            <h5><strong>Desempate:</strong></h5>
                                                            <div id="emparceiramento_{{$armageddon->id}}_enxadrista_a" class="center-block @if($armageddon->cor_a == 1) enxadrista_white @else @if($armageddon->cor_a == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                                <strong>@if($armageddon->inscricao_a) {{$armageddon->inscricao_A->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$armageddon->inscricao_A->enxadrista->chess_com_username}}) @endif <span id="emparceiramento_{{$armageddon->id}}_b_trofeu" class=" @if($armageddon->resultado != -1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else - @endif</strong>
                                                            </div><br/>
                                                            @if($armageddon->inscricao_a) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$armageddon->id}}_resultado_a_label_partida">{{$armageddon->getResultadoA()}}</div><br/></div>@endif
                                                            <i class="fa fa-times center-block"></i>
                                                            @if($armageddon->inscricao_b) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$armageddon->id}}_resultado_b_label_partida">{{$armageddon->getResultadoB()}}</div><br/></div>@endif
                                                            <div id="emparceiramento_{{$armageddon->id}}_enxadrista_b" class="center-block @if($armageddon->cor_b == 1) enxadrista_white @else @if($armageddon->cor_b == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                                <strong>@if($armageddon->inscricao_b) {{$armageddon->inscricao_B->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$armageddon->inscricao_B->enxadrista->chess_com_username}}) @endif <span id="emparceiramento_{{$armageddon->id}}_b_trofeu" class=" @if($armageddon->resultado != 1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else - @endif</strong>
                                                            </div>
                                                            @if($armageddon->inscricao_a && $armageddon->inscricao_b)
                                                                <hr/>
                                                                @if(!is_int($armageddon->resultado))
                                                                    <strong>Gerenciamento da Partida:</strong><br/>
                                                                    Enxadrista de Brancas:<br/>
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <button id="emparceiramento_{{$armageddon->id}}_cor_a_btn" class="btn btn_enxadrista_color @if($armageddon->cor_a == 1) bg-white @else @if($armageddon->cor_a == 2) bg-black @endif @endif" onclick="setWhite({{$armageddon->id}},'a')">@if($armageddon->inscricao_a) {{$armageddon->inscricao_A->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$armageddon->inscricao_A->enxadrista->chess_com_username}}) @endif @endif</button>
                                                                            <input type="hidden" id="emparceiramento_{{$armageddon->id}}_cor_a" value="{{$armageddon->cor_a}}" autocomplete="off"/>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <button id="emparceiramento_{{$armageddon->id}}_cor_b_btn" class="btn btn_enxadrista_color @if($armageddon->cor_b == 1) bg-white @else @if($armageddon->cor_b == 2) bg-black @endif @endif" onclick="setWhite({{$armageddon->id}},'b')">@if($armageddon->inscricao_b) {{$armageddon->inscricao_B->enxadrista->name}} @if($torneio->evento->is_chess_com) ({{$armageddon->inscricao_B->enxadrista->chess_com_username}}) @endif @endif</button>
                                                                            <input type="hidden" id="emparceiramento_{{$armageddon->id}}_cor_b" value="{{$armageddon->cor_b}}" autocomplete="off"/>
                                                                        </div>
                                                                    </div>
                                                                    <br/><br/>
                                                                    Resultado:<br/>
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="btn-group">
                                                                                @if($armageddon->inscricao_a) <h3><span class="label label-default" id="emparceiramento_{{$armageddon->id}}_resultado_a_label">{{$armageddon->getResultadoA()}}</span></h3> @endif
                                                                                <input type="hidden" id="emparceiramento_{{$armageddon->id}}_resultado_a" value="{{$armageddon->getResultadoA()}}" autocomplete="off" />
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="btn-group">
                                                                                @if($armageddon->inscricao_b) <h3><span class="label label-default" id="emparceiramento_{{$armageddon->id}}_resultado_b_label">{{$armageddon->getResultadoB()}}</span></h3> @endif
                                                                                <input type="hidden" id="emparceiramento_{{$armageddon->id}}_resultado_b" value="{{$armageddon->getResultadoB()}}" autocomplete="off" />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            <div class="btn-group" role="group">
                                                                                <button type="button" class="btn btn-default btn-xs" onclick="resultado_add({{$armageddon->id}},'a',1)">+1</button>
                                                                                <button type="button" class="btn btn-default btn-xs" onclick="resultado_sub({{$armageddon->id}},'a',1)">-1</button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            <div class="btn-group" role="group">
                                                                                <button type="button" class="btn btn-default btn-xs" onclick="resultado_add({{$armageddon->id}},'b',1)">+1</button>
                                                                                <button type="button" class="btn btn-default btn-xs" onclick="resultado_sub({{$armageddon->id}},'b',1)">-1</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <br/><br/>
                                                                    <button class="btn btn-success @if($armageddon->resultado != NULL) display-none @endif " onClick="enviarEmparceiramentoData({{$armageddon->id}})">Salvar</button><br/><br/>
                                                                    <hr/>
                                                                    <button id="homologar_emp_{{$armageddon->id}}" class="btn btn-warning @if($armageddon->resultado_a == $armageddon->resultado_b) display-none @endif" onClick="homologarEmparceiramento({{$armageddon->id}})">Aprovar Resultado</button><br/><br/>
                                                                @endif
                                                                <button id="desaprovar_emp_{{$armageddon->id}}" class="btn btn-warning @if($armageddon->resultado == NULL) display-none @endif" onClick="desaprovarEmparceiramento({{$armageddon->id}})">Desaprovar Resultado</button><br/><br/>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                @if($rodada->numero < 2)
                                                    <div class="text-center arrows">
                                                        <i class="fa fa-arrow-circle-down"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- /.box-body -->

                        <div class="box-footer">
                        </div>
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
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript" src="{{url("/js/jquery.mask.min.js")}}"></script>
<script type="text/javascript">
  $(document).ready(function(){
		@if($tab)
			$("#tab_{{$tab}}").tab("show");
		@endif
  });

  function resultado_add(emparceiramento, enxadrista, valor){
    var resultado_atual = parseFloat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val());
    resultado_atual = resultado_atual + valor;
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val(resultado_atual);
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista).concat("_label")).html(resultado_atual);
  }
  function resultado_sub(emparceiramento, enxadrista, valor){
    var resultado_atual = parseFloat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val());
    if(resultado_atual-valor < 0){
        resultado_atual = 0;
    }else{
        resultado_atual = resultado_atual - valor;
    }
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista)).val(resultado_atual);
    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_").concat(enxadrista).concat("_label")).html(resultado_atual);
  }

  function setWhite(emparceiramento, enxadrista){
      if(enxadrista == 'a'){
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).addClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_without_color");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).addClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_without_color");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).addClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).removeClass("bg-black");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).removeClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).addClass("bg-black");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a")).val(1);
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b")).val(2);
      }else{
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).addClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_a")).removeClass("enxadrista_without_color");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).addClass("enxadrista_white");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_black");
        $("#emparceiramento_".concat(emparceiramento).concat("_enxadrista_b")).removeClass("enxadrista_without_color");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).removeClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a_btn")).addClass("bg-black");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).addClass("bg-white");
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b_btn")).removeClass("bg-black");

        $("#emparceiramento_".concat(emparceiramento).concat("_cor_a")).val(2);
        $("#emparceiramento_".concat(emparceiramento).concat("_cor_b")).val(1);
      }
  }

  function enviarEmparceiramentoData(emparceiramento){
        var data = "";
        data = data.concat("emparceiramento_id=".concat(emparceiramento));
        data = data.concat("&cor_a=".concat($("#emparceiramento_".concat(emparceiramento).concat("_cor_a")).val()));
        data = data.concat("&cor_b=".concat($("#emparceiramento_".concat(emparceiramento).concat("_cor_b")).val()));
        data = data.concat("&resultado_a=".concat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val()));
        data = data.concat("&resultado_b=".concat($("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val()));
        data = data.concat("&_token={{ csrf_token() }}");
		$.ajax({
			type: "post",
			url: "{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3/api/setEmparceiramentoData")}}",
			data: data,
			dataType: "json",
			success: function(data){
                if(data.ok == 1){
                    Swal.fire({
                        text: 'Emparceiramento atualizado com sucesso.',
                        icon: 'success',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });

                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val(data.data.resultado_a);
                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val(data.data.resultado_b);

                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a_label")).html(data.data.resultado_a);
                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b_label")).html(data.data.resultado_b);

                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a_label_partida")).html(data.data.resultado_a);
                    $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b_label_partida")).html(data.data.resultado_b);
                }else{
                    Swal.fire({
                        text: data.message,
                        icon: 'error',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() == $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val() && $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != 0){
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).removeClass("display-none");
                }else{
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).addClass("display-none");
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val()){
                    $("#homologar_emp_".concat(emparceiramento)).removeClass("display-none");
                }else{
                    $("#homologar_emp_".concat(emparceiramento)).addClass("display-none");
                }
            }
        });
  }
  function homologarEmparceiramento(emparceiramento){
        var data = "";
		$.ajax({
			type: "get",
			url: "{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3/api/homologateEmparceiramento")}}/".concat(emparceiramento),
			dataType: "json",
			success: function(data){
                if(data.ok == 1){
                    Swal.fire({
                        text: 'Emparceiramento homologado com sucesso. Recarregando página...',
                        icon: 'success',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else{
                    Swal.fire({
                        text: data.message,
                        icon: 'error',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() == $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val() && $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != 0){
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).removeClass("display-none");
                }else{
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).addClass("display-none");
                }
            }
        });
  }
  function desaprovarEmparceiramento(emparceiramento){
        var data = "";
		$.ajax({
			type: "get",
			url: "{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/gerenciamento/torneio_3/api/unaproveEmparceiramento")}}/".concat(emparceiramento),
			dataType: "json",
			success: function(data){
                if(data.ok == 1){
                    Swal.fire({
                        text: 'Emparceiramento homologado com sucesso. Recarregando página...',
                        icon: 'success',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                    setTimeout(function(){
                        location.reload();
                    },1000);
                }else{
                    Swal.fire({
                        text: data.message,
                        icon: 'error',
                        toast: true,
                        position: 'top-right',
                        timer: 3000,
                        timerProgressBar: true,
                    });
                }

                if($("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() == $("#emparceiramento_".concat(emparceiramento).concat("_resultado_b")).val() && $("#emparceiramento_".concat(emparceiramento).concat("_resultado_a")).val() != 0){
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).removeClass("display-none");
                }else{
                    $("#emparceiramento_".concat(emparceiramento).concat("_btn_desempate")).addClass("display-none");
                }
            }
        });
  }
</script>
@endsection

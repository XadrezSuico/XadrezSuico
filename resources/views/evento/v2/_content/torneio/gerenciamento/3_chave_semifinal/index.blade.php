<!-- Main row -->
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
                                                        <strong>@if($emparceiramento->inscricao_a) <a target="_blank" href="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$emparceiramento->inscricao_a)}}">{{$emparceiramento->inscricao_A->enxadrista->name}}</a> @if($emparceiramento->inscricao_A->confirmado)<i class="fa fa-check"></i> @endif @if($torneio->evento->is_chess_com) ({{$emparceiramento->inscricao_A->enxadrista->chess_com_username}}) @endif <span id="emparceiramento_{{$emparceiramento->id}}_a_trofeu" class=" @if($emparceiramento->resultado != -1) display-none @endif "><i class="fa fa-trophy"></i></span> @else - @endif</strong>
                                                    </div>
                                                    @if($emparceiramento->inscricao_a) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$emparceiramento->id}}_resultado_a_label_partida">{{$emparceiramento->getResultadoA()}}</div><br/></div>@endif
                                                    <i class="fa fa-times center-block"></i>
                                                    @if($emparceiramento->inscricao_b) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$emparceiramento->id}}_resultado_b_label_partida">{{$emparceiramento->getResultadoB()}}</div><br/></div>@endif
                                                    <div id="emparceiramento_{{$emparceiramento->id}}_enxadrista_b" class="center-block @if($emparceiramento->cor_b == 1) enxadrista_white @else @if($emparceiramento->cor_b == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                        <strong>@if($emparceiramento->inscricao_b) <a target="_blank" href="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$emparceiramento->inscricao_b)}}">{{$emparceiramento->inscricao_B->enxadrista->name}}</a> @if($emparceiramento->inscricao_B->confirmado)<i class="fa fa-check"></i> @endif @if($torneio->evento->is_chess_com) ({{$emparceiramento->inscricao_B->enxadrista->chess_com_username}}) @endif<span id="emparceiramento_{{$emparceiramento->id}}_b_trofeu" class=" @if($emparceiramento->resultado != 1) display-none @endif "><i class="fa fa-trophy"></i></span> @else - @endif</strong>
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
                                                                <strong>@if($armageddon->inscricao_a) <a target="_blank" href="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$armageddon->inscricao_a)}}">{{$armageddon->inscricao_A->enxadrista->name}}</a> @if($torneio->evento->is_chess_com) ({{$armageddon->inscricao_A->enxadrista->chess_com_username}}) @endif <span id="emparceiramento_{{$armageddon->id}}_b_trofeu" class=" @if($armageddon->resultado != -1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else - @endif</strong>
                                                            </div><br/>
                                                            @if($armageddon->inscricao_a) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$armageddon->id}}_resultado_a_label_partida">{{$armageddon->getResultadoA()}}</div><br/></div>@endif
                                                            <i class="fa fa-times center-block"></i>
                                                            @if($armageddon->inscricao_b) <div class="resultados_confrontos"><div class="resultado" id="emparceiramento_{{$armageddon->id}}_resultado_b_label_partida">{{$armageddon->getResultadoB()}}</div><br/></div>@endif
                                                            <div id="emparceiramento_{{$armageddon->id}}_enxadrista_b" class="center-block @if($armageddon->cor_b == 1) enxadrista_white @else @if($armageddon->cor_b == 2) enxadrista_black @else enxadrista_without_color @endif @endif">
                                                                <strong>@if($armageddon->inscricao_b) <a target="_blank" href="{{url("/evento/".$torneio->evento->id."/torneios/".$torneio->id."/inscricoes/edit/".$armageddon->inscricao_b)}}">{{$armageddon->inscricao_B->enxadrista->name}}</a> @if($torneio->evento->is_chess_com) ({{$armageddon->inscricao_B->enxadrista->chess_com_username}}) @endif <span id="emparceiramento_{{$armageddon->id}}_b_trofeu" class=" @if($armageddon->resultado != 1) display-none @endif "><i class="fa fa-trophy"></i></span>  @else - @endif</strong>
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

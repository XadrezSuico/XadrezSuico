<br/>
<div class="callout callout-info" style="margin-bottom: 20px;">
    <h4 style="margin-top: 0;"><i class="fa fa-info-circle"></i> Sobre esta aba</h4>
    <p style="margin-bottom: 12px;">Atalhos operacionais e interruptores do evento, agrupados por etapa:</p>
    <ul class="list-inline" style="margin-bottom: 12px; font-size: 14px;">
        <li><i class="fa fa-pencil-square-o text-green"></i> <strong>Inscrições</strong></li>
        <li class="text-muted"><i class="fa fa-long-arrow-right"></i></li>
        <li><i class="fa fa-random text-light-blue"></i> <strong>Emparceiramento</strong></li>
        <li class="text-muted"><i class="fa fa-long-arrow-right"></i></li>
        <li><i class="fa fa-sort-amount-desc text-yellow"></i> <strong>Classificação e resultados</strong></li>
    </ul>
    <p class="text-muted" style="margin-bottom: 0; font-size: 13px;">
        <i class="fa fa-external-link"></i> Alguns links abrem em nova aba.
        <span class="hidden-xs">&nbsp;·&nbsp;</span>
        <br class="visible-xs"/>
        <i class="fa fa-toggle-on"></i> Os interruptores são salvos automaticamente ao alterar.
    </p>
</div>

<div class="row">
    <section class="col-lg-6 connectedSortable">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-pencil-square-o"></i> Inscrições</h3>
            </div>
            <div class="box-body">
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
                            <div class="alert alert-warning" role="alert" style="margin-bottom: 12px;">
                                <strong>Aviso!</strong> Inscrição apenas pelo link compartilhado (abaixo).
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

                <a href="{{url("/evento/".$evento->id)}}/inscricoes/list" class="btn btn-app">
                    <i class="fa fa-list"></i>
                    Lista de Inscritos (completa)
                </a>

                <hr style="margin: 15px 0;"/>
                <p class="text-muted" style="margin-bottom: 10px;"><small>Configurações de inscrição</small></p>

                <div class="form-group" style="margin-bottom: 8px;">
                    <label class="d-flex align-items-center">
                        <div class="form-switch">
                            <input type="checkbox" id="toggle_inscricoes" @if(!$evento->is_inscricoes_bloqueadas) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span id="toggle_inscricoes_status" class="switch-label">Permitir Inscrições</span>
                    </label>
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="d-flex align-items-center">
                        <div class="form-switch">
                            <input type="checkbox" id="toggle_edicao_inscricao" @if($evento->permite_edicao_inscricao) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span id="toggle_edicao_inscricao_status" class="switch-label">Permitir Edição de Inscrição</span>
                    </label>
                </div>

                @if($evento->isPaid())
                    <hr style="margin: 15px 0;"/>
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
            </div>
        </div>
    </section>

    <section class="col-lg-6 connectedSortable">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-random"></i> Emparceiramento</h3>
            </div>
            <div class="box-body">
                <p class="text-muted" style="margin-top: 0;"><small>Exporte para o {{config("xadrezsuico.name","XadrezSuíço")}} Emparceirador ou compartilhe o acompanhamento público.</small></p>
                <a href="{{url("/evento/".$evento->id."/exports/emparceirador")}}" class="btn btn-app">
                    <i class="fa fa-download"></i>
                    Baixar Emparceirador (todas — sem dados)
                </a>
                <a href="{{url("/evento/".$evento->id."/exports/xadrezsuicoemparceirador/data")}}" class="btn btn-app">
                    <i class="fa fa-download"></i>
                    Baixar Emparceirador (confirmadas — com dados)
                </a>
                <hr style="margin: 15px 0;"/>
                <a href="{{url("/evento/acompanhar/".$evento->id)}}" class="btn btn-app">
                    <i class="fa fa-eye"></i>
                    Acompanhar Emparceiramentos (público)
                </a>
            </div>
        </div>
    </section>
</div>

@if(
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
)
<div class="row">
    <section class="col-lg-6 connectedSortable">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-sort-amount-desc"></i> Classificação e resultados</h3>
            </div>
            <div class="box-body">
                <a href="/evento/classificar/{{$evento->id}}" class="btn btn-success btn-app">
                    <i class="fa fa-sort"></i>
                    Classificar Evento
                </a>
                <a href="{{url("/evento/classificacao/".$evento->id)}}" class="btn btn-app">
                    <i class="fa fa-eye"></i>
                    Visualizar Classificação
                </a>

                <hr style="margin: 15px 0;"/>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="d-flex align-items-center">
                        <div class="form-switch">
                            <input type="checkbox" id="toggle_resultados" @if($evento->mostrar_resultados) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span id="toggle_resultados_status" class="switch-label">Permitir visualização pública dos resultados</span>
                    </label>
                </div>

                @if($evento->event_team_awards()->count() > 0)
                    <hr style="margin: 15px 0;"/>
                    <p class="text-muted" style="margin-bottom: 10px;"><small>Premiação por equipes</small></p>
                    <a href="{{url("/evento/premiacao_time/classificar/".$evento->id)}}" class="btn btn-app">
                        <i class="fa fa-sort"></i>
                        Classificar Times no Evento
                    </a>
                    <a href="{{url("/evento/".$evento->id."/team_awards/standings")}}" class="btn btn-app">
                        <i class="fa fa-list"></i>
                        Listar Premiações de Times
                    </a>
                @endif

                <a href="{{url("/evento/dashboard/".$evento->id)}}?tab=premiacao_equipe" class="btn btn-app">
                    <i class="fa fa-cog"></i>
                    Configurar Premiação por Equipes
                </a>
            </div>
        </div>
    </section>

    <section class="col-lg-6 connectedSortable">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bar-chart"></i> Rating (Swiss-Manager)</h3>
            </div>
            <div class="box-body">
                @if(false)
                    <a href="{{url("/evento/".$evento->id)}}/enxadristas/sm" class="btn btn-app" target="_blank">
                        <i class="fa fa-download"></i>
                        Baixar para Uso neste Evento (Swiss-Manager)
                    </a>
                @endif
                <a href="{{url("/evento/".$evento->id)}}/enxadristas/sm/inscritos" class="btn btn-app" target="_blank">
                    <i class="fa fa-download"></i>
                    Lista de rating dos inscritos
                </a>

                @if($evento->tipo_rating)
                    <hr style="margin: 15px 0;"/>
                    <div class="form-group">
                        <label class="d-flex align-items-center">
                            <div class="form-switch">
                                <input type="checkbox" id="toggle_rating" @if($evento->is_rating_calculate_enabled) checked @endif>
                                <span class="form-check-input"></span>
                            </div>
                            <span id="toggle_rating_status" class="switch-label">Permitir Cálculo do Rating Interno</span>
                        </label>
                    </div>
                    <div id="toggle_rating_status_container" @if(!$evento->is_rating_calculate_enabled) style="display:none;" @endif>
                        @if ($evento->consegueCalcularRating() == 0)
                            <button type="button" class="btn btn-app disabled" disabled>
                                <i class="fa fa-calculator"></i>
                                Calcular Rating (emparceiramento não importado)
                            </button>
                        @else
                            <a href="{{url("/evento/".$evento->id)}}/rating/calculate" class="btn btn-app">
                                <i class="fa fa-calculator"></i>
                                Calcular Rating
                            </a>
                        @endif
                    </div>
                @else
                    <p class="text-muted" style="margin-bottom: 0;"><small>Este evento não utiliza rating interno configurado.</small></p>
                @endif
            </div>
        </div>
    </section>
</div>
@endif

@if(
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
)
    @if($evento->evento_classificador_id > 0)
        <div class="row">
            <section class="col-lg-12 connectedSortable">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-level-up"></i> Evento classificador vinculado</h3>
                    </div>
                    <div class="box-body">
                        <p class="text-muted" style="margin-top: 0;">Importe inscritos aprovados do evento classificador ou remova todas as inscrições deste evento.</p>
                        <a href="{{url("/evento/".$evento->id."/gerenciamento/torneio_3/import")}}" class="btn btn-app">
                            <i class="fa fa-upload"></i>
                            Importar Inscrições do Evento Classificador
                        </a>
                        <a href="{{url("/evento/".$evento->id."/gerenciamento/torneio_3/removeAll")}}" class="btn btn-app">
                            <i class="fa fa-times"></i>
                            Remover todas as Inscrições do Evento
                        </a>
                    </div>
                </div>
            </section>
        </div>
    @elseif($evento->grupo_evento_classificador_id > 0)
        <div class="row">
            <section class="col-lg-12 connectedSortable">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-sitemap"></i> Grupo de evento classificador vinculado</h3>
                    </div>
                    <div class="box-body">
                        <p class="text-muted" style="margin-top: 0;">Importe inscritos do grupo classificador ou remova todas as inscrições deste evento.</p>
                        <a href="{{url("/evento/".$evento->id."/gerenciamento/import")}}" class="btn btn-app">
                            <i class="fa fa-upload"></i>
                            Importar Inscrições do Grupo Classificador
                        </a>
                        <a href="{{url("/evento/".$evento->id."/gerenciamento/removeAll")}}" class="btn btn-app">
                            <i class="fa fa-times"></i>
                            Remover todas as Inscrições do Evento
                        </a>
                    </div>
                </div>
            </section>
        </div>
    @endif
@endif

@if($evento->grupo_evento->hasConfig("is_pr_esporte",true))
<div class="row">
    <section class="col-lg-12 connectedSortable">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-institution"></i> Paraná Esporte</h3>
            </div>
            <div class="box-body">
                <a href="{{url("/evento/".$evento->id."/imports/ingadigital/file")}}" class="btn btn-app">
                    <i class="fa fa-file"></i>
                    Importar Arquivo
                </a>
                @if($evento->tipo_modalidade == 0)
                    <p class="text-muted" style="margin: 12px 0 8px;"><small>Fichas por equipes</small></p>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/team")}}" class="btn btn-app">
                        <i class="fa fa-download"></i>
                        Confirmação (equipes) — .xlsx
                    </a>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/team/pdf")}}" class="btn btn-app">
                        <i class="fa fa-download"></i>
                        Confirmação (equipes) — .pdf
                    </a>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/team")}}?fill_blanks" class="btn btn-app">
                        <i class="fa fa-chevron-circle-down"></i>
                        Preenchidas (equipes) — .xlsx
                    </a>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/team/pdf")}}?fill_blanks" class="btn btn-app">
                        <i class="fa fa-chevron-circle-down"></i>
                        Preenchidas (equipes) — .pdf
                    </a>
                @else
                    <p class="text-muted" style="margin: 12px 0 8px;"><small>Fichas individuais</small></p>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/single")}}" class="btn btn-app">
                        <i class="fa fa-download"></i>
                        Confirmação (individual) — .xlsx
                    </a>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/single/pdf")}}" class="btn btn-app">
                        <i class="fa fa-download"></i>
                        Confirmação (individual) — .pdf
                    </a>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/single")}}?fill_blanks" class="btn btn-app">
                        <i class="fa fa-chevron-circle-down"></i>
                        Preenchidas (individual) — .xlsx
                    </a>
                    <a href="{{url("/evento/".$evento->id."/exports/presporte/single/pdf")}}?fill_blanks" class="btn btn-app">
                        <i class="fa fa-chevron-circle-down"></i>
                        Preenchidas (individual) — .pdf
                    </a>
                @endif
            </div>
        </div>
    </section>
</div>
@endif

<div class="row">
    <section class="col-lg-6 connectedSortable">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-sliders"></i> Configurações gerais</h3>
            </div>
            <div class="box-body">
                <p class="text-muted" style="margin-top: 0;"><small>Comportamento do evento na plataforma (salva ao alterar).</small></p>
                <div class="form-group">
                    <label class="d-flex align-items-center">
                        <div class="form-switch">
                            <input type="checkbox" id="toggle_classificavel" @if($evento->is_classificavel) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span id="toggle_classificavel_status" class="switch-label">Permitir classificação geral deste evento</span>
                    </label>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="d-flex align-items-center">
                        <div class="form-switch">
                            <input type="checkbox" id="toggle_resultados_automaticos" @if(!$evento->e_resultados_manuais) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span id="toggle_resultados_automaticos_status" class="switch-label">Resultados Automáticos</span>
                    </label>
                </div>
            </div>
        </div>
    </section>

    <section class="col-lg-6 connectedSortable">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-file-text-o"></i> Relatórios</h3>
            </div>
            <div class="box-body">
                <a href="{{url("/evento/".$evento->id."/relatorios/premiados")}}" class="btn btn-app">
                    <i class="fa fa-file"></i>
                    Enxadristas Premiados neste Evento
                </a>
            </div>
        </div>
    </section>
</div>

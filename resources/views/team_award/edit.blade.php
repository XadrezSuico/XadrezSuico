@extends('adminlte::page')

@section('title', 'Premiação por equipes #' . $team_award->id)

@section('css')
<style>
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
    .switch-row {
        display: flex;
        align-items: center;
        margin-bottom: 12px;
        cursor: pointer;
        font-weight: normal;
    }
    .switch-label {
        margin-left: 10px;
        margin-bottom: 0;
        user-select: none;
    }
    .team-award-checklist li {
        margin-bottom: 6px;
    }
    .team-award-checklist .fa-check { color: #00a65a; }
    .team-award-checklist .fa-times { color: #dd4b39; }
    .team-award-checklist .fa-minus { color: #999; }
    .scoring-panel-disabled {
        opacity: 0.55;
        pointer-events: none;
    }
    .scoring-mode-hint {
        border-left: 3px solid #3c8dbc;
        padding-left: 12px;
        margin-bottom: 15px;
    }
</style>
@endsection

@section('content_header')
    <h1>
        Premiação por equipes: {{ $team_award->name }}
        <small>#{{ $team_award->id }} —
        @if($context === 'grupo')
            Grupo {{ $parent->name }}
        @else
            Evento {{ $parent->name }}
        @endif
        </small>
    </h1>
@stop

@section('content')
@php
    $categories_count = $team_award->categories->count();
    $scores_count = $team_award->scores->count();
    $tiebreaks_count = $team_award->tiebreaks->count();
    $has_scoring = $is_points_mode || $scores_count > 0;
    $ready = $categories_count > 0 && $has_scoring && $team_award->is_can_calculate;
    $active_tab = request()->query('tab', 'geral');
@endphp

<ul class="nav nav-pills" style="margin-bottom: 10px;">
    <li role="presentation"><a href="{{ $dashboard_url }}"><i class="fa fa-arrow-left"></i> Voltar à lista</a></li>
    @if($team_award->is_can_calculate)
        <li role="presentation"><a href="{{ $classificar_url }}" class="text-success"><i class="fa fa-sort"></i> Classificar equipes</a></li>
    @endif
    @if($team_award->is_public)
        <li role="presentation"><a href="{{ $public_standings_url }}" target="_blank"><i class="fa fa-external-link"></i> Ver página pública</a></li>
    @endif
</ul>

@if (session('status'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        {{ session('status') }}
    </div>
@endif

<div class="row">
    <div class="col-md-8">
        <div class="callout callout-{{ $ready ? 'success' : 'warning' }}">
            <h4><i class="fa fa-{{ $ready ? 'check-circle' : 'info-circle' }}"></i> Situação da configuração</h4>
            <ul class="list-unstyled team-award-checklist">
                <li>
                    @if($categories_count > 0)
                        <i class="fa fa-check"></i>
                    @else
                        <i class="fa fa-times"></i>
                    @endif
                    {{ $categories_count }} categoria(s) vinculada(s)
                </li>
                <li>
                    @if($is_points_mode)
                        <i class="fa fa-check"></i> Modo: somar pontos individuais dos enxadristas
                    @elseif($scores_count > 0)
                        <i class="fa fa-check"></i> Modo: tabela fixa com {{ $scores_count }} colocação(ões)
                    @else
                        <i class="fa fa-times"></i> Pontuação ainda não definida (tabela ou modo individual)
                    @endif
                </li>
                <li>
                    @if($tiebreaks_count > 0)
                        <i class="fa fa-check"></i>
                    @else
                        <i class="fa fa-minus"></i>
                    @endif
                    {{ $tiebreaks_count }} critério(s) de desempate (recomendado para empates)
                </li>
                <li>
                    @if($team_award->is_can_calculate)
                        <i class="fa fa-check"></i> Cálculo automático habilitado
                    @else
                        <i class="fa fa-times"></i> Cálculo automático desabilitado
                    @endif
                </li>
            </ul>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Tipo de pontuação</span>
                <span class="info-box-number" style="font-size: 16px;">
                    @if($is_points_mode)
                        Individual
                    @elseif($scores_count > 0)
                        Por colocação
                    @else
                        Não configurado
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>

<ul class="nav nav-tabs" role="tablist" id="team_award_tabs">
    <li role="presentation" class="{{ $active_tab === 'geral' ? 'active' : '' }}">
        <a href="#ta_geral" aria-controls="ta_geral" role="tab" data-toggle="tab">Geral</a>
    </li>
    <li role="presentation" class="{{ $active_tab === 'pontos' ? 'active' : '' }}">
        <a href="#ta_pontos" aria-controls="ta_pontos" role="tab" data-toggle="tab">Categorias e pontuação</a>
    </li>
    <li role="presentation" class="{{ $active_tab === 'desempate' ? 'active' : '' }}">
        <a href="#ta_desempate" aria-controls="ta_desempate" role="tab" data-toggle="tab">Desempate</a>
    </li>
</ul>

<div class="tab-content" style="padding-top: 15px;">

    <div role="tabpanel" class="tab-pane {{ $active_tab === 'geral' ? 'active' : '' }}" id="ta_geral">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Identificação e visibilidade</h3>
            </div>
            <form method="post" action="{{ $url_base }}">
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Nome da premiação</label>
                        <input name="name" id="name" class="form-control" type="text" value="{{ $team_award->name }}" required />
                    </div>

                    <label class="switch-row">
                        <div class="form-switch">
                            <input type="checkbox" name="is_public" id="is_public" @if($team_award->is_public) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span class="switch-label">Exibir resultados na página pública</span>
                    </label>
                    <label class="switch-row">
                        <div class="form-switch">
                            <input type="checkbox" name="is_can_calculate" id="is_can_calculate" @if($team_award->is_can_calculate) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span class="switch-label">Permitir cálculo e classificação das equipes</span>
                    </label>
                    <label class="switch-row">
                        <div class="form-switch">
                            <input type="checkbox" name="no_classificate" id="no_classificate" @if($team_award->hasConfig('no_classificate')) checked @endif>
                            <span class="form-check-input"></span>
                        </div>
                        <span class="switch-label">Excluir da classificação automática em lote (classificar manualmente)</span>
                    </label>

                    <hr/>
                    <h4 class="box-title">Modo de soma de pontos</h4>
                    <div class="scoring-mode-hint">
                        <label class="switch-row">
                            <div class="form-switch">
                                <input type="checkbox" name="is_points" id="is_points" @if($is_points_mode) checked @endif>
                                <span class="form-check-input"></span>
                            </div>
                            <span class="switch-label">Usar pontos da classificação individual de cada enxadrista</span>
                        </label>
                        <p class="help-block" style="margin-bottom: 0;">
                            <strong>Desmarcado:</strong> cada colocação (1º, 2º…) vale o que você definir na aba «Categorias e pontuação».<br/>
                            <strong>Marcado:</strong> soma os pontos já obtidos na classificação do torneio (com opção de valor fixo por categoria).
                        </p>
                    </div>

                    <h4 class="box-title">Limites por equipe</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="limit_places">Máx. colocados contados <em>por categoria</em></label>
                                <input name="limit_places" id="limit_places" class="form-control" type="number" min="1" placeholder="Sem limite"
                                    value="@if($team_award->hasConfig('limit_places')){{ $team_award->getConfig('limit_places', true) }}@endif" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="limit_total_places">Máx. colocados contados <em>no total</em></label>
                                <input name="limit_total_places" id="limit_total_places" class="form-control" type="number" min="1" placeholder="Sem limite"
                                    value="@if($team_award->hasConfig('limit_total_places')){{ $team_award->getConfig('limit_total_places', true) }}@endif" />
                            </div>
                        </div>
                    </div>
                    <p class="help-block">Use apenas um tipo de limite, salvo se o regulamento exigir combinação específica.</p>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane {{ $active_tab === 'pontos' ? 'active' : '' }}" id="ta_pontos">
        <div class="row">
            <section class="col-lg-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Categorias que entram na premiação</h3>
                        <div class="box-tools pull-right">
                            @if($available_categorias->count() > 0)
                                <a class="btn btn-default btn-xs" href="{{ $url_base }}/categoria/add_all"
                                   onclick="return confirm('Vincular todas as categorias do {{ $context === 'grupo' ? 'grupo' : 'grupo do evento' }}?');">
                                    <i class="fa fa-plus-square"></i> Vincular todas
                                </a>
                            @endif
                        </div>
                    </div>
                    @if($available_categorias->count() > 0)
                        <form method="post" action="{{ $url_base }}/categoria/add">
                            <div class="box-body">
                                <div class="form-group">
                                    <label for="categories_id">Adicionar categoria</label>
                                    <select name="categories_id" id="categories_id" class="form-control" required>
                                        <option value="">— Selecione —</option>
                                        @foreach($available_categorias as $categoria)
                                            <option value="{{ $categoria->id }}">{{ $categoria->name }} (#{{ $categoria->id }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success btn-sm">Adicionar</button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                        </form>
                    @endif
                    <div class="box-body no-padding">
                        <table class="table table-striped table-condensed">
                            <thead>
                                <tr><th>Categoria</th><th style="width: 50px;"></th></tr>
                            </thead>
                            <tbody>
                                @forelse($team_award->categories as $link)
                                    <tr>
                                        <td>{{ $link->category->name }}</td>
                                        <td>
                                            <a class="btn btn-danger btn-xs" href="{{ $url_base }}/categoria/remove/{{ $link->id }}"
                                               onclick="return confirm('Remover esta categoria da premiação?');"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted text-center">Nenhuma categoria. Vincule ao menos uma para calcular a premiação.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="col-lg-6">
                <div class="box box-primary" id="box_pontos_posicao">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pontos por colocação</h3>
                        @if($grupo_pontuacoes->count() > 0 && !$is_points_mode)
                            <div class="box-tools pull-right">
                                <a class="btn btn-info btn-xs" href="{{ $url_base }}/importar_pontuacao_grupo"
                                   onclick="return confirm('Substituir/atualizar a tabela com a pontuação geral do grupo de evento?');">
                                    <i class="fa fa-download"></i> Importar do grupo
                                </a>
                            </div>
                        @endif
                    </div>
                    <div id="pontos_posicao_inner" @if($is_points_mode) class="scoring-panel-disabled" @endif>
                        <form method="post" action="{{ $url_base }}/pontuacao/add">
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-xs-5">
                                        <div class="form-group">
                                            <label for="place">Colocação</label>
                                            <input name="place" id="place" class="form-control" type="number" min="1" required />
                                        </div>
                                    </div>
                                    <div class="col-xs-5">
                                        <div class="form-group">
                                            <label for="score">Pontos</label>
                                            <input name="score" id="score" class="form-control" type="number" required />
                                        </div>
                                    </div>
                                    <div class="col-xs-2">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-success btn-block" title="Salvar"><i class="fa fa-plus"></i></button>
                                    </div>
                                </div>
                                <p class="help-block">
                                    Atalhos:
                                    <button type="button" class="btn btn-default btn-xs btn-preset" data-place="1" data-score="10">1º→10</button>
                                    <button type="button" class="btn btn-default btn-xs btn-preset" data-place="2" data-score="7">2º→7</button>
                                    <button type="button" class="btn btn-default btn-xs btn-preset" data-place="3" data-score="5">3º→5</button>
                                </p>
                            </div>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </form>
                        <div class="box-body no-padding border-top">
                            <table class="table table-striped table-condensed">
                                <thead>
                                    <tr><th>Col.</th><th>Pontos</th><th></th></tr>
                                </thead>
                                <tbody>
                                    @forelse($team_award->scores->sortBy('place') as $row)
                                        <tr>
                                            <td>{{ $row->place }}º</td>
                                            <td><strong>{{ $row->score }}</strong></td>
                                            <td>
                                                <a class="btn btn-danger btn-xs" href="{{ $url_base }}/pontuacao/remove/{{ $row->id }}"><i class="fa fa-times"></i></a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted text-center">Tabela vazia</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($is_points_mode)
                        <div class="box-footer text-muted">
                            <i class="fa fa-info-circle"></i> Desative «pontos individuais» na aba Geral para editar esta tabela.
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <div class="row" id="box_pontos_categoria" @if(!$is_points_mode) style="display:none;" @endif>
            <section class="col-lg-12">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Pontuação fixa por categoria (opcional)</h3>
                    </div>
                    <form method="post" action="{{ $url_base }}/pontuacao_categoria">
                        <div class="box-body">
                            <p class="help-block">Quando preenchido, ignora os pontos da classificação individual e usa este valor para todos os enxadristas da categoria na equipe.</p>
                            @if($team_award->categories->count() === 0)
                                <p class="text-muted">Vincule categorias acima para configurar valores fixos.</p>
                            @else
                                <table class="table table-condensed">
                                    <thead><tr><th>Categoria</th><th style="max-width: 200px;">Pontos fixos</th></tr></thead>
                                    <tbody>
                                        @foreach($team_award->categories as $link)
                                            @php($key = 'category_'.$link->category->id.'_default_points')
                                            <tr>
                                                <td>{{ $link->category->name }}</td>
                                                <td>
                                                    <input class="form-control input-sm" type="number" placeholder="Usar pontos da inscrição"
                                                        name="category_points[{{ $link->category->id }}]"
                                                        value="@if($team_award->hasConfig($key)){{ $team_award->getConfig($key, true) }}@endif" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                        @if($team_award->categories->count() > 0)
                            <div class="box-footer">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Salvar pontos por categoria</button>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            </div>
                        @endif
                    </form>
                </div>
            </section>
        </div>
    </div>

    <div role="tabpanel" class="tab-pane {{ $active_tab === 'desempate' ? 'active' : '' }}" id="ta_desempate">
        <div class="row">
            <section class="col-lg-5">
                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">Adicionar critério</h3></div>
                    <form method="post" action="{{ $url_base }}/desempate/add">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="tiebreaks_id">Critério de desempate</label>
                                <select name="tiebreaks_id" id="tiebreaks_id" class="form-control" required>
                                    <option value="">— Selecione —</option>
                                    @foreach($criterios_team_award as $criterio)
                                        <option value="{{ $criterio->id }}">{{ $criterio->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="priority">Prioridade (1 = primeiro desempate)</label>
                                <input name="priority" id="priority" class="form-control" type="number" min="1" value="{{ $next_tiebreak_priority }}" required />
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success">Adicionar</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </section>
            <section class="col-lg-7">
                <div class="box box-primary">
                    <div class="box-header with-border"><h3 class="box-title">Ordem aplicada na classificação</h3></div>
                    <div class="box-body no-padding">
                        <table class="table table-striped">
                            <thead>
                                <tr><th style="width: 80px;">Ordem</th><th>Critério</th><th style="width: 50px;"></th></tr>
                            </thead>
                            <tbody>
                                @forelse($team_award->tiebreaks->sortBy('priority') as $row)
                                    <tr>
                                        <td><span class="badge bg-blue">{{ $row->priority }}</span></td>
                                        <td>{{ $row->tiebreak->name }}</td>
                                        <td>
                                            <a class="btn btn-danger btn-xs" href="{{ $url_base }}/desempate/remove/{{ $row->id }}"><i class="fa fa-times"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted text-center">
                                            Nenhum critério. Recomendado: 1ºs lugares, 2ºs lugares, 3ºs lugares (TA1, TA2, TA3).
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    function syncScoringPanels() {
        var individual = $("#is_points").is(":checked");
        if (individual) {
            $("#box_pontos_categoria").slideDown(150);
            $("#pontos_posicao_inner").addClass("scoring-panel-disabled");
        } else {
            $("#box_pontos_categoria").slideUp(150);
            $("#pontos_posicao_inner").removeClass("scoring-panel-disabled");
        }
    }

    $("#is_points").on("change", syncScoringPanels);
    syncScoringPanels();

    $(".btn-preset").on("click", function () {
        $("#place").val($(this).data("place"));
        $("#score").val($(this).data("score"));
    });

    @if($active_tab && $active_tab !== 'geral')
        $("#team_award_tabs a[href='#ta_{{ $active_tab }}']").tab("show");
    @endif
</script>
@endsection

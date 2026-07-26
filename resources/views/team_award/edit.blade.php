@extends('adminlte::page')

@section('title', 'Premiação por equipes #' . $team_award->id)

@section('content_header')
    <h1>
        Premiação por equipes: {{ $team_award->name }}
        @if($context === 'grupo')
            <small>Grupo #{{ $parent->id }} — {{ $parent->name }}</small>
        @else
            <small>Evento #{{ $parent->id }} — {{ $parent->name }}</small>
        @endif
    </h1>
@stop

@section('content')
<ul class="nav nav-pills">
    <li role="presentation"><a href="{{ $dashboard_url }}">Voltar ao painel</a></li>
</ul>
<br/>

<div class="row">
    <section class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Dados gerais</h3></div>
            <form method="post" action="{{ $url_base }}">
                <div class="box-body">
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input name="name" id="name" class="form-control" type="text" value="{{ $team_award->name }}" required />
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_public" @if($team_award->is_public) checked @endif> Exibir publicamente</label>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_can_calculate" @if($team_award->is_can_calculate) checked @endif> Permitir cálculo/classificação automática</label>
                    </div>
                    <div class="form-group">
                        <label><input type="checkbox" name="no_classificate" id="no_classificate" @if($team_award->hasConfig('no_classificate')) checked @endif> Não incluir na classificação automática em lote</label>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label><input type="checkbox" name="is_points" id="is_points" @if($team_award->hasConfig('is_points') && $team_award->getConfig('is_points', true)) checked @endif> Usar pontos da colocação individual (em vez de tabela fixa por posição)</label>
                        <p class="help-block">Marcado: soma os pontos de classificação individual de cada enxadrista (ou pontuação fixa por categoria, abaixo). Desmarcado: usa a tabela de pontos por colocação desta premiação.</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="limit_places">Limite de colocados por categoria (por equipe)</label>
                                <input name="limit_places" id="limit_places" class="form-control" type="number" min="0"
                                    value="@if($team_award->hasConfig('limit_places')){{ $team_award->getConfig('limit_places', true) }}@endif" />
                                <p class="help-block">Deixe vazio para não limitar por categoria.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="limit_total_places">Limite total de colocados (por equipe)</label>
                                <input name="limit_total_places" id="limit_total_places" class="form-control" type="number" min="0"
                                    value="@if($team_award->hasConfig('limit_total_places')){{ $team_award->getConfig('limit_total_places', true) }}@endif" />
                                <p class="help-block">Deixe vazio para não limitar o total. Não use junto com limite por categoria, salvo se souber o efeito desejado.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">Salvar dados gerais</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
        </div>
    </section>
</div>

<div class="row">
    <section class="col-lg-6 connectedSortable">
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Categorias participantes</h3></div>
            <form method="post" action="{{ $url_base }}/categoria/add">
                <div class="box-body">
                    <div class="form-group">
                        <label for="categories_id">Categoria</label>
                        <select name="categories_id" id="categories_id" class="form-control" required>
                            <option value="">--- Selecione ---</option>
                            @foreach($categorias as $categoria)
                                @if($team_award->categories()->where([['categories_id', '=', $categoria->id]])->count() == 0)
                                    <option value="{{ $categoria->id }}">#{{ $categoria->id }} — {{ $categoria->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">Adicionar categoria</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
            <div class="box-body">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr><th>Categoria</th><th width="15%"></th></tr>
                    </thead>
                    <tbody>
                        @forelse($team_award->categories as $link)
                            <tr>
                                <td>#{{ $link->category->id }} — {{ $link->category->name }}</td>
                                <td>
                                    <a class="btn btn-danger btn-sm" href="{{ $url_base }}/categoria/remove/{{ $link->id }}"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="text-muted">Nenhuma categoria vinculada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="col-lg-6 connectedSortable" id="box_pontos_posicao" @if($team_award->hasConfig('is_points') && $team_award->getConfig('is_points', true)) style="opacity: 0.5" @endif>
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Pontos por colocação</h3></div>
            <form method="post" action="{{ $url_base }}/pontuacao/add">
                <div class="box-body">
                    <div class="form-group">
                        <label for="place">Colocação</label>
                        <input name="place" id="place" class="form-control" type="number" min="1" required />
                    </div>
                    <div class="form-group">
                        <label for="score">Pontos</label>
                        <input name="score" id="score" class="form-control" type="number" required />
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">Adicionar / atualizar</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
            <div class="box-body">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr><th>Colocação</th><th>Pontos</th><th width="15%"></th></tr>
                    </thead>
                    <tbody>
                        @forelse($team_award->scores()->orderBy('place', 'ASC')->get() as $row)
                            <tr>
                                <td>{{ $row->place }}º</td>
                                <td>{{ $row->score }}</td>
                                <td>
                                    <a class="btn btn-danger btn-sm" href="{{ $url_base }}/pontuacao/remove/{{ $row->id }}"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">Nenhuma colocação configurada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="row" id="box_pontos_categoria" @if(!$team_award->hasConfig('is_points') || !$team_award->getConfig('is_points', true)) style="display:none;" @endif>
    <section class="col-lg-12">
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Pontuação fixa por categoria (modo pontos individuais)</h3></div>
            <form method="post" action="{{ $url_base }}/pontuacao_categoria">
                <div class="box-body">
                    <p class="help-block">Se preenchido, substitui os pontos da classificação individual para enxadristas dessa categoria. Deixe vazio para usar os pontos da inscrição.</p>
                    <table class="table table-condensed">
                        <thead>
                            <tr><th>Categoria</th><th>Pontos fixos</th></tr>
                        </thead>
                        <tbody>
                            @foreach($team_award->categories as $link)
                                @php($key = 'category_'.$link->category->id.'_default_points')
                                <tr>
                                    <td>{{ $link->category->name }}</td>
                                    <td>
                                        <input class="form-control" type="number" name="category_points[{{ $link->category->id }}]"
                                            value="@if($team_award->hasConfig($key)){{ $team_award->getConfig($key, true) }}@endif" />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">Salvar pontuação por categoria</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
        </div>
    </section>
</div>

<div class="row">
    <section class="col-lg-6 connectedSortable">
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Critérios de desempate entre equipes</h3></div>
            <form method="post" action="{{ $url_base }}/desempate/add">
                <div class="box-body">
                    <div class="form-group">
                        <label for="tiebreaks_id">Critério</label>
                        <select name="tiebreaks_id" id="tiebreaks_id" class="form-control" required>
                            <option value="">--- Selecione ---</option>
                            @foreach($criterios_team_award as $criterio)
                                <option value="{{ $criterio->id }}">#{{ $criterio->id }} — {{ $criterio->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="priority">Prioridade</label>
                        <input name="priority" id="priority" class="form-control" type="number" value="1" required />
                    </div>
                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-success">Adicionar critério</button>
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                </div>
            </form>
        </div>
    </section>
    <section class="col-lg-6 connectedSortable">
        <div class="box box-primary">
            <div class="box-header"><h3 class="box-title">Ordem de desempate</h3></div>
            <div class="box-body">
                <table class="table table-condensed table-striped">
                    <thead>
                        <tr><th>Prior.</th><th>Critério</th><th width="15%"></th></tr>
                    </thead>
                    <tbody>
                        @forelse($team_award->tiebreaks()->orderBy('priority', 'ASC')->get() as $row)
                            <tr>
                                <td>{{ $row->priority }}</td>
                                <td>{{ $row->tiebreak->name }}</td>
                                <td>
                                    <a class="btn btn-danger btn-sm" href="{{ $url_base }}/desempate/remove/{{ $row->id }}"><i class="fa fa-times"></i></a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">Nenhum critério configurado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script>
    $("#is_points").on("change", function(){
        if($(this).is(":checked")){
            $("#box_pontos_categoria").show();
            $("#box_pontos_posicao").css("opacity", "0.5");
        }else{
            $("#box_pontos_categoria").hide();
            $("#box_pontos_posicao").css("opacity", "1");
        }
    });
</script>
@endsection

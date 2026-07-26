@php
    $can_edit = isset($can_edit_team_awards) ? $can_edit_team_awards : true;
    $add_url = $team_award_add_url;
    $awards = $team_awards;
@endphp
<br/>
<p class="text-muted">
    Configure premiações por clube/equipe com base nas colocações individuais ou em uma tabela de pontos.
    Depois de configurar, use <strong>Classificar Premiação por Times</strong> no menu do {{ isset($context_label) ? $context_label : 'evento/grupo' }}.
</p>
<div class="row">
    @if($can_edit)
        <section class="col-lg-4 connectedSortable">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Nova premiação</h3>
                </div>
                <form method="post" action="{{ $add_url }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="team_award_name">Nome</label>
                            <input name="name" id="team_award_name" class="form-control" type="text" placeholder="Ex.: Equipes absoluto" required />
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="is_public" checked> Página pública</label>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="is_can_calculate" checked> Permitir cálculo</label>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Criar e configurar</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </section>
    @endif
    <section class="col-lg-{{ $can_edit ? '8' : '12' }} connectedSortable">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Premiações cadastradas</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                @if($awards->count() === 0)
                    <p class="text-muted" style="padding: 15px;">Nenhuma premiação cadastrada.</p>
                @else
                    <table class="table table-hover table-striped" style="width: 100%; margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Modo</th>
                                <th>Cat.</th>
                                <th>Status</th>
                                <th class="text-right">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($awards as $award)
                                @php
                                    $mode_individual = $award->hasConfig('is_points') && $award->getConfig('is_points', true);
                                    $cat_count = $award->categories()->count();
                                    $score_ok = $mode_individual || $award->scores()->count() > 0;
                                    $ok = $cat_count > 0 && $score_ok && $award->is_can_calculate;
                                @endphp
                                <tr>
                                    <td>{{ $award->id }}</td>
                                    <td>
                                        <strong>{{ $award->name }}</strong>
                                        @if(!$award->is_public)<br><small class="text-muted">Não público</small>@endif
                                    </td>
                                    <td>
                                        @if($mode_individual)
                                            <span class="label label-info">Pontos individuais</span>
                                        @elseif($award->scores()->count() > 0)
                                            <span class="label label-primary">Por colocação</span>
                                        @else
                                            <span class="label label-default">Incompleto</span>
                                        @endif
                                    </td>
                                    <td>{{ $cat_count }}</td>
                                    <td>
                                        @if($ok)
                                            <span class="label label-success">Pronta</span>
                                        @else
                                            <span class="label label-warning">Configurar</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($can_edit)
                                            <a class="btn btn-primary btn-sm" href="{{ $team_award_edit_url_prefix }}/{{ $award->id }}" title="Configurar">
                                                <i class="fa fa-cog"></i> Configurar
                                            </a>
                                            <a class="btn btn-danger btn-sm" href="{{ $team_award_remove_url_prefix }}/{{ $award->id }}"
                                               onclick="return confirm('Remover esta premiação? Os resultados calculados serão apagados.');" title="Remover">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </section>
</div>

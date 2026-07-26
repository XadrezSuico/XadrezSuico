@php
    $can_edit = isset($can_edit_team_awards) ? $can_edit_team_awards : true;
    $add_url = $team_award_add_url;
    $awards = $team_awards;
@endphp
<br/>
<div class="row">
    @if($can_edit)
        <section class="col-lg-5 connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Nova premiação por equipes</h3>
                </div>
                <form method="post" action="{{ $add_url }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="team_award_name">Nome</label>
                            <input name="name" id="team_award_name" class="form-control" type="text" required />
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_public" checked> Exibir publicamente</label>
                        </div>
                        <div class="form-group">
                            <label><input type="checkbox" name="is_can_calculate" checked> Permitir cálculo/classificação</label>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success">Criar e configurar</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </section>
    @endif
    <section class="col-lg-{{ $can_edit ? '7' : '12' }} connectedSortable">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Premiações por equipes</h3>
            </div>
            <div class="box-body">
                @if($awards->count() === 0)
                    <p class="text-muted">Nenhuma premiação cadastrada.</p>
                @else
                    <table class="table table-condensed table-striped" style="width: 100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nome</th>
                                <th>Público</th>
                                <th>Cálculo</th>
                                <th>Categorias</th>
                                <th width="25%">Opções</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($awards as $award)
                                <tr>
                                    <td>{{ $award->id }}</td>
                                    <td>{{ $award->name }}</td>
                                    <td>@if($award->is_public) Sim @else Não @endif</td>
                                    <td>@if($award->is_can_calculate) Sim @else Não @endif</td>
                                    <td>{{ $award->categories()->count() }}</td>
                                    <td>
                                        @if($can_edit)
                                            <a class="btn btn-primary btn-sm" href="{{ $team_award_edit_url_prefix }}/{{ $award->id }}" title="Configurar"><i class="fa fa-cog"></i></a>
                                            <a class="btn btn-danger btn-sm" href="{{ $team_award_remove_url_prefix }}/{{ $award->id }}" onclick="return confirm('Remover esta premiação? Os resultados calculados serão apagados.');" title="Remover"><i class="fa fa-times"></i></a>
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

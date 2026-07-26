@php
    $can_edit = isset($can_edit_timeline) ? $can_edit_timeline : true;
    $add_url = $timeline_add_url;
    $items = $timeline_items;
    $edit_url_prefix = $timeline_edit_url_prefix;
    $remove_url_prefix = $timeline_remove_url_prefix;
    $next_order = $timeline_next_order ?? 1;
@endphp
<br/>
<p class="text-muted">
    A timeline exibida no aplicativo inclui automaticamente o início e o fim das inscrições online quando configurados em
    <strong>Editar Evento</strong>. Use esta aba para cadastrar marcos adicionais (abertura do torneio, premiação, etc.).
</p>

@if($evento->hasConfig('date_start_registration', true) || $evento->data_limite_inscricoes_abertas)
    <div class="box box-default">
        <div class="box-header with-border">
            <h3 class="box-title">Itens automáticos (somente leitura)</h3>
        </div>
        <div class="box-body">
            <ul class="list-unstyled" style="margin-bottom: 0;">
                @if($evento->hasConfig('date_start_registration', true))
                    <li>
                        <span class="label label-default">Confirmado</span>
                        {{ $evento->getDataInicioInscricoesOnline() }} — Início das Inscrições Online
                    </li>
                @endif
                @if($evento->data_limite_inscricoes_abertas)
                    <li>
                        <span class="label label-default">Confirmado</span>
                        {{ $evento->getDataFimInscricoesOnline() }} — Fim das Inscrições Online
                    </li>
                @endif
            </ul>
        </div>
    </div>
@endif

@if(session('status'))
    <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        {{ session('status') }}
    </div>
@endif

<div class="row">
    @if($can_edit)
        <section class="col-lg-4 connectedSortable">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title">Novo item</h3>
                </div>
                <form method="post" action="{{ $add_url }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="timeline_title">Título</label>
                            <input name="title" id="timeline_title" class="form-control" type="text" placeholder="Ex.: Cerimônia de abertura" required />
                        </div>
                        <div class="form-group">
                            <label for="timeline_datetime">Data e hora</label>
                            <input name="datetime" id="timeline_datetime" class="form-control timeline-datetime" type="text" placeholder="dd/mm/aaaa hh:mm" required />
                        </div>
                        <div class="form-group">
                            <label for="timeline_order">Ordem</label>
                            <input name="order" id="timeline_order" class="form-control" type="number" min="1" value="{{ $next_order }}" />
                            <p class="help-block">Define a posição na timeline do app (menor = antes).</p>
                        </div>
                        <div class="checkbox">
                            <label><input type="checkbox" name="is_expected" value="1"> Marcar como previsão</label>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Adicionar</button>
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    </div>
                </form>
            </div>
        </section>
    @endif
    <section class="col-lg-{{ $can_edit ? '8' : '12' }} connectedSortable">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Itens cadastrados</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                @if($items->count() === 0)
                    <p class="text-muted" style="padding: 15px;">Nenhum item adicional cadastrado.</p>
                @else
                    <table class="table table-hover table-striped" style="width: 100%; margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Ordem</th>
                                <th>Título</th>
                                <th>Data e hora</th>
                                <th>Status</th>
                                @if($can_edit)
                                    <th class="text-right">Ações</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $timeline_item)
                                <tr>
                                    <td>{{ $timeline_item->order }}</td>
                                    <td><strong>{{ $timeline_item->title }}</strong></td>
                                    <td>{{ $timeline_item->getDateTime() }}</td>
                                    <td>
                                        @if($timeline_item->is_expected)
                                            <span class="label label-warning">Previsão</span>
                                        @else
                                            <span class="label label-success">Confirmado</span>
                                        @endif
                                    </td>
                                    @if($can_edit)
                                        <td class="text-right">
                                            <a class="btn btn-primary btn-sm" href="{{ $edit_url_prefix }}/{{ $timeline_item->id }}" title="Editar">
                                                <i class="fa fa-pencil"></i> Editar
                                            </a>
                                            <a class="btn btn-danger btn-sm" href="{{ $remove_url_prefix }}/{{ $timeline_item->id }}"
                                               onclick="return confirm('Remover este item da timeline?');" title="Remover">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </section>
</div>

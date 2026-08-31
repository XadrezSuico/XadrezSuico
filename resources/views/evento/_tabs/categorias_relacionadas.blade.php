@php
    $can_edit_relacao = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );

    $show_pag_vinculo = (
        env('XADREZSUICOPAG_URI', null) &&
        env('XADREZSUICOPAG_SYSTEM_ID', null) &&
        env('XADREZSUICOPAG_SYSTEM_TOKEN', null) &&
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobalbyPerfil([1, 10, 11]) &&
        $evento->xadrezsuicopag_uuid != ''
    );

    $pag_categories = collect();
    if ($show_pag_vinculo && $xadrezsuicopag_controller) {
        $xadrezsuicopag_category_request = $xadrezsuicopag_controller->factory('categories')->list($evento->xadrezsuicopag_uuid);
        if ($xadrezsuicopag_category_request->ok == 1) {
            $pag_categories = collect($xadrezsuicopag_category_request->categories);
        }
    }
@endphp

<div class="v2-categorias-relacionadas-tab">
    <div class="row v2-categorias-relacionadas-panels">
        @if($can_edit_relacao)
            <section class="col-lg-6 connectedSortable">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Nova Relação de Categoria</h3>
                    </div>
                    <form method="post" action="{{ url('/evento/' . $evento->id . '/categoria/add') }}">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="categoria_id">Categoria</label>
                                <select name="categoria_id" id="categoria_id" class="form-control width-100">
                                    <option value="">--- Selecione ---</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->id }} - {{ $categoria->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            @if($show_pag_vinculo && $pag_categories->isNotEmpty())
                                <div class="form-group">
                                    <label for="category_xadrezsuicopag_uuid">PAG: Categoria</label>
                                    <select name="xadrezsuicopag_uuid" id="category_xadrezsuicopag_uuid" class="form-control width-100">
                                        <option value="">--- Sem Categoria no PAG ---</option>
                                        @foreach($pag_categories as $xadrezsuicopag_category)
                                            <option value="{{ $xadrezsuicopag_category->uuid }}">{{ $xadrezsuicopag_category->uuid }} - {{ $xadrezsuicopag_category->name }}</option>
                                        @endforeach
                                    </select>
                                    <small><strong>IMPORTANTE!</strong> Apenas selecione uma categoria do PAG caso esta necessite pagamento.</small>
                                </div>
                            @endif
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success">Enviar</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </section>
        @endif

        <section class="col-lg-{{ $can_edit_relacao ? '6' : '12' }} connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Categorias Relacionadas</h3>
                </div>
                <div class="box-body">
                    @if($evento->categorias->isEmpty())
                        <p class="text-muted v2-categorias-relacionadas-empty">Nenhuma categoria relacionada.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-condensed table-striped v2-categorias-relacionadas-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Vínculo Principal</th>
                                        @if($show_pag_vinculo)
                                            <th>Vínculo PAG</th>
                                        @endif
                                        <th class="text-right">Opções</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evento->categorias->all() as $categoria)
                                        <tr>
                                            <td>{{ $categoria->categoria->id }}</td>
                                            <td>{{ $categoria->categoria->name }}</td>
                                            <td>
                                                @if($categoria->categoria->grupo_evento_id)
                                                    Grupo de Evento: #{{ $categoria->categoria->grupo_evento->id }} - {{ $categoria->categoria->grupo_evento->name }}
                                                @elseif($categoria->categoria->evento_id)
                                                    Evento: #{{ $categoria->categoria->evento->id }} - {{ $categoria->categoria->evento->name }}
                                                @else
                                                    Estou Confuso. Não há vínculo.
                                                @endif
                                            </td>
                                            @if($show_pag_vinculo)
                                                <td class="v2-categorias-relacionadas-pag">
                                                    @if($categoria->xadrezsuicopag_uuid)
                                                        @php($xadrezsuicopag_category_request = $xadrezsuicopag_controller->factory('category')->get($evento->xadrezsuicopag_uuid, $categoria->xadrezsuicopag_uuid))
                                                        @if($xadrezsuicopag_category_request->ok == 1)
                                                            {{ $xadrezsuicopag_category_request->category->uuid }} -
                                                            {{ $xadrezsuicopag_category_request->category->name }}
                                                        @else
                                                            Há um registro cadastrado, mas não existe uma categoria com este registro cadastrada no PAG.
                                                        @endif
                                                    @else
                                                        -- Não há --
                                                    @endif
                                                </td>
                                            @endif
                                            <td class="text-right">
                                                <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/categoria/edit/' . $categoria->id) }}" role="button" title="Editar">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                @if($evento->torneios()->whereHas('categorias', function ($q) use ($categoria) { $q->where([['categoria_id', '=', $categoria->categoria_id]]); })->count() == 0)
                                                    <a class="btn btn-warning btn-sm" href="{{ url('/evento/' . $evento->id . '/categoria/createTournament/' . $categoria->id) }}" role="button" title="Criar torneio">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                @endif
                                                @if($can_edit_relacao)
                                                    <a class="btn btn-danger btn-sm" href="{{ url('/evento/' . $evento->id . '/categoria/remove/' . $categoria->id) }}" role="button" title="Remover">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

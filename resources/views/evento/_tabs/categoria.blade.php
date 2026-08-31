@php
    $can_edit_categoria = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );
@endphp

<div class="v2-categoria-tab">
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <strong>Alerta!</strong><br/>
                Esta aba se destina apenas ao <strong>caso de necessitar de uma categoria específica para este evento</strong>. As categorias aqui cadastradas <strong>não serão replicadas</strong> a qualquer outro Evento ou então Grupo de Evento.<br/>
                Obs: O cadastro da categoria aqui <strong>não retira a necessidade de efetuar a relação</strong> da mesma na aba "Categorias Relacionadas".
            </div>
        </div>
    </div>

    <div class="row v2-categoria-panels">
        @if($can_edit_categoria)
            <section class="col-lg-6 connectedSortable">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Nova Categoria</h3>
                    </div>
                    <form method="post" action="{{ url('/evento/' . $evento->id . '/categorias/new') }}">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="name">Nome</label>
                                <input name="name" id="name" class="form-control" type="text" />
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="idade_minima">Idade Mínima (Em anos)</label>
                                        <input name="idade_minima" id="idade_minima" class="form-control" type="number" step="1" min="0" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="idade_maxima">Idade Máxima (Em anos)</label>
                                        <input name="idade_maxima" id="idade_maxima" class="form-control" type="number" step="1" min="0" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="cat_code">Código Categoria (Padrão Swiss-Manager)</label>
                                        <input name="cat_code" id="cat_code" class="form-control" type="text" maxlength="10" />
                                        <small>Exemplo: Para Sub-08, utilizar <strong>U08</strong>.</small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="code">Código Grupo</label>
                                        <input name="code" id="code" class="form-control" type="text" maxlength="10" />
                                        <small>Deve ser único em cada evento.</small>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted v2-categoria-form__hint">
                                O código de grupo é utilizado na identificação da categoria no processamento do resultado. Preencha-o também no Swiss-Manager e mantenha-o único para cada categoria.
                            </p>
                            <div class="form-group">
                                <label><input type="checkbox" id="nao_classificar" name="nao_classificar"> Não Classificar Categoria</label>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-success">Enviar</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </section>
        @endif

        <section class="col-lg-{{ $can_edit_categoria ? '6' : '12' }} connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Categorias</h3>
                </div>
                <div class="box-body">
                    @if($evento->categorias_cadastradas->isEmpty())
                        <p class="text-muted v2-categoria-empty">Nenhuma categoria cadastrada.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-condensed table-striped v2-categoria-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Nome</th>
                                        <th>Classificar?</th>
                                        @if($can_edit_categoria)
                                            <th class="text-right">Opções</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($evento->categorias_cadastradas->all() as $categoria)
                                        <tr>
                                            <td>{{ $categoria->id }}</td>
                                            <td>{{ $categoria->name }}</td>
                                            <td>@if(!$categoria->nao_classificar) Sim @else Não @endif</td>
                                            @if($can_edit_categoria)
                                                <td class="text-right">
                                                    <a class="btn btn-success btn-sm" href="{{ url('/evento/' . $evento->id . '/categorias/dashboard/' . $categoria->id) }}" role="button" title="Gerenciar">
                                                        <i class="fa fa-dashboard"></i>
                                                    </a>
                                                    @if($categoria->isDeletavel())
                                                        <a class="btn btn-danger btn-sm" href="{{ url('/evento/' . $evento->id . '/categorias/delete/' . $categoria->id) }}" role="button" title="Remover">
                                                            <i class="fa fa-times"></i>
                                                        </a>
                                                    @endif
                                                </td>
                                            @endif
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

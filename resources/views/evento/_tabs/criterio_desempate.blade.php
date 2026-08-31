@php
    $can_edit_criterio = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );

    $criterios_agrupados = $evento->criterios()
        ->with(['criterio', 'tipo_torneio', 'software'])
        ->orderBy('tipo_torneio_id', 'ASC')
        ->orderBy('softwares_id', 'ASC')
        ->orderBy('prioridade', 'ASC')
        ->get()
        ->groupBy(function ($criterio) {
            return $criterio->tipo_torneio_id . '-' . $criterio->softwares_id;
        });
@endphp

<div class="v2-criterio-desempate-tab">
    <div class="row">
        <div class="col-lg-12">
            <div class="alert alert-warning alert-dismissible" role="alert">
                <strong>Alerta!</strong><br/>
                Lembre-se que, o <b>Grupo de Evento</b> poderá possuir critérios de desempate também.<br/>
                Caso você escolha um ou mais critérios nesta tela, os critérios de desempate do Grupo de Evento <strong>serão desconsiderados!</strong>
            </div>
        </div>
    </div>

    <div class="row v2-criterio-desempate-panels">
        @if($can_edit_criterio)
            <section class="col-lg-6 connectedSortable">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Relacionar Critério de Desempate</h3>
                    </div>
                    <form method="post" action="{{ url('/evento/' . $evento->id . '/criteriodesempate/add') }}">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="criterio_desempate_id">Critério de Desempate</label>
                                <select name="criterio_desempate_id" id="criterio_desempate_id" class="form-control width-100">
                                    <option value="">--- Selecione ---</option>
                                    @foreach($criterios_desempate as $criterio_desempate)
                                        <option value="{{ $criterio_desempate->id }}">{{ $criterio_desempate->id }} - {{ $criterio_desempate->name }} @if($criterio_desempate->sm_code) [{{ $criterio_desempate->sm_code }}] @endif</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tipo_torneio_id">Tipo de Torneio</label>
                                <select name="tipo_torneio_id" id="tipo_torneio_id" class="form-control width-100">
                                    <option value="">--- Selecione ---</option>
                                    @foreach($tipos_torneio as $tipo_torneio)
                                        <option value="{{ $tipo_torneio->id }}">{{ $tipo_torneio->id }} - {{ $tipo_torneio->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="softwares_id">Software</label>
                                <select name="softwares_id" id="softwares_id" class="form-control width-100">
                                    <option value="">--- Selecione ---</option>
                                    @foreach($softwares as $software)
                                        <option value="{{ $software->id }}">{{ $software->id }} - {{ $software->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="prioridade">Prioridade</label>
                                <input name="prioridade" id="prioridade" class="form-control" type="number" />
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

        <section class="col-lg-{{ $can_edit_criterio ? '6' : '12' }} connectedSortable">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Critérios de Desempate</h3>
                </div>
                <div class="box-body">
                    @if($criterios_agrupados->isEmpty())
                        <p class="text-muted v2-criterio-empty">Nenhum critério de desempate cadastrado.</p>
                    @else
                        @foreach($criterios_agrupados as $grupo)
                            @php $grupo_ref = $grupo->first(); @endphp
                            <div class="v2-criterio-group">
                                <h4 class="v2-criterio-group__title">
                                    {{ $grupo_ref->tipo_torneio->name }}
                                    <span class="text-muted">·</span>
                                    {{ $grupo_ref->software->name }}
                                </h4>
                                <div class="table-responsive">
                                    <table class="table table-condensed table-striped v2-criterio-group__table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nome</th>
                                                <th>Prior.</th>
                                                @if($can_edit_criterio)
                                                    <th class="text-right">Opções</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($grupo as $criterio_desempate)
                                                <tr>
                                                    <td>{{ $criterio_desempate->criterio->id }}</td>
                                                    <td>{{ $criterio_desempate->criterio->name }}</td>
                                                    <td>{{ $criterio_desempate->prioridade }}</td>
                                                    @if($can_edit_criterio)
                                                        <td class="text-right">
                                                            <a class="btn btn-danger btn-sm" href="{{ url('/evento/' . $evento->id . '/criteriodesempate/remove/' . $criterio_desempate->id) }}" role="button" title="Remover">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

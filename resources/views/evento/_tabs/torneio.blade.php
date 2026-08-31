@php
    $can_edit_torneio = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );

    $can_view_stats = (
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id, [4, 5]) ||
        \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id, [7])
    );

    $torneios_agrupados = $evento->torneios()
        ->with(['categorias.categoria', 'tipo_torneio', 'software', 'template'])
        ->orderBy('tipo_torneio_id', 'ASC')
        ->orderBy('softwares_id', 'ASC')
        ->orderBy('name', 'ASC')
        ->get()
        ->groupBy(function ($torneio) {
            return $torneio->tipo_torneio_id . '-' . $torneio->softwares_id;
        });
@endphp

<div class="v2-torneio-tab">
    <div class="box box-primary v2-torneio-list-box">
        <div class="box-header">
            <h3 class="box-title">Torneios</h3>
            @if($can_edit_torneio)
                <div class="pull-right box-tools">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalNovoTorneio">
                        <i class="fa fa-plus"></i> Novo Torneio
                    </button>
                </div>
            @endif
        </div>
        <div class="box-body">
            @if($torneios_agrupados->isEmpty())
                <p class="text-muted v2-torneio-empty">Nenhum torneio cadastrado.</p>
            @else
                @foreach($torneios_agrupados as $grupo)
                    @php $grupo_ref = $grupo->first(); @endphp
                    <section class="v2-torneio-group">
                        <h4 class="v2-torneio-group__title">
                            {{ $grupo_ref->tipo_torneio->name }}
                            <span class="text-muted">·</span>
                            {{ $grupo_ref->software->name }}
                        </h4>

                        <div class="v2-torneio-cards">
                            @foreach($grupo as $torneio)
                                @php
                                    $show_extra_acoes = (
                                        ($can_edit_torneio && $torneio->isDeletavel()) ||
                                        (\Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() && $evento->torneios()->count() > 1) ||
                                        $evento->is_lichess_integration ||
                                        $torneio->software->isChessCom() ||
                                        $evento->grupo_evento->hasConfig('is_pr_esporte', true)
                                    );
                                @endphp
                                <article class="v2-torneio-card">
                                    <header class="v2-torneio-card__header">
                                        <div class="v2-torneio-card__title-wrap">
                                            <span class="label label-default">#{{ $torneio->id }}</span>
                                            <h5 class="v2-torneio-card__title">{{ $torneio->name }}</h5>
                                        </div>
                                        <div class="v2-torneio-card__badges">
                                            <span class="label label-{{ $torneio->getIsResultadosImportados() === 'Sim' ? 'success' : 'default' }}">
                                                Resultados: {{ $torneio->getIsResultadosImportados() }}
                                            </span>
                                            @if($torneio->template)
                                                <span class="label label-info">{{ $torneio->template->name }}</span>
                                            @endif
                                        </div>
                                    </header>

                                    <div class="v2-torneio-card__body">
                                        <div class="v2-torneio-card__section">
                                            <span class="v2-torneio-card__section-label">Categorias</span>
                                            @if($torneio->categorias->isEmpty())
                                                <span class="text-muted">—</span>
                                            @else
                                                <div class="v2-torneio-card__tags">
                                                    @foreach($torneio->categorias as $categoria)
                                                        <span class="label label-primary">{{ $categoria->categoria->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <div class="v2-torneio-card__section">
                                            @include('components.v2.torneio-inscricoes-summary', [
                                                'evento' => $evento,
                                                'torneio' => $torneio,
                                                'showDetails' => $can_view_stats,
                                            ])
                                        </div>

                                        <div class="v2-torneio-card__section v2-torneio-card__section--acoes">
                                            @include('evento._tabs.partials.torneio_acoes_principais', compact('evento', 'torneio'))
                                        </div>
                                    </div>

                                    @if($show_extra_acoes)
                                        <footer class="v2-torneio-card__actions v2-torneio-card__actions--extras">
                                            @include('evento._tabs.partials.torneio_acoes_extras', compact('evento', 'torneio'))
                                        </footer>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @endif
        </div>
    </div>

    @if($can_edit_torneio)
        <div class="modal fade" id="modalNovoTorneio" tabindex="-1" role="dialog" aria-labelledby="modalNovoTorneioLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="post" action="{{ url('/evento/' . $evento->id . '/torneios/new') }}" id="formNovoTorneio">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <h4 class="modal-title" id="modalNovoTorneioLabel">Novo Torneio</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="novo_torneio_name">Nome</label>
                                <input name="name" id="novo_torneio_name" class="form-control" type="text" required />
                            </div>
                            <div class="form-group">
                                <label for="novo_torneio_tipo_torneio_id">Tipo de Torneio</label>
                                <select id="novo_torneio_tipo_torneio_id" name="tipo_torneio_id" class="form-control width-100">
                                    <option value="">-- Selecione --</option>
                                    @foreach(\App\TipoTorneio::all() as $tipo_torneio)
                                        <option value="{{ $tipo_torneio->id }}">{{ $tipo_torneio->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="novo_torneio_softwares_id">Software</label>
                                <select id="novo_torneio_softwares_id" name="softwares_id" class="form-control width-100">
                                    <option value="">-- Selecione --</option>
                                    @foreach(\App\Software::all() as $software)
                                        <option value="{{ $software->id }}">{{ $software->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-success">Enviar</button>
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

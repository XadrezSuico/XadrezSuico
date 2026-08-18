@php
    $totals = $dashboard_stats['totals'];
    $payment = $dashboard_stats['payment'];
    $bigger_tournament = $dashboard_stats['bigger_tournament'];
    $upcoming_timeline = $evento->getUpcomingTimelineItems(3);
    $inscricoes_list_url = url('/evento/' . $evento->id . '/inscricoes/list');

    $resume_pct = function ($parte, $total) {
        if ($total <= 0) {
            return 0;
        }
        return min(100, (int) round(100 * $parte / $total));
    };
@endphp

<br/>

<div class="callout callout-info" style="margin-bottom: 20px;">
    <h4 style="margin-top: 0;"><i class="fa fa-bar-chart"></i> Sobre esta aba</h4>
    <p style="margin-bottom: 0;">
        Visão analítica do evento: funil de inscrições, breakdown por torneio e pagamentos.
        A faixa acima das abas resume o essencial; aqui você encontra o detalhamento completo.
    </p>
</div>

@include('evento._partials.dashboard_alerts', ['compact' => false, 'alert_collapse_id' => 'resume'])

<div class="row">
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <a href="{{ $inscricoes_list_url }}" class="small-box-link">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $totals['inscritos'] }}</h3>
                    <p>Inscritos</p>
                </div>
                <div class="icon"><i class="fa fa-users"></i></div>
                <span class="small-box-footer">Ver lista <i class="fa fa-arrow-circle-right"></i></span>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <a href="{{ $inscricoes_list_url }}" class="small-box-link">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $totals['confirmados'] }}</h3>
                    <p>Confirmados</p>
                </div>
                <div class="icon"><i class="fa fa-check"></i></div>
                <span class="small-box-footer">Ver lista <i class="fa fa-arrow-circle-right"></i></span>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <a href="{{ $inscricoes_list_url }}" class="small-box-link">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $totals['presentes'] }}</h3>
                    <p>Presentes</p>
                </div>
                <div class="icon"><i class="fa fa-check-circle"></i></div>
                <span class="small-box-footer">Ver lista <i class="fa fa-arrow-circle-right"></i></span>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <a href="{{ $inscricoes_list_url }}" class="small-box-link">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ $totals['resultados'] }}</h3>
                    <p>Com resultados</p>
                </div>
                <div class="icon"><i class="fa fa-bar-chart"></i></div>
                <span class="small-box-footer">Ver lista <i class="fa fa-arrow-circle-right"></i></span>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <section class="col-lg-12 connectedSortable">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-bolt"></i> Atalhos rápidos</h3>
            </div>
            <div class="box-body">
                @if(
                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4,5]) ||
                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                )
                    @if($evento->layout_version == 2)
                        <a href="{{ url('/inscricao/' . $evento->id) }}" class="btn btn-bg-green btn-app">
                            <i class="fa fa-plus"></i> Nova Inscrição
                        </a>
                        <a href="{{ $evento->getEventPublicLink() }}" class="btn btn-success btn-app">
                            <i class="fa fa-link"></i> Link Público
                        </a>
                    @else
                        <a href="{{ url('/inscricao/' . $evento->id . ($evento->e_inscricao_apenas_com_link ? '?token=' . $evento->token : '')) }}" class="btn btn-bg-green btn-app">
                            <i class="fa fa-plus"></i> Nova Inscrição
                        </a>
                        <a href="{{ url('/inscricao/' . $evento->id . ($evento->e_inscricao_apenas_com_link ? '?token=' . $evento->token : '')) }}" class="btn btn-success btn-app">
                            <i class="fa fa-link"></i> Link Público
                        </a>
                    @endif
                @endif

                <a href="{{ $inscricoes_list_url }}" class="btn btn-app">
                    <i class="fa fa-list"></i> Lista de Inscritos
                </a>

                @if(
                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGlobal() ||
                    \Illuminate\Support\Facades\Auth::user()->hasPermissionEventByPerfil($evento->id,[4]) ||
                    \Illuminate\Support\Facades\Auth::user()->hasPermissionGroupEventByPerfil($evento->grupo_evento->id,[7])
                )
                    <a href="/evento/classificar/{{ $evento->id }}" class="btn btn-success btn-app">
                        <i class="fa fa-sort"></i> Classificar Evento
                    </a>
                @endif

                <a href="{{ url('/evento/' . $evento->id . '/exports/emparceirador') }}" class="btn btn-app">
                    <i class="fa fa-download"></i> Emparceirador
                </a>
            </div>
        </div>
    </section>
</div>

<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Funil de inscrições</h3>
            </div>
            <div class="box-body">
                @if($totals['inscritos'] === 0)
                    <p class="text-muted" style="margin: 0;">Nenhum inscrito registrado neste evento.</p>
                @else
                    <div class="progress-group">
                        <span class="progress-text">Confirmados sobre inscritos</span>
                        <span class="progress-number"><b>{{ $totals['confirmados'] }}</b>/{{ $totals['inscritos'] }} ({{ $resume_pct($totals['confirmados'], $totals['inscritos']) }}%)</span>
                        <div class="progress sm">
                            <div class="progress-bar progress-bar-green" style="width: {{ $resume_pct($totals['confirmados'], $totals['inscritos']) }}%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Presentes sobre inscritos</span>
                        <span class="progress-number"><b>{{ $totals['presentes'] }}</b>/{{ $totals['inscritos'] }} ({{ $resume_pct($totals['presentes'], $totals['inscritos']) }}%)</span>
                        <div class="progress sm">
                            <div class="progress-bar progress-bar-yellow" style="width: {{ $resume_pct($totals['presentes'], $totals['inscritos']) }}%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Com resultados sobre inscritos</span>
                        <span class="progress-number"><b>{{ $totals['resultados'] }}</b>/{{ $totals['inscritos'] }} ({{ $resume_pct($totals['resultados'], $totals['inscritos']) }}%)</span>
                        <div class="progress sm">
                            <div class="progress-bar progress-bar-aqua" style="width: {{ $resume_pct($totals['resultados'], $totals['inscritos']) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-trophy"></i> Por torneio</h3>
            </div>
            <div class="box-body table-responsive no-padding">
                @if(count($dashboard_stats['torneios']) === 0)
                    <p class="text-muted" style="padding: 10px 15px; margin: 0;">Nenhum torneio cadastrado.</p>
                @else
                    <table class="table table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Torneio</th>
                                <th class="text-right">Inscritos</th>
                                <th class="text-right">Confirmados</th>
                                <th class="text-right">Presentes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dashboard_stats['torneios'] as $torneio_stat)
                                <tr @if($bigger_tournament['status'] && $bigger_tournament['tournament']->id === $torneio_stat['id']) class="active" @endif>
                                    <td>
                                        {{ $torneio_stat['name'] }}
                                        @if($bigger_tournament['status'] && $bigger_tournament['tournament']->id === $torneio_stat['id'])
                                            <span class="label label-primary">Maior</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $torneio_stat['inscritos'] }}</td>
                                    <td class="text-right">{{ $torneio_stat['confirmados'] }}</td>
                                    <td class="text-right">{{ $torneio_stat['presentes'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </section>

    <section class="col-lg-4 connectedSortable">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-sliders"></i> Status e configuração</h3>
            </div>
            <div class="box-body">
                <p style="margin-top: 0;">
                    <span class="label label-{{ $evento->is_inscricoes_bloqueadas ? 'danger' : 'success' }}">
                        Inscrições {{ $evento->is_inscricoes_bloqueadas ? 'bloqueadas' : 'permitidas' }}
                    </span>
                    <span class="label label-{{ $evento->permite_edicao_inscricao ? 'success' : 'default' }}">
                        Edição {{ $evento->permite_edicao_inscricao ? 'permitida' : 'bloqueada' }}
                    </span>
                </p>
                <p>
                    <span class="label label-{{ $evento->mostrar_resultados ? 'success' : 'default' }}">
                        Resultados públicos {{ $evento->mostrar_resultados ? 'ativos' : 'inativos' }}
                    </span>
                    <span class="label label-{{ $evento->classificavel ? 'success' : 'default' }}">
                        Classificação geral {{ $evento->classificavel ? 'habilitada' : 'desabilitada' }}
                    </span>
                </p>
                <p style="margin-bottom: 0;">
                    @if($evento->tipo_rating)
                        <span class="label label-{{ $evento->is_rating_calculate_enabled ? 'success' : 'default' }}">
                            Rating interno {{ $evento->is_rating_calculate_enabled ? 'habilitado' : 'desabilitado' }}
                        </span>
                    @else
                        <span class="label label-default">Sem rating interno</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-random"></i> Torneio e presença</h3>
            </div>
            <div class="box-body">
                <dl style="margin-bottom: 0;">
                    <dt>Maior torneio (por inscrições)</dt>
                    <dd style="margin-bottom: 12px;">
                        @if($bigger_tournament['status'])
                            <strong>{{ $bigger_tournament['name'] }}</strong>
                            <span class="label label-primary">{{ $bigger_tournament['total'] }} inscrito(s)</span>
                        @else
                            <span class="text-muted">{{ $bigger_tournament['tournament'] }}</span>
                        @endif
                    </dd>
                    <dt>Emparceiramentos importados</dt>
                    <dd style="margin-bottom: 12px;"><strong>{{ $totals['emparceiramentos'] }}</strong></dd>
                    <dt>Clubes / equipes presentes</dt>
                    <dd style="margin-bottom: 0;"><strong>{{ $totals['clubes'] }}</strong></dd>
                </dl>
            </div>
        </div>

        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-clock-o"></i> Próximos marcos</h3>
            </div>
            <div class="box-body">
                @if(count($upcoming_timeline) === 0)
                    <p class="text-muted" style="margin: 0;">Nenhum marco futuro cadastrado.</p>
                @else
                    <ul class="list-unstyled" style="margin-bottom: 10px;">
                        @foreach($upcoming_timeline as $item)
                            <li style="margin-bottom: 8px;">
                                <strong>{{ $item['datetime'] }}</strong>
                                @if($item['is_expected'])
                                    <span class="label label-warning">Previsão</span>
                                @endif
                                <br/>
                                <span class="text-muted">{{ $item['text'] }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <a href="{{ url('/evento/dashboard/' . $evento->id) }}?tab=timeline">Gerenciar timeline &rarr;</a>
            </div>
        </div>
    </section>
</div>

@if ($evento->isPaid())
    <div class="row">
        <section class="col-lg-12 connectedSortable">
            <div class="box box-success">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-money"></i> Pagamentos</h3>
                </div>
                <div class="box-body table-responsive no-padding">
                    <table class="table table-striped table-hover" style="margin-bottom: 0;">
                        <thead>
                            <tr>
                                <th>Situação</th>
                                <th class="text-right">Total</th>
                                <th class="text-right">Confirmados</th>
                                <th class="text-right">Presentes</th>
                                <th class="text-right">Com resultados</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="label label-success">Pagos</span></td>
                                <td class="text-right">{{ $payment['paid']['total'] }}</td>
                                <td class="text-right">{{ $payment['paid']['confirmados'] }}</td>
                                <td class="text-right">{{ $payment['paid']['presentes'] }}</td>
                                <td class="text-right">{{ $payment['paid']['resultados'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="label label-info">Gratuidade</span></td>
                                <td class="text-right">{{ $payment['free']['total'] }}</td>
                                <td class="text-right">{{ $payment['free']['confirmados'] }}</td>
                                <td class="text-right">{{ $payment['free']['presentes'] }}</td>
                                <td class="text-right">{{ $payment['free']['resultados'] }}</td>
                            </tr>
                            <tr>
                                <td><span class="label label-warning">Não pagos</span></td>
                                <td class="text-right">{{ $payment['not_paid']['total'] }}</td>
                                <td class="text-right">{{ $payment['not_paid']['confirmados'] }}</td>
                                <td class="text-right">{{ $payment['not_paid']['presentes'] }}</td>
                                <td class="text-right">{{ $payment['not_paid']['resultados'] }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="active">
                                <th>Geral</th>
                                <th class="text-right">{{ $totals['inscritos'] }}</th>
                                <th class="text-right">{{ $totals['confirmados'] }}</th>
                                <th class="text-right">{{ $totals['presentes'] }}</th>
                                <th class="text-right">{{ $totals['resultados'] }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endif

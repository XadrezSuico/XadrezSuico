@php
    $total_inscritos = $evento->quantosInscritos();
    $total_confirmados = $evento->quantosInscritosConfirmados();
    $total_presentes = $evento->quantosInscritosPresentes();
    $total_resultados = $evento->quantosInscritosComResultados();
    $total_emparceiramentos = $evento->quantosEmparceiramentos();
    $total_clubes = $evento->quantosClubes();
    $bigger_tournament = $evento->getTournamentWithMoreRegistrations();

    $resume_pct = function ($parte, $total) {
        if ($total <= 0) {
            return 0;
        }
        return min(100, (int) round(100 * $parte / $total));
    };
@endphp

<br/>

<div class="row">
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ $total_inscritos }}</h3>
                <p>Inscritos</p>
            </div>
            <div class="icon"><i class="fa fa-users"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ $total_confirmados }}</h3>
                <p>Confirmados</p>
            </div>
            <div class="icon"><i class="fa fa-check"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>{{ $total_presentes }}</h3>
                <p>Presentes</p>
            </div>
            <div class="icon"><i class="fa fa-check-circle"></i></div>
        </div>
    </div>
    <div class="col-lg-3 col-sm-6 col-xs-12">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>{{ $total_resultados }}</h3>
                <p>Com resultados</p>
            </div>
            <div class="icon"><i class="fa fa-bar-chart"></i></div>
        </div>
    </div>
</div>

<div class="row">
    <section class="col-lg-8 connectedSortable">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Funil de inscrições</h3>
            </div>
            <div class="box-body">
                @if($total_inscritos === 0)
                    <p class="text-muted" style="margin: 0;">Nenhum inscrito registrado neste evento.</p>
                @else
                    <div class="progress-group">
                        <span class="progress-text">Confirmados sobre inscritos</span>
                        <span class="progress-number"><b>{{ $total_confirmados }}</b>/{{ $total_inscritos }} ({{ $resume_pct($total_confirmados, $total_inscritos) }}%)</span>
                        <div class="progress sm">
                            <div class="progress-bar progress-bar-green" style="width: {{ $resume_pct($total_confirmados, $total_inscritos) }}%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Presentes sobre inscritos</span>
                        <span class="progress-number"><b>{{ $total_presentes }}</b>/{{ $total_inscritos }} ({{ $resume_pct($total_presentes, $total_inscritos) }}%)</span>
                        <div class="progress sm">
                            <div class="progress-bar progress-bar-yellow" style="width: {{ $resume_pct($total_presentes, $total_inscritos) }}%"></div>
                        </div>
                    </div>
                    <div class="progress-group">
                        <span class="progress-text">Com resultados sobre inscritos</span>
                        <span class="progress-number"><b>{{ $total_resultados }}</b>/{{ $total_inscritos }} ({{ $resume_pct($total_resultados, $total_inscritos) }}%)</span>
                        <div class="progress sm">
                            <div class="progress-bar progress-bar-aqua" style="width: {{ $resume_pct($total_resultados, $total_inscritos) }}%"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="col-lg-4 connectedSortable">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-random"></i> Torneio e presença</h3>
            </div>
            <div class="box-body">
                <dl style="margin-bottom: 0;">
                    <dt>Maior torneio (por inscrições)</dt>
                    <dd style="margin-bottom: 12px;">
                        @if($bigger_tournament['status'])
                            <strong>{{ $bigger_tournament['tournament']->name }}</strong>
                            <span class="label label-primary">{{ $bigger_tournament['total'] }} inscrito(s)</span>
                        @else
                            <span class="text-muted">{{ $bigger_tournament['tournament'] }}</span>
                        @endif
                    </dd>
                    <dt>Emparceiramentos importados</dt>
                    <dd style="margin-bottom: 12px;"><strong>{{ $total_emparceiramentos }}</strong></dd>
                    <dt>Clubes / equipes presentes</dt>
                    <dd style="margin-bottom: 0;"><strong>{{ $total_clubes }}</strong></dd>
                </dl>
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
                                <td class="text-right">{{ $evento->howManyPaid() }}</td>
                                <td class="text-right">{{ $evento->howManyConfirmedPaid() }}</td>
                                <td class="text-right">{{ $evento->howManyPresentPaid() }}</td>
                                <td class="text-right">{{ $evento->howManyWithResultsPaid() }}</td>
                            </tr>
                            <tr>
                                <td><span class="label label-info">Gratuidade</span></td>
                                <td class="text-right">{{ $evento->howManyFree() }}</td>
                                <td class="text-right">{{ $evento->howManyConfirmedFree() }}</td>
                                <td class="text-right">{{ $evento->howManyPresentFree() }}</td>
                                <td class="text-right">{{ $evento->howManyWithResultsFree() }}</td>
                            </tr>
                            <tr>
                                <td><span class="label label-warning">Não pagos</span></td>
                                <td class="text-right">{{ $evento->howManyNotPaid() }}</td>
                                <td class="text-right">{{ $evento->howManyConfirmedNotPaid() }}</td>
                                <td class="text-right">{{ $evento->howManyPresentNotPaid() }}</td>
                                <td class="text-right">{{ $evento->howManyWithResultsNotPaid() }}</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="active">
                                <th>Geral</th>
                                <th class="text-right">{{ $total_inscritos }}</th>
                                <th class="text-right">{{ $total_confirmados }}</th>
                                <th class="text-right">{{ $total_presentes }}</th>
                                <th class="text-right">{{ $total_resultados }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </section>
    </div>
@endif

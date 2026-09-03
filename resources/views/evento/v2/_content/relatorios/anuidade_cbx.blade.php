<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title evento">Evento: {{ $evento->name }}</h3>
        </div>
    </div>

    @if(!$elegivel)
        <div class="box box-warning">
            <div class="box-body">
                <p>Este relatório só está disponível para eventos convencionais com cálculo de rating CBX e/ou FIDE configurado.</p>
            </div>
        </div>
    @else
        <div class="alert alert-warning" id="processo_aguarde">
            <h4><strong>Aguarde... consultando anuidades CBX...</strong></h4>
            A consulta é feita jogador a jogador no site da CBX e pode demorar alguns minutos.
        </div>
        <div class="alert alert-success" id="processo_sucesso" style="display:none">
            <h4><strong>Consulta concluída!</strong></h4>
            Todas as anuidades CBX foram verificadas com sucesso.
        </div>
        <div class="alert alert-danger" id="processo_erro" style="display:none">
            <h4><strong>Consulta concluída com avisos</strong></h4>
            Algumas consultas falharam. Verifique os registros marcados com <i class="fa fa-times"></i> ou status "Erro".
        </div>

        <div class="box box-default">
            <div class="box-header">
                <h3 class="box-title">Legenda</h3>
            </div>
            <div class="box-body">
                <span class="anuidade-badge anuidade-pago">Pago</span>
                <span class="anuidade-badge anuidade-pendente">Pendente</span>
                <span class="anuidade-badge anuidade-sem-id">Sem ID CBX</span>
                <span class="anuidade-badge anuidade-erro">Erro</span>
                <p style="margin-top: 10px; margin-bottom: 0;">
                    Consulta a coluna "Data Pagto." no site da CBX para cada jogador inscrito com ID CBX.
                    Anuidade considerada paga quando há data de pagamento do ano atual.
                </p>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Inscrições</h3>
            </div>
            <div class="box-body">
                <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                    <thead>
                        <tr>
                            <th class="display-none">Ordem</th>
                            <th>ID Enxadrista</th>
                            <th>Nome do Enxadrista</th>
                            <th>Cidade</th>
                            <th>Clube</th>
                            <th>ID CBX</th>
                            <th>Data Pagto. (CBX)</th>
                            <th>Status</th>
                            <th>Consulta</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($linhas as $linha)
                            @php
                                $classeBadge = 'anuidade-aguardando';
                                if ($linha['status'] === 'sem_id') {
                                    $classeBadge = 'anuidade-sem-id';
                                } elseif ($linha['status'] === 'pago') {
                                    $classeBadge = 'anuidade-pago';
                                }
                            @endphp
                            <tr id="linha_{{ $linha['enxadrista_id'] }}" data-status-ordenacao="{{ $linha['status_ordenacao'] }}">
                                <td class="display-none">{{ $linha['status_ordenacao'] }}</td>
                                <td>{{ $linha['enxadrista_id'] }}</td>
                                <td>{{ $linha['nome'] }}</td>
                                <td>{{ $linha['cidade'] }}</td>
                                <td>{{ $linha['clube'] }}</td>
                                <td>{{ $linha['cbx_id'] }}</td>
                                <td class="col-data-pagto">{{ $linha['data_pagto'] }}</td>
                                <td class="col-status">
                                    <span class="anuidade-badge {{ $classeBadge }}" title="{{ $linha['detalhe'] }}">
                                        {{ $linha['label'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($linha['tem_id_cbx'])
                                        @if($linha['requer_consulta'])
                                            <i id="enxadrista_{{ $linha['enxadrista_id'] }}_icon" style="display:none;" class="fa fa-spinner anuidade-icon"></i>
                                        @else
                                            <i class="fa fa-check anuidade-icon" title="Resultado em cache"></i>
                                        @endif
                                    @else
                                        <i class="fa fa-minus anuidade-icon" title="Sem ID CBX"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
  </section>
</div>

<div class="row">
  <section class="col-lg-12 connectedSortable">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title evento">Evento: {{ $evento->name }}</h3>
        </div>
    </div>

    <div class="box box-default">
        <div class="box-header">
            <h3 class="box-title">Legenda</h3>
        </div>
        <div class="box-body">
            <p style="margin-bottom: 0;">
                Resumo de inscrições agrupadas por categoria.
                <strong>Confirmados</strong> são inscrições marcadas como confirmadas.
                <strong>Presentes</strong> são confirmados que não estão marcados como WO (ausência).
                @if($com_pagamento)
                    Este evento está vinculado à plataforma de pagamento: <strong>Pagos</strong> e <strong>Pagamento pendente</strong> consideram apenas categorias com cobrança configurada.
                @else
                    Este evento não está vinculado à plataforma de pagamento; colunas de pagamento não são exibidas.
                @endif
            </p>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Participação por Categoria</h3>
        </div>
        <div class="box-body">
            <table id="tabela" class="table-responsive table-condensed table-striped" style="width: 100%">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th>Inscritos</th>
                        @if($com_pagamento)
                            <th>Pagos</th>
                            <th>Pagamento pendente</th>
                        @endif
                        <th>Confirmados</th>
                        <th>Presentes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($linhas as $linha)
                        <tr>
                            <td>{{ $linha['categoria'] }}</td>
                            <td>{{ $linha['inscritos'] }}</td>
                            @if($com_pagamento)
                                <td>
                                    @if($linha['categoria_paga'])
                                        {{ $linha['pagos'] }}
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($linha['categoria_paga'])
                                        {{ $linha['pendentes'] }}
                                    @else
                                        —
                                    @endif
                                </td>
                            @endif
                            <td>{{ $linha['confirmados'] }}</td>
                            <td>{{ $linha['presentes'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $com_pagamento ? 6 : 4 }}">Nenhuma categoria encontrada para este evento.</td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($linhas) > 0)
                    <tfoot>
                        <tr>
                            <td>Total</td>
                            <td>{{ $totais['inscritos'] }}</td>
                            @if($com_pagamento)
                                <td>{{ $totais['pagos'] }}</td>
                                <td>{{ $totais['pendentes'] }}</td>
                            @endif
                            <td>{{ $totais['confirmados'] }}</td>
                            <td>{{ $totais['presentes'] }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
  </section>
</div>
